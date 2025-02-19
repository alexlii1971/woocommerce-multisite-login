<?php
class WC_SMS_Verification {
    const PREFIX = 'wc_sms_';

    // 发送验证码
    public function send_code($phone) {
        if ($this->is_flooding($phone)) {
            throw new Exception('请求过于频繁，请稍后重试');
        }

        // 生成随机验证码
        $code = wp_rand(100000, 999999);

        // 存储验证码（加密存储）
        set_transient(
            self::PREFIX . md5($phone),
            wp_hash_password($code),
            $this->get_code_expiry()
        );

        // 获取短信模板
        $template = get_option('wc_sms_template_reg');

        // 调用短信网关发送验证码
        $gateway = $this->get_active_gateway();
        return $gateway->send(
            $phone,
            $template,
            ['code' => $code]
        );
    }

    // 验证验证码
    public function verify_code($phone, $input_code) {
        $hashed_code = get_transient(self::PREFIX . md5($phone));
        return $hashed_code && wp_check_password($input_code, $hashed_code);
    }

    // 获取当前激活的短信网关
    private function get_active_gateway() {
        $gateway = get_option('wc_sms_gateway');
        switch ($gateway) {
            case 'aliyun':
                return new WC_Aliyun_SMS();
            case 'tencent':
                return new WC_Tencent_SMS();
            default:
                throw new Exception('未配置短信服务商');
        }
    }

    // 检查是否频繁请求
    private function is_flooding($phone) {
        // 实现防刷逻辑
        $last_request = get_transient(self::PREFIX . 'flood_' . md5($phone));
        return $last_request && (time() - $last_request < 60);
    }

    // 获取验证码有效期
    private function get_code_expiry() {
        return apply_filters('wc_sms_code_expiry', 600); // 默认 10 分钟
    }
}