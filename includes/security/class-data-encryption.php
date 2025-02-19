<?php
class WC_Data_Encryptor {
    const METHOD = 'aes-256-cbc';
    
    public static function encrypt($data) {
        $key = defined('AUTH_SALT') ? AUTH_SALT : '';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::METHOD));
        
        return base64_encode($iv . openssl_encrypt(
            $data, 
            self::METHOD, 
            $key, 
            0, 
            $iv
        ));
    }

    public static function decrypt($encrypted) {
        $key = defined('AUTH_SALT') ? AUTH_SALT : '';
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, openssl_cipher_iv_length(self::METHOD));
        
        return openssl_decrypt(
            substr($data, openssl_cipher_iv_length(self::METHOD)), 
            self::METHOD, 
            $key, 
            0, 
            $iv
        );
    }
}

add_action('wp_insert_user', function($user_id) {
    if (!empty($_POST['wechat_unionid'])) {
        $encrypted = WC_Data_Encryptor::encrypt($_POST['wechat_unionid']);
        update_user_meta($user_id, 'wechat_unionid_encrypted', $encrypted);
    }
});