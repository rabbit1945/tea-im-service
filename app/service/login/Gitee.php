<?php


namespace app\service\login;
use GuzzleHttp\Client;
use app\common\utils\Curl;
use app\service\JsonService;
use GuzzleHttp\Exception\GuzzleException;
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
    protected $client_id;

    /**
     * 回调
     * @var
     */
    protected $redirect_uri;

    public function __construct()
    {

    }

    /**
     * 获取code
     */
    public function authorization($client_id,$redirect_uri): string
    {
        return "https://gitee.com/oauth/authorize?client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code";

    }

    /**
     * 获取token
     * @param $client_id
     * @param $redirect_uri
     * @param $client_secret
     * @param $code
     * @return void
     * @throws GuzzleException
     */
    public function getAccessToken($client_id,$redirect_uri,$client_secret,$code) {
        $url = "https://gitee.com/oauth/token";
        $query = array_filter([
            "grant_type" => "authorization_code",
            "code"       => $code,
            "client_id"   => $client_id,
            "redirect_uri"=> $redirect_uri,
            "client_secret"=> $client_secret
        ]);
        $client =  app()->make(client::class);
        return $client->request('POST', $url, [
            'query' => $query,
        ])->getBody()->getContents();
    }

}
