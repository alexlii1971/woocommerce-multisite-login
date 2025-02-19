<?php
/**
 * Plugin Name: WooCommerce Multisite Login
 * Description: 在WordPress多站点环境中与WooCommerce集成，支持手机号、短信、微信登录等功能。
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL-2.0+
 * Text Domain: woocommerce-multisite-login
 */

defined('ABSPATH') || exit;

// 定义插件常量
define('WC_MULTISITE_LOGIN_VERSION', '1.0.0');
define('WC_MULTISITE_LOGIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WC_MULTISITE_LOGIN_PLUGIN_URL', plugin_dir_url(__FILE__));

// 加载核心功能
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/class-multisite-sync.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/class-cross-site-login.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/class-user-query.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/sms/class-sms-core.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/sms/class-sms-anti-flood.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/wechat/class-wechat-oauth.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/class-user-binding.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/security/class-data-encryption.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/class-debug-logger.php';
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'includes/compliance/class-gdpr.php';

// 加载后台管理
require_once WC_MULTISITE_LOGIN_PLUGIN_DIR . 'admin/class-admin-settings.php';

// 初始化插件
add_action('plugins_loaded', function() {
    // 初始化管理类（必须先加载才能读取设置）
    $admin = new WC_MultiLogin_Admin();
    new WC_GDPR_Compliance();

    // 挂载前端功能（需确保管理类已初始化设置）
    add_action('woocommerce_login_form', 'add_sms_wechat_login_buttons');
    add_action('woocommerce_register_form', 'add_sms_wechat_login_buttons');
});

/**
 * 登录按钮渲染函数
 */
function add_sms_wechat_login_buttons() {
    // 获取管理类实例
    $admin = new WC_MultiLogin_Admin();

    // 短信登录按钮
    if ($admin->is_sms_enabled()) {
        wc_get_template('login/sms-login-button.php', [], '', WC_MULTISITE_LOGIN_PLUGIN_DIR . 'templates/');
    }

    // 微信登录按钮
    if ($admin->is_wechat_enabled()) {
        wc_get_template('login/wechat-login-button.php', [], '', WC_MULTISITE_LOGIN_PLUGIN_DIR . 'templates/');
    }
}

/**
 * 挂载微信登录按钮到 WooCommerce 登录/注册页面
 */
add_action('woocommerce_login_form', 'add_wechat_login_button');
add_action('woocommerce_register_form', 'add_wechat_login_button');

function add_wechat_login_button() {
    // 仅在微信登录启用时显示
    if (get_option('wc_wechat_enabled')) {
        wc_get_template('login/wechat-login-button.php', [], '', WC_MULTISITE_LOGIN_PLUGIN_DIR . 'templates/');
    }
}