<?php
// Heading
$_['heading_title'] = 'Yedpay';

// Text
$_['text_extension'] = 'Extensions';
$_['text_success'] = 'Success: You have modified Yedpay account details!';
$_['text_edit'] = 'Edit Yedpay Settings';
$_['text_yedpay'] = '<a target="_BLANK" href="https://www.yedpay.com"><img src="view/image/payment/yedpay.png" alt="Yedpay" title="Yedpay Website" style="border: 1px solid #EEEEEE;" height = "42" width = "140"/></a>';
$_['text_live'] = 'Live';
$_['text_sandbox'] = 'Sandbox';
$_['text_all'] = 'All';
$_['text_wallet_hk'] = 'Hong Kong Wallet';
$_['text_wallet_cn'] = 'China Wallet';
$_['text_gateway_alipay_online'] = 'Alipay Online Only';
$_['text_gateway_wechatpay_online'] = 'WeChat Pay Online Only';
$_['text_gateway_unionpay_express_pay'] = 'UnionPay ExpressPay Only';
$_['text_gateway_unionpay_upop'] = 'UnionPay UPOP Only';
$_['text_gateway_credit_card_online'] = 'Visa/mastercard Only';

// Entry
$_['entry_token'] = 'API Key';
$_['entry_sign_key'] = 'Sign Key';

$_['entry_test'] = 'Production/Sandbox Mode';
$_['entry_support_gateway'] = 'Support Gateway';
$_['entry_support_wallet'] = 'Support Wallet';
$_['entry_expiry_time'] = 'Expiry Time';
$_['entry_custom_id_prefix'] = 'Order ID Prefix';

$_['entry_total'] = 'Minimum Payment';
$_['entry_order_status'] = 'Completed Status';
$_['entry_geo_zone'] = 'Geo Zone';
$_['entry_status'] = 'Status';
$_['entry_sort_order'] = 'Sort Order';

// Help
$_['help_total'] = 'Minimum order amount before Yedpay will be enabled as a payment option (leave empty if Yedpay should always be enabled)';
$_['help_yedpay_setup'] = '<a target="_blank" href="http://www.yedpay.com">Click here</a> to learn how to set up Yedpay account.';
$_['help_test'] = 'Payment mode of Yedpay. Production mode for actual payment. Please update API Key and Sign Key according to the mode.';
$_['help_support_gateway'] = 'The gateway displayed in Yedpay online payment page.';
$_['help_support_wallet'] = 'Support Wallet of the gateway. (Applicable only for Alipay Online)';
$_['help_expiry_time'] = 'Online payment expiry time in seconds. (900-10800)';
$_['help_custom_id_prefix'] = 'Prefix of order id in Yedpay, leave empty if default order id is used (Maximum: 10 characters, accept only English letters)';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify Yedpay settings!';
$_['error_sign_key'] = 'Sign Key required!';
$_['error_token'] = 'API Key required!';
$_['error_warning'] = 'Warning!';
$_['error_expiry_time'] = 'The expiry time should be within 900-10800 seconds!';
$_['error_custom_id_prefix'] = 'Prefix must be less than or equal to 10 characters and accept English letters only!';
