<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// 清理插件选项
delete_option('wc_sms_enabled');
delete_option('wc_wechat_appid');
delete_option('wc_wechat_secret');
delete_option('wc_sms_aliyun_key');
delete_option('wc_sms_aliyun_secret');
delete_option('wc_sms_aliyun_sign');

// 清理用户元数据
global $wpdb;
$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key IN ('wechat_unionid', 'phone_verified', 'sms_code_cache')");