<?php
class ControllerExtensionPaymentYedpay extends Controller
{
    private $error = array();
    private $paramKeys = [
        'payment_yedpay_token',
        'payment_yedpay_sign_key',
        'payment_yedpay_total',
        'payment_yedpay_order_status_id',
        'payment_yedpay_geo_zone_id',
        'payment_yedpay_test',
        'payment_yedpay_status',
        'payment_yedpay_sort_order',
    ];

    public function index()
    {
        $this->load->language('extension/payment/yedpay');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_yedpay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }
        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['error_token'] = $this->error['token'] ?? '';
        $data['error_sign_key'] = $this->error['sign_key'] ?? '';


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/yedpay', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/yedpay', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        $data = $this->setData($data);

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/yedpay', $data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/yedpay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_yedpay_token']) {
            $this->error['token'] = $this->language->get('error_token');
        }
        if (!$this->request->post['payment_yedpay_sign_key']) {
            $this->error['sign_key'] = $this->language->get('error_sign_key');
        }

        return !$this->error;
    }

    public function setData($data)
    {
        foreach ($this->paramKeys as $key) {
            $data[$key] = $this->request->post[$key] ?? $this->config->get($key);
        }

        return $data;
    }
}
