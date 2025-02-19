<?php
class WC_User_Binding {
    public function enforce_binding($user_id) {
        if (!$this->is_profile_complete($user_id)) {
            WC()->session->set('pending_user', $user_id);
            wp_redirect(wc_get_account_endpoint_url('bind-profile'));
            exit;
        }
    }

    private function is_profile_complete($user_id) {
        return get_user_meta($user_id, 'billing_phone', true) 
            && get_user_meta($user_id, 'phone_verified', true);
    }

    public function handle_binding_form() {
        add_shortcode('wechat_bind_form', function() {
            ob_start();
            wc_get_template('myaccount/bind-form.php');
            return ob_get_clean();
        });
    }
}

add_action('wp_login', function($user_login, $user) {
    (new WC_User_Binding())->enforce_binding($user->ID);
}, 10, 2);