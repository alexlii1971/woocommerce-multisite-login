<?php
// 文件路径：includes/sms/gateways/class-aliyun-sms.php

use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

class WC_Aliyun_SMS {
    /**
     * 发送短信验证码
     * @param string $phone     手机号
     * @param string $code      验证码
     * @return bool             发送结果
     */
    public function send($phone, $code) {
        // 从插件设置中获取配置
        $config = [
            'access_key'   => get_option('wc_sms_aliyun_key'),
            'secret'       => get_option('wc_sms_aliyun_secret'),
            'sign_name'    => get_option('wc_sms_aliyun_sign'),
            'template_id'  => get_option('wc_sms_aliyun_template') // 新增模板ID配置项
        ];

        // 初始化客户端
        $client = new DefaultAcsClient(
            new DefaultProfile(
                $config['access_key'],
                $config['secret']
            )
        );

        // 构建请求
        $request = new SendSmsRequest();
        $request->setPhoneNumbers($phone);
        $request->setSignName($config['sign_name']);
        $request->setTemplateCode($config['template_id']);
        $request->setTemplateParam(json_encode(['code' => $code])); // 动态绑定验证码

        try {
            $response = $client->getAcsResponse($request);
            return $response->Code === 'OK';
        } catch (Exception $e) {
            // 记录详细错误日志
            wc_get_logger()->error('阿里云短信发送失败', [
                'error' => $e->getMessage(),
                'phone' => $phone,
                'code'  => $code
            ]);
            return false;
        }
    }
}