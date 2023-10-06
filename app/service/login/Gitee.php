<?php


namespace app\service\login;

use app\common\utils\Curl;
use app\service\JsonService;
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
    public function getCode($client_id,$redirect_uri): string
    {
        return "https://gitee.com/oauth/authorize?client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code";
    }

    /**
     * 回调信息
     * @param $code
     * @return void
     */
    public function callback($client_id,$redirect_uri,$client_secret,$code) {
        $url = "https://gitee.com/oauth/token";
        $data = Curl::send($url,"","",[
            "grant_type" => "authorization_code",
            "code"       => $code,
            "client_id"   => $client_id,
            "redirect_uri"=> $redirect_uri,
            "client_secret"=> $client_secret

        ],"post");
        $info = json_decode($data,true);
        return $info;
    }

}
