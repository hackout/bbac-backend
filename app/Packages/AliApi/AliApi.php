<?php
namespace App\Packages\AliApi;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use Illuminate\Validation\ValidationException;

class AliApi
{
    private $runtime;

    private $service;

    public function __construct()
    {
        $config = new Config([
            'accessKeyId' => env('ALIBABA_ACCESS_KEY_ID'),
            'accessKeySecret' => env('ALIBABA_ACCESS_KEY_SECRET'),
        ]);
        $config->endpoint = "dysmsapi.aliyuncs.com";
        $this->runtime = new RuntimeOptions();
        $this->runtime->maxIdleConns = 3;
        $this->runtime->connectTimeout = 10000;
        $this->runtime->readTimeout = 10000;
        $this->service = new Dysmsapi($config);
    }

    /**
     * 发送短信
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $mobile 手机号码
     * @param  string $code 验证码
     * @return void
     */
    public function sentSms(string $mobile,string $code)
    {
        $data = [
            'phoneNumbers' => $mobile,
            'signName' => env('ALIBABA_SMS_SIGN'),
            'templateCode' => env('ALIBABA_SMS_TEMPLATE'),
            'templateParam' => json_encode([env('ALIBABA_SMS_TEMPLATE_CODE')=>$code])
        ];
        $sendSmsRequest = new SendSmsRequest($data);
        try {
            $this->service->sendSmsWithOptions($sendSmsRequest,$this->runtime);
        } catch (\Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            throw ValidationException::withMessages(['message.error' => $error->message]);
        }
    }
}