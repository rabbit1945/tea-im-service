<?php
declare (strict_types = 1);

namespace app\api\listener;

use app\api\business\RoomUserBusiness;
use app\common\utils\ImJson;
use app\common\utils\JwToken;
use app\service\WebSocketService;
use Swoole\Server;
use think\Container;
use think\exception\ErrorException;
use think\exception\ValidateException;
use think\facade\Log;
use think\swoole\Websocket;

class WebsocketOpen extends WebSocketService
{
    /**
     *
     * @param Server $server
     * @param Websocket $websocket
     */

    public function __construct(Server $server,Websocket $websocket,JwToken $jwToken)
    {
        parent::__construct( $server, $websocket,$jwToken);
    }

    /**
     * 事件监听处理
     *
     * @return void
     */
    public function handle($request)
    {
        $data = $request->param();
        // 鉴权
        if ($this->checkToken($data)){
            // 检测用户状态
            $this->getUserState($data);
            // 加入房间
            $this->join($data);
        }

    }

    /**
     * 获取用户状态
     * @param $data
     */
    public function getUserState($data)
    {
        $fd = $this->websocket->getSender();
        $isClientState = $this->isClientState($fd);
        if ($isClientState) {
            echo '用户状态'.$this->user_id."上线".$fd;

        } else {
            echo '用户状态'.$this->user_id."离线".$fd;
        }
    }

    /**
     * token 鉴权
     * @param $data
     * @return bool
     */
    public function checkToken($data): bool
    {
        $fd = $this->websocket->getSender();
        try {

            $token = $data['token'];
            $verify = $this->socketVerifyToken($token);

            if ($verify === false) {
                $this->server->close($fd);
                return false;
            }

        } catch (ErrorException $e) {
            $this->server->close($fd);
            return false;

        }

        return true;


    }

    /**
     * 加入房间
     * @param $data
     * @return void
     */
    public function join($data): void
    {
        $token = $data['token'];
        $verify = $this->jwToken->verifyToken($token);

        $user_id = $verify['user_id'];
        $roomUserBusiness = app()->make(RoomUserBusiness::class);
        $roomList = $roomUserBusiness->getRoomList($user_id);
        if ($roomList) {

            Log::write(date('Y-m-d H:i:s').'$roomList'.json_encode($roomList),'info');
            foreach ($roomList as $val) {
                $room_id = $val['room_id'];
                $this->websocket->join($room_id);
            }

        }

    }
}
