<?php

require_once __DIR__ . '/../../../../system/library/autoload.php';

use Yedpay\Client;
use Yedpay\Response\Error;
use Yedpay\Response\Success;

class ControllerExtensionPaymentYedpay extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/yedpay');
        $total_requirement = $this->config->get('payment_yedpay_total') == null ? 1 : $this->config->get('payment_yedpay_total');
        if ($this->cart->countProducts() < $total_requirement) {
            die('You must spend at least $' . $total_requirement . ' to use Yedpay');
        }
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['payment_description'] = $this->language->get('payment_description');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $api_key = $this->config->get('payment_yedpay_token');

        $currency = strtoupper($order_info['currency_code']);
        if ($currency != 'HKD') {
            $this->log->write('YedPay error:  ' . $currency . ' not supported!');
            die('Yedpay supports Hong Kong Dollars only!');
        }

        $out_trade_no = trim($order_info['order_id']);
        $total_amount = trim($this->currency->format($order_info['total'], $currency, '', false));

        $environment = $this->config->get('payment_yedpay_test') == 'sandbox' ?
            'staging' :
            'production';

        $custom_id = $out_trade_no;
        $custom_id_prefix = $this->config->get('payment_yedpay_custom_id_prefix');
        if (!empty($custom_id_prefix)) {
            $custom_id = $custom_id_prefix . '-' . $out_trade_no;
        }

        // create data request
        try {
            $client = new Client($environment, $api_key, false);
            $currency = Client::INDEX_CURRENCY_HKD;

            $client->setCurrency($currency)
                ->setNotifyUrl($this->url->link('extension/payment/yedpay/expressNotify', '', true))
                ->setReturnUrl($this->url->link('extension/payment/yedpay/expressReturn', '', true))
                ->setSubject('Order #' . $custom_id)
                ->setMetadata(json_encode([
                    'opencart' => VERSION,
                    'yedpay_for_opencart' => '1.1.2',
                ]));

            $support_gateway = $this->config->get('payment_yedpay_support_gateway');
            $support_wallet = $this->config->get('payment_yedpay_support_wallet');
            $expiry_time = $this->config->get('payment_yedpay_expiry_time');
            if ($support_gateway != '0') {
                $client->setGatewayCode($support_gateway);
            }
            if ($support_gateway == '4_2' && $support_wallet != '0') {
                $client->setWallet($this->getWallet($support_wallet));
            }
            if (
                is_numeric($expiry_time) &&
                filter_var($expiry_time, FILTER_VALIDATE_INT) &&
                $expiry_time >= '900' &&
                $expiry_time <= '10800'
            ) {
                $client->setExpiryTime($expiry_time);
            }

            $billing_country = strtoupper(trim($order_info['payment_iso_code_2']));
            $billing_address = [
                'first_name' => trim($order_info['payment_firstname']),
                'last_name'  => trim($order_info['payment_lastname']),
                'email' => trim($order_info['email']),
                'phone' => $order_info['telephone'],
                'billing_country' => $billing_country,
                'billing_post_code' => trim($order_info['payment_postcode']),
                'billing_city' => trim($order_info['payment_city']),
                'billing_address1' => trim($order_info['payment_address_1']),
                'billing_address2' => trim($order_info['payment_address_2']),
            ];

            if ($billing_country == 'US' || $billing_country == 'CA') {
                $billing_address['billing_state'] = trim($order_info['payment_zone_code']);
            }
            $client->setPaymentData(json_encode($billing_address));

            $checkout_domain_id = $this->config->get('payment_yedpay_checkout_domain_id');
            if (!empty($checkout_domain_id)) {
                $client->setCheckoutDomainId($checkout_domain_id);
            }

            $online_payment = $client->onlinePayment($custom_id, $total_amount);
        } catch (Exception $e) {
            $this->log->write($e);
            die('An error has occurred. Please try again later or contact the store owner' . ' ' . $e);
        }

        $payment_url = '';

        if ($online_payment instanceof Error) {
            $this->log->write('YedPay error:  ' . strval($online_payment->getErrorCode()) . ' - ' . $online_payment->getMessage());
            die('An error has occurred. Please try again later or contact the store owner.');
        } elseif ($online_payment instanceof Success) {
            $payment_data = json_decode(json_encode($online_payment->getData()), true);
            $payment_url = $payment_data['checkout_url'];
        }

        $data['action'] = $payment_url;

        return $this->load->view('extension/payment/yedpay', $data);
    }

    public function callback()
    {
        error_log('Yedpay notified');

        try {
            $is_payment_success = $this->checkPaymentSuccessByReceivedData($_POST);
        } catch (Exception $e) {
            die('Error verifying signature');
        }

        if ($is_payment_success) {
            echo ('success'); //Do not modified or delete
        } else {
            $this->log->write('Payment failed');
            //check failed
            echo 'fail';
        }
        return $is_payment_success;
    }

    public function expressReturn()
    {
        $this->log->write('Returned from successful payment');
        $this->log->write('GET' . var_export($_GET, true));

        try {
            $is_payment_success = $this->checkPaymentSuccessByReceivedData($_GET, false);
        } catch (Exception $e) {
            $this->session->data['error'] = $e->getMessage();
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

        if ($is_payment_success) {
            return $this->response->redirect($this->url->link('checkout/success'));
        } else {
            $this->session->data['error'] = 'Payment Failed.';
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }

    public function expressNotify()
    {
        $this->log->write('Notification received');
        $this->log->write('POST' . var_export($_POST, true));
        if (!$this->callback()) {
            $this->log->write('Failed notification');
        }
        die;
    }

    /**
     * Returns Wallet Index
     *
     * @param string $wallet
     * @return int|void
     */
    public function getWallet($wallet)
    {
        if ($wallet == Client::HK_WALLET) {
            return Client::INDEX_WALLET_HK;
        } elseif ($wallet == Client::CN_WALLET) {
            return Client::INDEX_WALLET_CN;
        }
        return null;
    }

    /**
     * Handle received data from Yedpay and update order history
     *
     * @param array $received_data
     * @param bool $is_notification
     * @return bool
     */
    public function checkPaymentSuccessByReceivedData($received_data, $is_notification = true)
    {
        if ($is_notification) {
            $arr = $received_data['transaction'];
            $unset_fields = [];
            $message_title = 'notification';
        } else {
            $arr = $received_data;
            $unset_fields = ['route'];
            $message_title = 'redirect parameters';
        }

        $order_id = $this->getOrderIdByYedpay($arr);
        $transaction_result = $this->getTransactionResultByYedpay($arr);

        $environment = $this->config->get('payment_yedpay_test') == 'sandbox' ?
            'staging' :
            'production';
        $api_key = $this->config->get('payment_yedpay_token');

        try {
            $sign_client = new Client($environment, $api_key);
            $sign_result = $sign_client->verifySign($received_data, $this->config->get('payment_yedpay_sign_key'), $unset_fields);
        } catch (Exception $e) {
            throw new Exception('Error verifying signature');
        }

        $this->log->write('Yedpay ' . $message_title . ' transaction result check: ' . $transaction_result);
        $this->log->write('Yedpay ' . $message_title . ' sign check: ' . $sign_result);

        if ($transaction_result && $sign_result) { // check successful
            $this->log->write('Yedpay payment successful by ' . $message_title);

            $this->load->model('checkout/order');
            // Ensure that double reception of order notification is handled
            $current_order_status_id = ($this->model_checkout_order->getOrder($order_id))['order_status_id'];

            if ($current_order_status_id == 0) {
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_yedpay_order_status_id'));
            }

            return true;
        }
        return false;
    }

    /**
     * Get Order Id by Yedpay notification / return parameters
     *
     * @param array $arr
     * @return string
     */
    public function getOrderIdByYedpay($arr)
    {
        if (isset($arr['custom_id'])) {
            $custom_id_prefix = $this->config->get('payment_yedpay_custom_id_prefix');
            if (!empty($custom_id_prefix) && strpos($arr['custom_id'], $custom_id_prefix . '-') !== false) {
                return substr($arr['custom_id'], strlen($custom_id_prefix . '-'));
            } elseif (strpos($arr['custom_id'], '-') !== false) {
                return explode('-', $arr['custom_id'])[1];
            }
            return $arr['custom_id'];
        }
        return '';
    }

    /**
     * Get Transaction result by Yedpay notification / return parameters
     *
     * @param array $arr
     * @return bool
     */
    public function getTransactionResultByYedpay($arr)
    {
        if (isset($arr['status']) && $arr['status'] == 'paid') {
            return true;
        }
        return false;
    }
}
