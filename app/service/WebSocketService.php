<?php


namespace app\service;
use app\common\utils\JwToken;
use Exception;
use Swoole\Server;
use think\swoole\Websocket;

class WebSocketService
{
    public Server $server;
    public Websocket $websocket;
    public JwToken $jwToken;
    public mixed $user_id;

    public function __construct(Server $server,Websocket $websocket,JwToken $jwToken)
    {
        //启用压缩
        $this->server = $server;
        $this->websocket = $websocket;
        $this->jwToken = $jwToken;

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

        } catch (Exception $e) {
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
