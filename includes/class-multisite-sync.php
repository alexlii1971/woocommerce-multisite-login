<?php
class Multisite_User_Sync {
    
    public function sync_new_user($user_id, $password, $meta) {
        if (!is_multisite()) return;
        
        $primary_blog_id = get_current_blog_id();
        $sites = get_sites(['exclude' => $primary_blog_id]);
        
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);
            
            if (!get_user_by('id', $user_id)) {
                add_existing_user_to_blog([
                    'user_id' => $user_id,
                    'role'    => get_option('default_role')
                ]);
                
                $this->sync_user_meta($user_id, $meta);
            }
            
            restore_current_blog();
        }
    }
    
    private function sync_user_meta($user_id, $meta) {
        $sync_keys = apply_filters('multisite_sync_meta_keys', [
            'billing_phone',
            'phone_verified',
            'wechat_unionid'
        ]);
        
        foreach ($sync_keys as $key) {
            if (isset($meta[$key])) {
                update_user_meta($user_id, $key, $meta[$key]);
            }
        }
    }
}

add_action('plugins_loaded', function() {
    $sync = new Multisite_User_Sync();
    add_action('wpmu_activate_user', [$sync, 'sync_new_user'], 10, 3);
});