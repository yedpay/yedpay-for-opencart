<?php
// Heading
$_['heading_title'] = 'Yedpay';

// Text
$_['text_extension'] = '插件';
$_['text_success'] = '成功: 你已修改設定!';
$_['text_edit'] = '修改Yedpay設定';
$_['text_yedpay'] = '<a target="_BLANK" href="https://www.yedpay.com"><img src="view/image/payment/yedpay.png" alt="Yedpay" title="Yedpay Website" style="border: 1px solid #EEEEEE;" height = "42" width = "140"/></a>';
$_['text_live'] = '生產模式';
$_['text_sandbox'] = '測試模式';
$_['text_all'] = '全部';
$_['text_wallet_hk'] = '香港錢包';
$_['text_wallet_cn'] = '內地錢包';
$_['text_gateway_alipay_online'] = '只有Alipay Online';
$_['text_gateway_wechatpay_online'] = '只有WeChat Pay Online';
$_['text_gateway_unionpay_express_pay'] = '只有UnionPay ExpressPay';
$_['text_gateway_unionpay_upop'] = '只有UnionPay UPOP';
$_['text_gateway_credit_card_online'] = '只有Visa/mastercard';

// Entry
$_['entry_token'] = 'API Key';
$_['entry_sign_key'] = 'Sign Key';

$_['entry_test'] = '生產/測試模式';
$_['entry_support_gateway'] = '支付渠道';
$_['entry_support_wallet'] = '支付渠道錢包';
$_['entry_expiry_time'] = '有效期限';
$_['entry_custom_id_prefix'] = '訂單編號前綴';
$_['entry_checkout_domain_id'] = '付款網域ID';

$_['entry_total'] = '最小交易金額';
$_['entry_order_status'] = '交易成功狀態';
$_['entry_geo_zone'] = '地理區域';
$_['entry_status'] = '狀態';
$_['entry_sort_order'] = '訂單排列';

// Help
$_['help_total'] = '啓用Yedpay作交易方式的最低訂單交易金額 (如不需最低訂單交易金額維持Yedpay啓用狀態請留空)';
$_['help_yedpay_setup'] = '<a target="_blank" href="http://www.yedpay.com">請按此</a> 了解怎樣設定Yedpay戶口。';
$_['help_test'] = 'Yedpay生產/測試模式。真實交易必須使用生產模式。請根據付款模式更新API Key以及Sign Key';
$_['help_support_gateway'] = '支援的支付渠道';
$_['help_support_wallet'] = '支援的支付渠道錢包 (適用於Alipay Online)';
$_['help_expiry_time'] = '網上交易有效期限 (900-10800秒)';
$_['help_custom_id_prefix'] = 'Yedpay訂單編號前綴, 如保留原有訂單編號請留空。(最大爲10個字, 只接受英文字母)';

// Error
$_['error_permission'] = '警告: 你並沒有權限修改設定!';
$_['error_sign_key'] = 'Sign Key必需填寫!';
$_['error_token'] = 'API Key必需填寫!';
$_['error_warning'] = '警告!';
$_['error_expiry_time'] = '網上交易有效期限必須介乎900-10800秒!';
$_['error_custom_id_prefix'] = '訂單編號前綴必須等於或少於10個英文字母!';
