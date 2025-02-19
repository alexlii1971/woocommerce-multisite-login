<?php
add_action('woocommerce_login_form', 'add_wechat_login_button');
add_action('woocommerce_register_form', 'add_wechat_login_button');

function add_wechat_login_button() {
    if (get_option('wc_wechat_enabled')): ?>
        <div class="wechat-login-wrapper">
            <button type="button" 
                    class="button wechat-login-btn"
                    onclick="initWechatAuth()">
                <img src="<?= plugins_url('assets/wechat-icon.png', __FILE__) ?>">
                微信登录
            </button>
        </div>
        
        <script>
        function initWechatAuth() {
            <?php if(wp_is_mobile()): ?>
                window.location.href = '<?= WC_WeChat_OAuth::get_auth_url() ?>';
            <?php else: ?>
                new QRCode(document.getElementById('qrcode'), {
                    text: '<?= WC_WeChat_OAuth::get_auth_url('snsapi_login') ?>'
                });
            <?php endif; ?>
        }
        </script>
    <?php endif;
}