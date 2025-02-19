<?php
class WC_GDPR_Compliance {
    public function __construct() {
        add_filter('wp_privacy_personal_data_exporters', [$this, 'register_exporter']);
        add_filter('wp_privacy_personal_data_erasers', [$this, 'register_eraser']);
    }

    public function register_exporter($exporters) {
        $exporters['multi-login'] = [
            'exporter_friendly_name' => '多方式登录数据',
            'callback' => [$this, 'export_user_data']
        ];
        return $exporters;
    }

    public function export_user_data($email) {
        $user = get_user_by('email', $email);
        return [
            'data' => [
                [
                    'group_id' => 'multi_login',
                    'group_label' => '登录数据',
                    'item_id' => 'user_' . $user->ID,
                    'data' => [
                        '手机号' => get_user_meta($user->ID, 'billing_phone', true),
                        '微信绑定时间' => get_user_meta($user->ID, 'wechat_bind_date', true)
                    ]
                ]
            ]
        ];
    }

    public function register_eraser($erasers) {
        $erasers['multi-login'] = [
            'eraser_friendly_name' => '清除登录数据',
            'callback' => [$this, 'erase_user_data']
        ];
        return $erasers;
    }

    public function erase_user_data($email) {
        $user = get_user_by('email', $email);
        delete_user_meta($user->ID, 'wechat_unionid_encrypted');
        return ['items_removed' => true];
    }
}