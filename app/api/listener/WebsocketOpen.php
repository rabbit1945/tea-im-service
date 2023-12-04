<?php
declare (strict_types = 1);

namespace app\api\listener;

use app\api\business\RoomUserBusiness;
use app\common\utils\JwToken;
use app\service\WebSocketService;
use Swoole\Server;
use think\exception\ErrorException;
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
        $fd = $this->websocket->getSender();
        $data = $request->param();
        // 鉴权
        if ($this->checkToken($data)) {
            // 检测用户状态
            $this->getUserState($data);
            // 加入房间
//            $this->join($data);
        } else {
            $this->server->close($fd);
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

        try {
            $token = $data['token'];
            $verify = $this->socketVerifyToken($token);
            Log::write(date('Y-m-d H:i:s') . '_用户参数' . $this->user_id, 'info');
            if ($verify === false) return false;
            if (!$this->join($this->user_id)) return false;

        } catch (ErrorException $e) {
            return false;
        }

        return true;


    }

    /**
     * 加入房间
     * @param $user_id
     * @return bool
     */
    public function join($user_id): bool
    {
        $roomUserBusiness = app()->make(RoomUserBusiness::class);
        $roomList = $roomUserBusiness->getRoomList($user_id);
        if ($roomList) {

            Log::write(date('Y-m-d H:i:s') . '$roomList' . json_encode($roomList), 'info');
            foreach ($roomList as $val) {
                $room_id = $val['room_id'];
                $this->websocket->join($room_id);
            }
            return true;

        }
        return false;


    }
}
