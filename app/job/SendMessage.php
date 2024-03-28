<?php


namespace app\job;
use app\service\JsonService;
use think\facade\Log;
use think\facade\Queue;


class SendMessage
{

    /**
     * 发布任务
     */
    public function send($data): bool
    {
        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'ReceiveMessage';

        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName        = "MessageJobQueue";
        $data['msg_content'] =  urlencode($data['msg']);
        $data['send_time']   = $data['send_timestamp'];
        $data['contact']     = $data['contactList']??"";
        $data['msg_form']    = $data['user_id'];
        $sendData = app()->make(JsonService::class)->jsonEncode($data);
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName ,$sendData, $jobQueueName);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ) {

            Log::write(date('Y-m-d H:i:s').'_发布成功'.$sendData,'info');
            return true;
        } else {
            Log::write(date('Y-m-d H:i:s').$sendData.'_发布失败','info');
            return false;
        }

    }




}
