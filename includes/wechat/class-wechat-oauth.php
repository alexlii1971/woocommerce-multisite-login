<?php
class WC_WeChat_OAuth {
    const API_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    public function get_auth_url($scope = 'snsapi_base') {
        $callback = urlencode($this->get_callback_url());
        $appid = get_option('wc_wechat_appid');
        
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . http_build_query([
            'appid' => $appid,
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => wp_create_nonce('wechat_auth')
        ]) . "#wechat_redirect";
    }

    public function handle_callback() {
        if (!wp_verify_nonce($_GET['state'], 'wechat_auth')) {
            throw new Exception('非法请求来源');
        }

        $response = wp_remote_get(self::API_URL . '?' . http_build_query([
            'appid' => get_option('wc_wechat_appid'),
            'secret' => get_option('wc_wechat_secret'),
            'code' => $_GET['code'],
            'grant_type' => 'authorization_code'
        ]));

        $data = json_decode($response['body'], true);
        return $this->process_user($data['unionid'] ?? $data['openid']);
    }

    private function process_user($wechat_id) {
        $users = get_users([
            'meta_key' => 'wechat_unionid',
            'meta_value' => $wechat_id,
            'number' => 1
        ]);

        if (!empty($users)) {
            return $users[0];
        }

        return $this->create_wechat_user($wechat_id);
    }

    private function get_callback_url() {
        // 获取回调 URL 的逻辑实现
        // ...
    }

    private function create_wechat_user($wechat_id) {
        // 创建新用户的逻辑实现
        // ...
    }

    // 集成微信 SDK 并实现 OAuth 流程
    public function get_auth_url_v2() {
        $appid = get_option('wc_wechat_appid');
        $redirect_uri = urlencode($this->get_callback_url());
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=wechat_login#wechat_redirect";
    }
    
    public function handle_callback_v2($code) {
        // 调用微信 SDK 获取用户信息
        $user_info = $this->get_user_info_v2($code);
        // 处理用户绑定逻辑
        return $this->process_user($user_info['unionid'] ?? $user_info['openid']);
    }

    private function get_user_info_v2($code) {
        $response = wp_remote_get(self::API_URL . '?' . http_build_query([
            'appid' => get_option('wc_wechat_appid'),
            'secret' => get_option('wc_wechat_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]));

        return json_decode($response['body'], true);
    }
}
