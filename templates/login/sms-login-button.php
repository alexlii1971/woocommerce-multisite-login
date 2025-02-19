<?php
if (!defined('ABSPATH')) {
    exit;
}

// 仅在短信验证启用时显示
if (!get_option('wc_sms_enabled')) {
    return;
}
?>

<div class="sms-login-section">
    <button type="button" class="button sms-trigger-btn">
        <?php esc_html_e('短信验证登录', 'woocommerce-multisite-login'); ?>
    </button>
    <div class="sms-code-form" style="display:none;">
        <input type="tel" name="sms_phone" placeholder="<?php esc_attr_e('请输入手机号', 'woocommerce-multisite-login'); ?>">
        <button type="button" class="button send-code-btn">
            <?php esc_html_e('获取验证码', 'woocommerce-multisite-login'); ?>
        </button>
        <input type="text" name="sms_code" placeholder="<?php esc_attr_e('请输入验证码', 'woocommerce-multisite-login'); ?>">
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.sms-trigger-btn').on('click', function() {
        $(this).hide();
        $('.sms-code-form').show();
    });
});
</script>