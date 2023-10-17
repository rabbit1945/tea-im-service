<?php
namespace app\api\controller\v1\message;
use app\api\business\MessageReceiveBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use app\validate\MessageValidate;
use think\App;
use think\facade\Request;

class Message extends BaseController
{

    private  $business;

    public function __construct(App $app,MessageReceiveBusiness $business)
    {
        parent::__construct($app);
        $this->business = $business;

    }


    /**
     * 获取离线消息列表
     */
    public function getOffLineMessageList()
    {
        $user_id =static::$user_id;
        $room_id = Request::param('room_id');
        validate(MessageValidate::class)->check(['room_id' => $room_id]);

        $list = $this->business->getOffLineMessage($room_id,$user_id);
        if (empty($list)) return ImJson::output('20006');
        // 用户请求，获取到数据，离线消息送达。更改离线状态
        $room_msg_key = "room_$room_id"."_".$user_id;
        if (!$this->business->updateDeliveredStatus($user_id,['delivered'=>1],$room_msg_key)) ImJson::output('20006');

        return ImJson::output(10000,'成功',$list);
    }

    /**
     * 获取所有消息记录
     */
    public function getMessageList()
    {
        $user_id =static::$user_id;
        $room_id = Request::param('room_id');
        $page = Request::param('page')??1;
        $limit = Request::param('limit')??20;
        $validate = validate(MessageValidate::class);
        $result = $validate->check(['room_id' => $room_id]);
        if(!$result){
            return ImJson::output('20006',$validate->getError());
        }
        $onList = $this->business->getMessage($room_id,$user_id,$page,$limit);
        $offList = $this->business->getOffLineMessage($room_id,$user_id);

        if (!empty($offList)) {

            $offTotal =  $this->business->receiveCount($room_id,$user_id,0);
        }
        $list = array_merge($offList,$onList);
        if (empty($list))  return ImJson::output('20006');
        $total = $this->business->receiveCount($room_id,$user_id);
        $data = [
            "list" => $list,
            "total"=> $total,
            "offTotal" => $offTotal ?? 0

        ];
        if (!empty($offList)) {
            $room_msg_key = "room_$room_id"."_".$user_id;
            if (!$this->business->updateDeliveredStatus($user_id,['delivered'=>1],$room_msg_key)) return ImJson::output('20006');
        }

        return ImJson::output(10000,'成功',$data);

    }


}
