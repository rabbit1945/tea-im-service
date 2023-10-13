<?php


namespace app\service\ai;


use app\common\utils\Curl;
use app\service\JsonService;
use \Exception;
use think\facade\Cache;
use think\facade\Config;

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
    public $parameter;

    /**
     * 模型
     * @var
     */
    public  $model;

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




    public function __construct() {
        $this->domain = Config::get('aiChat.qian_fan.domain');

        $this->accessToken = $this->getAccessToken();

    }

    /**
     * 获取模型名称
     * @throws Exception
     */
    public function getModel($model): static
    {
        if (empty($model))  throw new Exception('请填写模型名称');

        $this->model = $model;

        $this->parameter =  Config::get('aiChat.qian_fan.'.$model);
        return $this;
    }

    /**
     * 获取应用ID
     * @return $this
     */
    public function getClientId(): static
    {
        $this->clientId = Config::get('aiChat.qian_fan.client_id');
        return $this;
    }

    /**
     * @return $this
     */
    public function getClientSecret(): static
    {
        $this->clientSecret = Config::get('aiChat.qian_fan.client_secret');
        return $this;
    }

    /**
     * @return $this
     */
    public function getGrantType(): static
    {
        $this->grantType = Config::get('aiChat.qian_fan.grant_type');
        return $this;

    }



    public function run($messages)
    {
        $url = $this->domain.$this->parameter.$this->accessToken;
        $curl =  app()->make(Curl::class);

        return $curl->send($url,$messages,array(
            'Content-Type: application/json'
        ),'post');


    }

    public function getAccessToken()
    {
        if (Cache::has($this->cacheName)) return Cache::get($this->cacheName);
        $clientId =  $this->getClientId()->clientId;
        $clientSecret = $this->getClientSecret()->clientSecret;
        $grantType = $this->getGrantType()->grantType;
        $oauth = Config::get('aiChat.qian_fan.oauth');
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
