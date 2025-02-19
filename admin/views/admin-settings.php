<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e('多方式登录设置', 'woocommerce-multisite-login'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('multi_login_settings');
        do_settings_sections('multi_login_settings');
        submit_button();
        ?>
    </form>
</div>