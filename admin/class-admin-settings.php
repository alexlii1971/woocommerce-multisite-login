<?php
class WC_MultiLogin_Admin {
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function register_settings() {
        // 注册设置选项
        register_setting('multi_login_settings', 'wc_sms_enabled');
        register_setting('multi_login_settings', 'wc_wechat_enabled');
        register_setting('multi_login_settings', 'wc_wechat_appid');
        register_setting('multi_login_settings', 'wc_wechat_secret');
        register_setting('multi_login_settings', 'wc_sms_gateway');
        register_setting('multi_login_settings', 'wc_sms_aliyun_key');
        register_setting('multi_login_settings', 'wc_sms_aliyun_secret');
        register_setting('multi_login_settings', 'wc_sms_aliyun_sign');
        register_setting('multi_login_settings', 'wc_sms_tencent_secret_id');
        register_setting('multi_login_settings', 'wc_sms_tencent_secret_key');
        register_setting('multi_login_settings', 'wc_sms_tencent_sdk_appid');
        register_setting('multi_login_settings', 'wc_enable_debug_log');

        // 添加短信配置部分
        add_settings_section(
            'sms_settings',
            __('短信配置', 'woocommerce-multisite-login'),
            function() {
                echo '<p>' . __('短信服务商参数设置', 'woocommerce-multisite-login') . '</p>';
            },
            'multi_login_settings'
        );

        // 启用短信验证
        add_settings_field(
            'wc_sms_enabled',
            __('启用短信验证', 'woocommerce-multisite-login'),
            [$this, 'render_checkbox'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_enabled']
        );

        // 短信服务商选择
        add_settings_field(
            'wc_sms_gateway',
            __('短信服务商', 'woocommerce-multisite-login'),
            [$this, 'render_select'],
            'multi_login_settings',
            'sms_settings',
            [
                'name' => 'wc_sms_gateway',
                'options' => [
                    'aliyun' => __('阿里云', 'woocommerce-multisite-login'),
                    'tencent' => __('腾讯云', 'woocommerce-multisite-login')
                ]
            ]
        );

        // 阿里云配置
        add_settings_field(
            'wc_sms_aliyun_key',
            __('阿里云AccessKey', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_aliyun_key']
        );

        add_settings_field(
            'wc_sms_aliyun_secret',
            __('阿里云SecretKey', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_aliyun_secret']
        );

        add_settings_field(
            'wc_sms_aliyun_sign',
            __('短信签名', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_aliyun_sign']
        );

        // 腾讯云配置
        add_settings_field(
            'wc_sms_tencent_secret_id',
            __('腾讯云SecretId', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_tencent_secret_id']
        );

        add_settings_field(
            'wc_sms_tencent_secret_key',
            __('腾讯云SecretKey', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_tencent_secret_key']
        );

        add_settings_field(
            'wc_sms_tencent_sdk_appid',
            __('腾讯云SDK AppId', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'sms_settings',
            ['name' => 'wc_sms_tencent_sdk_appid']
        );

        // 添加微信配置部分
        add_settings_section(
            'wechat_settings',
            __('微信配置', 'woocommerce-multisite-login'),
            function() {
                echo '<p>' . __('微信登录参数设置', 'woocommerce-multisite-login') . '</p>';
            },
            'multi_login_settings'
        );

        // 启用微信登录
        add_settings_field(
            'wc_wechat_enabled',
            __('启用微信登录', 'woocommerce-multisite-login'),
            [$this, 'render_checkbox'],
            'multi_login_settings',
            'wechat_settings',
            ['name' => 'wc_wechat_enabled']
        );

        // 微信AppID
        add_settings_field(
            'wc_wechat_appid',
            __('微信AppID', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'wechat_settings',
            ['name' => 'wc_wechat_appid']
        );

        // 微信AppSecret
        add_settings_field(
            'wc_wechat_secret',
            __('微信AppSecret', 'woocommerce-multisite-login'),
            [$this, 'render_input'],
            'multi_login_settings',
            'wechat_settings',
            ['name' => 'wc_wechat_secret']
        );

        // 添加调试配置部分
        add_settings_section(
            'debug_settings',
            __('调试配置', 'woocommerce-multisite-login'),
            function() {
                echo '<p>' . __('调试模式设置', 'woocommerce-multisite-login') . '</p>';
            },
            'multi_login_settings'
        );

        // 启用调试日志
        add_settings_field(
            'wc_enable_debug_log',
            __('启用调试日志', 'woocommerce-multisite-login'),
            [$this, 'render_checkbox'],
            'multi_login_settings',
            'debug_settings',
            ['name' => 'wc_enable_debug_log']
        );
    }

    public function render_checkbox($args) {
        $value = get_option($args['name']);
        echo "<input type='checkbox' name='{$args['name']}' value='1' " 
           . checked(1, $value, false) . ">";
    }

    public function render_input($args) {
        $value = get_option($args['name']);
        echo "<input type='text' name='{$args['name']}' value='{$value}' class='regular-text'>";
    }

    public function render_select($args) {
        $value = get_option($args['name']);
        echo "<select name='{$args['name']}'>";
        foreach ($args['options'] as $key => $label) {
            echo "<option value='{$key}' " . selected($key, $value, false) . ">{$label}</option>";
        }
        echo "</select>";
    }

    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('多方式登录设置', 'woocommerce-multisite-login'),
            __('登录管理', 'woocommerce-multisite-login'),
            'manage_options',
            'multi-login-settings',
            [$this, 'render_settings_page']
        );
    }

    public function render_settings_page(): void {
        $path = plugin_dir_path(__DIR__) . 'admin/views/admin-settings.php';
        error_log("Trying to load: " . $path); // 调试日志
        if (file_exists($path)) {
            include $path;
        } else {
            error_log("File not found: " . $path); // 文件不存在时记录日志
            echo '<div class="error"><p>' . __('配置文件未找到，请检查插件目录结构。', 'woocommerce-multisite-login') . '</p></div>';
        }
    }
        /**
     * 检查短信验证是否启用
     */
    public function is_sms_enabled() {
        return get_option('wc_sms_enabled') === '1';
    }

    /**
     * 检查微信登录是否启用
     */
    public function is_wechat_enabled() {
        return get_option('wc_wechat_enabled') === '1';
    }

}