<?php
if (!defined('ABSPATH')) {
    exit;
}

// 记录日志：微信登录按钮加载中
error_log('微信登录按钮加载中...');

// 记录日志：微信登录启用状态
error_log('微信登录启用状态: ' . (get_option('wc_wechat_enabled') ? '是' : '否'));

// 仅在微信登录启用时显示
if (!get_option('wc_wechat_enabled')) {
    return;
}
?>

<div class="wechat-login-button">
    <button type="button" class="button wechat-login-btn" onclick="window.location.href='<?php echo WC_WeChat_OAuth::get_auth_url(); ?>'">
        <img src="<?php echo plugins_url('assets/wechat-icon.png', dirname(__DIR__)); ?>" alt="微信登录">
        <?php esc_html_e('微信登录', 'woocommerce-multisite-login'); ?>
    </button>
</div>
