<?php
class WC_MultiLogin_Logger {
    public static function log($message, $level = 'info') {
        $logger = wc_get_logger();
        $context = ['source' => 'multi-login'];
        
        switch ($level) {
            case 'error':
                $logger->error($message, $context);
                break;
            case 'warning':
                $logger->warning($message, $context);
                break;
            default:
                $logger->info($message, $context);
        }
    }

    public static function debug_mode_check() {
        if (get_option('wc_enable_debug_log')) {
            add_filter('woocommerce_logging_enabled', '__return_true');
        }
    }
}

add_action('init', [WC_MultiLogin_Logger::class, 'debug_mode_check']);