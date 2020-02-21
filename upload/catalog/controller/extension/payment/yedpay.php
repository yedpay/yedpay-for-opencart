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
        $apikey = $this->config->get('payment_yedpay_token');
        $config = [
            'token' => $apikey,
            'return_url' => $this->url->link('extension/payment/yedpay/expressReturn', '', true),
        ];

        $currency = strtoupper($order_info['currency_code']);
        if ($currency != 'HKD') {
            $this->log->write('YedPay error:  ' . $currency . ' not supported!');
            die('Yedpay supports Hong Kong Dollars only!');
        }

        $out_trade_no = trim($order_info['order_id']);
        $subject = trim($this->config->get('config_name'));
        $total_amount = trim($this->currency->format($order_info['total'], $currency, '', false));
        $body = ''; //trim($_POST['WIDbody']);

        $environment = $this->config->get('payment_yedpay_test') == 'sandbox' ?
            'staging' :
            'production';

        // create data request
        try {
            $client = new Client($environment, $apikey, false);
            $currency = Client::INDEX_CURRENCY_HKD;

            $client->setCurrency($currency)
                ->setNotifyUrl($this->url->link('extension/payment/yedpay/expressNotify', '', true))
                ->setReturnUrl($this->url->link('extension/payment/yedpay/expressReturn', '', true));
            $onlinePayment = $client->onlinePayment($out_trade_no, $total_amount);
        } catch (Exception $e) {
            $this->log->write($e);
            die('An error has occurred. Please try again later or contact the store owner' . ' ' . $e);
        }

        $payment_url = '';

        if ($onlinePayment instanceof Error) {
            $this->log->write('YedPay error:  ' . strval($onlinePayment->getErrorCode()) . ' - ' . $onlinePayment->getMessage());
            die('An error has occurred. Please try again later or contact the store owner.');
        } elseif ($onlinePayment instanceof Success) {
            $paymentData = json_decode(json_encode($onlinePayment->getData()), true);
            $payment_url = $paymentData['checkout_url'];
        }

        $data['action'] = $payment_url; //$gateway_url . 'precreate/' . $store_id;//$config['gateway_url'] . "?charset=" . $this->model_extension_payment_yedpay->getPostCharset();

        return $this->load->view('extension/payment/yedpay', $data);
    }

    public function callback()
    {
        $result = false;
        $orderId = '';
        $arr = $_POST['transaction'];
        error_log('Yedpay notified');

        if (isset($arr['custom_id']) && isset($arr['status'])) {
            $orderId = $arr['custom_id'];
            if ($arr['status'] == 'paid') {
                $result = true;
            }
        }

        $environment = $this->config->get('payment_yedpay_test') == 'sandbox' ?
            'staging' :
            'production';
        $apikey = $this->config->get('payment_yedpay_token');
        $signResult = '';
        try {
            $signClient = new Client($environment, $apikey);
            $signResult = $signClient->verifySign($_POST, $this->config->get('payment_yedpay_sign_key'));
        } catch (Exception $e) {
            die('Error verifying signature');
        }
        $this->log->write('Result check: ' . $result);
        $this->log->write('Sign check: ' . $signResult);
        if ($result && $signResult) { //check successful
            $this->log->write('Yedpay successful');
            $this->load->model('checkout/order');
            // Ensure that double reception of order notification is handled
            $currentOrderStatusId = ($this->model_checkout_order->getOrder($orderId))['order_status_id'];

            if ($currentOrderStatusId == 0) {
                $this->model_checkout_order->addOrderHistory($orderId, $this->config->get('payment_yedpay_order_status_id'));
            }

            echo('success'); //Do not modified or delete
        } else {
            $this->log->write('Payment failed');
            //check failed
            echo 'fail';
        }
        return $result;
    }

    public function expressReturn()
    {
        $this->log->write('Returned from successful payment');
        if (isset($_GET['status']) && strtolower($_GET['status']) == 'paid') {
            $this->response->redirect($this->url->link('checkout/success'));
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
}
