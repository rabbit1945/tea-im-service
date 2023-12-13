<?php


namespace app\service\ai;

use app\service\JsonService;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use think\facade\Config;


class ZhiPu implements AiChat
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
    public mixed $clientId;
    public mixed $clientSecret;
    public mixed $characterglm;

    public string $model = 'characterglm';

    public function __construct() {
        $this->domain =  Config::get('aichat.zhipu.domain');
        $this->characterglm = Config::get('aichat.zhipu.characterglm');
        $this->clientSecret =  Config::get('aichat.zhipu.client_secret');
        $this->clientId =  Config::get('aichat.zhipu.client_id');
        $this->accessToken = $this->getAccessToken();
    }




    /**
     * 获取应用ID
     * @return mixed
     */
    public function getClientId(): mixed
    {
        return  $this->clientId;
    }



    /**
     * @return mixed
     */
    public function getClientSecret(): mixed
    {
        return $this->clientSecret;
    }

    /**
     * @throws GuzzleException
     */
    public function run($messages): bool|array
    {
        $model = $this->model;

        $meta = [
            "user_info" => "我是宮先生，是一位软件工程师。",// 用户信息
            "bot_info"  => "苏小小，是阳光快乐的小女孩", // 角色信息
            "bot_name"  => "苏小小",// 角色名称
            "user_name" => "宮先生",// 用户名称
        ];

        $prompt = [
             [
                 "role"=> "user",
                 "content" => $messages
             ]
        ];
        $query = array_filter(
            [
                "model" => $model,
                "meta"  => $meta,
                "prompt"=> $prompt
            ]
        );
        $url = $this->domain.$this->characterglm;
        $client =  app()->make(client::class);
        $data = $client->request('POST', $url, [
            'headers' => [
                'Authorization' =>  $this->accessToken,
                'Accept' => "application/json"
            ],
            'json' => $query,
        ])->getBody()->getContents();
        if (!$data) return false;
        $jsonService = app()->make(JsonService::class);
        $info = $jsonService->jsonDecode($data);
        if (!$info['success']) return false;
        return [
            "result" => $info['data']['choices'][0]['content']
        ];
    }
    
    

    /**
     * 获取token
     * @return string
     */
    public function getAccessToken(): string
    {
        $secret = $this->getClientSecret();
        $id  = $this->getClientId();
        $payload = [
            "api_key" => $id,
            "exp" => time()+86400*64*1000,
            "timestamp" =>  time()*1000
        ];
       return JWT::encode($payload,$secret,head: ["alg" => "HS256", "sign_type" => "SIGN"]);
    }
}
