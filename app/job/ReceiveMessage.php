<?php


namespace app\job;


use app\home\business\MessageReceiveBusiness;
use app\home\business\MessageSendBusiness;
use app\model\UserSendModel;
use think\facade\Db;
use think\facade\Log;
use think\queue\Job;

class ReceiveMessage
{


    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job,$data) {


        // 有些消息在到达消费者时,可能已经不再需要执行了
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){

            $job->delete();
            return;
        }

        $data = json_decode($data,true);

        $isJobDone = $this->receiveMsg($data);
        if ($isJobDone === true) {
            // 如果任务执行成功， 记得删除任务
            $job->delete();

        } else {

            //通过这个方法可以检查这个任务已经重试了几次了
            if ($job->attempts() > 3) {
                // 如果任务执行成功， 记得删除任务
                $job->delete();

                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }

    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data): bool
    {
        return true;
    }

    /**
     * 处理消费
     * @param $data
     * @return bool
     */
    public function receiveMsg($data): bool
    {

        // 启动事务
        $userSendModel = app()->make(UserSendModel::class);
        $userSendModel::startTrans();
        try {

            app()->make(MessageSendBusiness::class)->addSend($data);

            app()->make(MessageReceiveBusiness::class)->addReceive($data);

            // 提交事务
            $userSendModel::commit();
            Log::write(date('Y-m-d H:i:s').'_消费成功');

            return true;

        } catch (\Exception $e) {
            Log::write(date('Y-m-d H:i:s').'_消费失败_'.$e->getMessage(),'info');

            // 回滚事务
            $userSendModel::rollback();
            return false;
        }

    }
}
