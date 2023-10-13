<?php


namespace app\service\login;


use app\service\JsonService;
use GuzzleHttp\Client;
use think\facade\Config;
use think\facade\Log;


class Github implements OauthInterface
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

    /**
     * @var Config 配置
     */
    protected Config $config;

    /**
     * @var mixed
     */
    protected mixed $client_secret;


    public function __construct(Config $config)
    {

        $this->config = $config;
        $this->client_id = $config::get('login.github.client_id');
        $this->redirect_uri = $config::get('login.github.redirect_uri');
        $this->client_secret = $config::get('login.github.client_secret');

    }

    public function authorization(): string
    {
        return "https://github.com/login/oauth/authorize?client_id={$this->client_id}&redirect_uri={$this->redirect_uri}";
    }

    /**
     * @param $code
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccessToken($code): mixed
    {

        try {
            $url = "https://github.com/login/oauth/access_token";
            $query = array_filter([
                "code"       => $code,
                "client_id"   => $this->client_id,
                "client_secret"=> $this->client_secret
            ]);
            $client =  app()->make(client::class);
            $jsonService = app()->make(JsonService::class);
            return $jsonService->jsonDecode($client->request('POST', $url, [
                'json' => $query,
            ])->getBody()->getContents());

        } catch (\Exception $e) {
            return $e->getMessage();
        }


    }

    /**
     * @param $access_token
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserInfo($access_token): mixed
    {
        $url = 'https://api.github.com/user';
        $jsonService = app()->make(JsonService::class);
        $client =  app()->make(client::class);

        $data = $client->request('get',$url,[
            'headers' => [
                'Authorization' =>  `token $access_token`,
            ]
        ])->getBody()->getContents();
        Log::write(date('Y-m-d H:i:s').'.github_'.$data,'info');
        return $jsonService->jsonDecode($data);
    }


    /**
     * 获取邮箱
     * @param $access_token
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEmailInfo($access_token)
    {
        $url = 'https://api.github.com/user/emails';
        $jsonService = app()->make(JsonService::class);
        $client =  app()->make(client::class);

        $data = $client->request('get',$url,[
            'headers' => [
                'Accept'        => "application/vnd.github+json",
                'Authorization' =>  `token $access_token`,

            ]
        ])->getBody()->getContents();
        Log::write(date('Y-m-d H:i:s').'_'.$data,'info');
        return $jsonService->jsonDecode($data);

    }
}
