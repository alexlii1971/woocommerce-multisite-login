<?php
class WC_SMS_Flood_Control {
    public function is_flooding($phone) {
        $last_sent = get_transient('wc_sms_last_sent_' . $phone);
        $retry_window = get_option('wc_sms_retry_interval', 60);

        if ($last_sent && (time() - $last_sent) < $retry_window) {
            return true;
        }

        set_transient('wc_sms_last_sent_' . $phone, time(), $retry_window);
        return false;
    }

    public function daily_limit_check($phone) {
        $count_key = 'wc_sms_daily_' . date('Ymd') . '_' . $phone;
        $count = get_transient($count_key) ?: 0;
        
        $max_daily = get_option('wc_sms_max_daily', 10);
        if ($count >= $max_daily) {
            throw new Exception('今日验证码发送已达上限');
        }

        set_transient($count_key, $count + 1, DAY_IN_SECONDS);
    }
}