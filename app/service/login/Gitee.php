<?php


namespace app\service\login;

use app\common\utils\Curl;
use app\service\JsonService;
use think\facade\Log;

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
    public function getCode($client_id,$redirect_uri)
    {
       $url = "https://gitee.com/oauth/authorize?client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code";
       return redirect($url);
    }

}
