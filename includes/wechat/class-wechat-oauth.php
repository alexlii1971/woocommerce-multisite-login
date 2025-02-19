<?php
class WC_WeChat_OAuth {
    const API_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * 获取微信登录授权 URL（静态方法）
     */
    public static function get_auth_url($scope = 'snsapi_base') {
        error_log('生成微信登录 URL：开始');

        // 检查 AppID 是否配置
        $appid = get_option('wc_wechat_appid');
        if (empty($appid)) {
            error_log('微信登录错误：未配置 AppID');
            return '#';
        }

        // 获取回调地址
        $callback = urlencode(self::get_callback_url());
        error_log('微信回调地址：' . $callback);

        // 生成授权 URL
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?" . http_build_query([
            'appid' => $appid,
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => wp_create_nonce('wechat_auth')
        ]) . "#wechat_redirect";

        error_log('生成的微信登录 URL：' . $url);
        return $url;
    }

    /**
     * 获取微信登录回调地址（静态方法）
     */
    private static function get_callback_url() {
        return home_url('/wc-api/wechat-callback'); // 示例回调地址
    }

    /**
     * 处理微信登录回调
     */
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

    /**
     * 处理用户信息
     */
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

    /**
     * 创建微信用户
     */
    private function create_wechat_user($wechat_id) {
        // 创建新用户的逻辑实现
        // ...
    }

    /**
     * 获取微信用户信息（V2 版本）
     */
    public function handle_callback_v2($code) {
        $user_info = $this->get_user_info_v2($code);
        return $this->process_user($user_info['unionid'] ?? $user_info['openid']);
    }

    /**
     * 获取微信用户信息（V2 版本）
     */
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