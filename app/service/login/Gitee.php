<?php


namespace app\service\login;
use GuzzleHttp\Client;
use app\common\utils\Curl;
use app\service\JsonService;
use GuzzleHttp\Exception\GuzzleException;
use think\facade\Config;
use think\facade\Log;
use function Swoole\Coroutine\Http\post;

/**
 * Class gitee
 * @package app\service\login
 */
class Gitee
{
    /**
     * 客户端
     * @var
     */
    protected mixed $client_id;

    /**
     * 回调
     * @var
     */
    protected mixed $redirect_uri;

    protected Config $config;
    protected mixed $client_secret;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client_id = $config::get('login.gitee.client_id');
        $this->redirect_uri = $config::get('login.gitee.redirect_uri')."?origin=gitee";
        $this->client_secret = $config::get('login.gitee.client_secret');

    }

    /**
     * 回调获取code
     */
    public function authorization(): string
    {
        $redirect_uri =$this->redirect_uri;
        return "https://gitee.com/oauth/authorize?client_id={$this->client_id}&redirect_uri={$redirect_uri}&response_type=code";
    }

    /**
     * 获取token
     * @param $code
     * @return mixed
     * @throws GuzzleException
     */
    public function getAccessToken($code) {
        $url = "https://gitee.com/oauth/token";
        $query = array_filter([
            "grant_type" => "authorization_code",
            "code"       => $code,
            "client_id"   => $this->client_id,
            "redirect_uri"=> $this->redirect_uri,
            "client_secret"=> $this->client_secret
        ]);
        $client =  app()->make(client::class);
        $jsonService = app()->make(JsonService::class);
        return $jsonService->jsonDecode($client->request('POST', $url, [
            'query' => $query,
        ])->getBody()->getContents());
    }

    /**
     * @throws GuzzleException
     */
    public function getUserInfo($access_token)
    {
        $url = 'https://gitee.com/api/v5/user?access_token='.$access_token;
        $jsonService = app()->make(JsonService::class);
        $client =  app()->make(client::class);
        return $jsonService->jsonDecode($client->get($url)->getBody()->getContents());
    }

}
