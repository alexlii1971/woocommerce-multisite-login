<?php
add_filter('pre_get_site_by_path', function($site, $domain, $path, $segments) {
    if (is_phone_number($domain)) {
        return get_user_site_by_phone($domain);
    }
    return $site;
}, 10, 4);

function get_user_site_by_phone($phone) {
    global $wpdb;
    
    $user_id = $wpdb->get_var($wpdb->prepare(
        "SELECT user_id FROM {$wpdb->usermeta} 
        WHERE meta_key = 'billing_phone' 
        AND meta_value = %s", 
        $phone
    ));
    
    return get_site(get_user_main_site($user_id));
}