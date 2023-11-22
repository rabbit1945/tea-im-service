<?php
namespace app\api\controller\v1\message;
use app\api\business\MessageReceiveBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use app\validate\MessageValidate;
use think\App;
use think\facade\Request;
use think\Response;

class Message extends BaseController
{

    private  $business;

    public function __construct(App $app,MessageReceiveBusiness $business)
    {
        parent::__construct($app);
        $this->business = $business;
    }

    /**
     * 获取所有消息记录
     */
    public function getMessageList(): Response
    {
        $user_id = static::$user_id;
        $room_id = Request::param('room_id');
        $page = Request::param('page') ?? 1;
        $limit = Request::param('limit') ?? 20;
        $validate = validate(MessageValidate::class);
        $result = $validate->check(['room_id' => $room_id]);
        if (!$result) {
            return ImJson::output('20006', $validate->getError());
        }
        $list = $this->business->getMessage($room_id,$user_id,$page,$limit);

        if (empty($list))  return ImJson::output('20006');
        // 获取消息总数
        $total = $this->business->receiveCount($room_id,$user_id);
        //  获取离线消息总数
        $offTotal = $this->business->receiveCount($room_id,$user_id,0);
        $data = [
            "list" => $list,
            "total"=> $total,
            "offTotal" => $offTotal ?? 0
        ];
        if (!empty($offTotal)) {
            if (!$this->business->updateDeliveredStatus($user_id,['delivered'=>1])) return ImJson::output('20006');
        }

        return ImJson::output(10000,'成功',$data);

    }


}
