<?php
if (!defined('ABSPATH')) {
    exit;
}

$user_id = WC()->session->get('pending_user');
if (!$user_id) {
    return;
}

$user = get_user_by('id', $user_id);
if (!$user) {
    return;
}
?>

<div class="woocommerce-bind-profile">
    <h2><?php esc_html_e('绑定手机号', 'woocommerce-multisite-login'); ?></h2>
    <form method="post" class="woocommerce-form woocommerce-form-bind-profile">
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="bind_phone"><?php esc_html_e('手机号', 'woocommerce-multisite-login'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bind_phone" id="bind_phone" value="" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="bind_phone_code"><?php esc_html_e('验证码', 'woocommerce-multisite-login'); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="bind_phone_code" id="bind_phone_code" value="" />
            <button type="button" class="button" id="send_code_btn"><?php esc_html_e('获取验证码', 'woocommerce-multisite-login'); ?></button>
        </p>
        <p class="woocommerce-form-row form-row">
            <?php wp_nonce_field('bind_profile', 'bind_profile_nonce'); ?>
            <button type="submit" class="woocommerce-Button button" name="bind_profile" value="<?php esc_attr_e('绑定', 'woocommerce-multisite-login'); ?>"><?php esc_html_e('绑定', 'woocommerce-multisite-login'); ?></button>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#send_code_btn').on('click', function() {
        var phone = $('#bind_phone').val();
        if (!phone) {
            alert('请输入手机号');
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'send_bind_code',
                phone: phone
            },
            success: function(response) {
                if (response.success) {
                    alert('验证码已发送');
                } else {
                    alert('发送失败，请重试');
                }
            }
        });
    });
});
</script>