<?php


namespace app\service;
use think\Container;
use think\exception\ValidateException;
use think\swoole\Websocket;
use Swoole\Server;
use app\common\utils\JwToken;
class WebSocketService
{

    /**
     * 用户ID
     * @var
     */
    public  $user_id;

    public $server;
    public $websocket;
    public $jwToken;

    public function __construct(Server $server,Websocket $websocket,Container $container)
    {
        //启用压缩
        $this->server = $server;
        $this->websocket = $websocket;
        $this->jwToken = $container->make(JwToken::class);

    }

    /**
     * 鉴权
     */
    public function socketVerifyToken ($token = ""): bool
    {
        try {
            if (!$token) return false;
            $verify = $this->jwToken->verifyToken($token);
            if (!$verify) return false;
            $this->user_id = $verify['user_id'];
            return true;

        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     *
     */
    public function getUserId()
    {
        return $this->user_id;

    }




    /**
     * 判断客户端的状态
     * @param $fd
     * @return bool
     */
    public function isClientState($fd): bool
    {
        $client = $this->server->getClientInfo($fd);
        if (isset($client['websocket_status']))  return true;
        return false;

    }

}
