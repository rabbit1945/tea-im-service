<?php


namespace app\service\ai;


use app\common\utils\Curl;
use app\service\JsonService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;

class QianFan implements AiChat
{
    /**
     * 域名
     * @var mixed|string
     */
    public mixed $domain;
    /**
     * token
     * @var mixed
     */
    public mixed $accessToken;
    /**
     * 参数
     * @var
     */
    public mixed $parameter;


    /**
     * 应用的API Key
     * @var mixed
     */
    public mixed $clientId;

    /**
     * 应用的Secret Key
     * @var mixed
     */
    public mixed $clientSecret;

    /**
     * @var string
     */
    public string $grantType;


    public string $cacheName = 'newQianFanToken';


    /**
     * @throws Exception
     */
    public function __construct() {
        $this->domain = Config::get('aichat.qian_fan.domain');
        $this->accessToken = $this->getAccessToken();
        $this->parameter   = Config::get('aichat.qian_fan.chatglm2_6b_32k');

    }

    /**
     * 获取模型名称
     * @throws Exception
     */
    public function getModel($model): static
    {
        if (empty($model))  throw new Exception('请填写模型名称');

        $this->model = $model;

        $this->parameter =  Config::get('aichat.qian_fan.'.$model);
        return $this;
    }

    /**
     * 获取应用ID
     * @return $this
     */
    public function getClientId(): static
    {
        $this->clientId = Config::get('aichat.qian_fan.client_id');
        return $this;
    }

    /**
     * @return $this
     */
    public function getClientSecret(): static
    {
        $this->clientSecret = Config::get('aichat.qian_fan.client_secret');
        return $this;
    }

    /**
     * @return $this
     */
    public function getGrantType(): static
    {
        $this->grantType = Config::get('aichat.qian_fan.grant_type');
        return $this;

    }

    /**
     * @param $messages
     * @param string $user_id
     * @return false|mixed
     * @throws GuzzleException
     * @throws BindingResolutionException
     */
    public function run($messages, string $user_id = ""): mixed
    {
        $msgData = [
            "user_id"=> $user_id,
            "messages"=> [
                [
                    "role" => 'user',
                    "content"=> $messages
                ]
            ]
        ];
        $jsonService = app()->make(JsonService::class);
        $url = $this->domain.$this->parameter.$this->accessToken;
        $client =  app()->make(client::class);
        $data = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => "application/json"
            ],
            'json' => $msgData,
        ])->getBody()->getContents();
        if (!$data) return false;
        Log::write(date('Y-m-d H:i:s').'_baiduAiService_'.json_encode($data),'info');
        return $jsonService->jsonDecode($data);
    }

    public function getAccessToken()
    {
        if (Cache::has($this->cacheName)) return Cache::get($this->cacheName);
        $clientId =  $this->getClientId()->clientId;
        $clientSecret = $this->getClientSecret()->clientSecret;
        $grantType = $this->getGrantType()->grantType;
        $oauth = Config::get('aichat.qian_fan.oauth');
        $parameter = "{$oauth}client_id={$clientId}&client_secret={$clientSecret}&grant_type={$grantType}";

        $url =  $this->domain.$parameter;
        $curl =  app()->make(Curl::class);
        $send = $curl->send($url,'[]',array(
            'Content-Type: application/json',
            'Accept: application/json'
        ),'post');


        if (empty($send)) throw new Exception('数据返回为空');
        $data = app()->make(JsonService::class)->jsonDecode($send);
        Cache::set($this->cacheName,$data['access_token'],$data['expires_in']-86400);

        return $data['access_token'];

    }
}
