<?php
class WC_Tencent_SMS {
    public function send($phone, $template_id, $params) {
        $config = [
            'secret_id' => get_option('wc_sms_tencent_secret_id'),
            'secret_key' => get_option('wc_sms_tencent_secret_key'),
            'sdk_appid' => get_option('wc_sms_tencent_sdk_appid'),
            'sign_name' => get_option('wc_sms_tencent_sign')
        ];

        $client = new TencentCloud\Sms\V20210111\SmsClient(
            new TencentCloud\Common\Credential($config['secret_id'], $config['secret_key']),
            'ap-guangzhou'
        );

        $req = new TencentCloud\Sms\V20210111\Models\SendSmsRequest();
        $req->SmsSdkAppId = $config['sdk_appid'];
        $req->SignName = $config['sign_name'];
        $req->TemplateId = $template_id;
        $req->PhoneNumberSet = [$phone];
        $req->TemplateParamSet = array_values($params);

        try {
            $resp = $client->SendSms($req);
            return $resp->SendStatusSet[0]->Code === 'Ok';
        } catch (Exception $e) {
            wc_get_logger()->error('腾讯云短信异常: ' . $e->getMessage());
            return false;
        }
    }
}