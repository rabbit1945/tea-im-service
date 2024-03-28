<?php

namespace app\service\rabbitMq;
use app\api\business\MessageBusiness;
use app\api\business\MessageReceiveBusiness;
use app\api\business\MessageSendBusiness;
use app\model\UserSendModel;
use app\service\JsonService;
use Illuminate\Contracts\Container\BindingResolutionException as BindingResolutionExceptionAlias;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPOutOfBoundsException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use think\facade\Log;
use PhpAmqpLib\Wire\AMQPTable;
use function PHPUnit\Framework\assertNotTrue;

/**
 * 消费者
 */
class ReceiveMq extends RabbitMqService
{

    protected int $retryLimit = 3;// 设置最大重试次数
    protected int $retryCount = 0; // 当前重试次数

    public string $queueName;
    public string $exchangeName;
    public string $routeKey;

    public function __construct($exchangeName,$queueName,$routeKey)
    {
        parent::__construct();
        $this->queueName = $queueName;
        $this->exchangeName = $exchangeName;
        $this->routeKey = $routeKey;



    }

    /**
     * 消费
     * @throws AMQPConnectionClosedException
     * @throws AMQPOutOfBoundsException
     * @throws AMQPRuntimeException
     * @throws AMQPTimeoutException
     */
    public function consumer()
    {
        Log::write(date('Y-m-d H:i:s').'_消费者开始消费queueName'.$this->queueName,'info');
        Log::write(date('Y-m-d H:i:s').'_消费者开始消费exchangeName'.$this->exchangeName,'info');
        Log::write(date('Y-m-d H:i:s').'_消费者开始消费routeKey'.$this->routeKey,'info');
        try {
            $callback = function ($msg)  {
                Log::write(date('Y-m-d H:i:s') . '_消费者详情：'.$msg->body,'info');
                try {
                    $jsonService = app()->make(JsonService::class);
                    $data = $jsonService->jsonDecode($msg->body);
                    Log::write(date('Y-m-d H:i:s') . '_消费者成功_'.$jsonService->jsonEncode($data),'info');
                    if (!$this->receiveMsg($data)) {
                        Log::write(date('Y-m-d H:i:s') . '_消费者失败_','info');
                        throw new \Exception("消费者失败");

                    };
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                } catch (\Exception $e) {
                    // 获取重试次数
                    $this->retryCount++;
                    // 处理失败，增加重试计数
                    Log::write(date('Y-m-d H:i:s') . '_消费者失败重试次数'.$this->retryCount.'次'.$e->getMessage(),'info');
                    if ($this->retryCount <= $this->retryLimit) {
                        // 具体的消费逻辑

                        // 还未达到重试上限，重新发布到原队列（假设直接发送回原队列进行重试）
                        $msg->delivery_info['channel']->basic_nack(
                            $msg->delivery_info['delivery_tag'],
                            false, // 多条消息一起拒绝，这里通常设为false，仅针对当前消息
                            true // 是否重新排队（true表示将消息重新放入队列头部）
                        );

                    } else {
                        // 达到重试上限，可以考虑将消息路由到死信队列或者其他处理流程
                        // ... 发送到死信队列或其他逻辑
                        $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], false); // 不重新排队，丢弃消息或做其他处理
                        $this->retryCount = 0;
                        Log::write(date('Y-m-d H:i:s') . '_消费者失败重试次数超过'.$this->retryLimit.'次，放弃重试'.$e->getMessage(),'info');
                    }

                }
            };
            // 消费
            $this->channel->basic_consume($this->queueName, '', false, false, false, false, $callback);
            while ($this->channel->is_consuming()) {
                $this->channel->wait();
            }
        } catch (\Exception $e) {
            Log::write(date('Y-m-d H:i:s') . '_消费者失败_'.$e->getMessage(),'info');
            return false;

        }
    }

    /**
     * @param $data
     * @return bool
     * @throws BindingResolutionExceptionAlias
     */
    public function receiveMsg($data): bool
    {

        // 启动事务
        $userSendModel = app()->make(UserSendModel::class);
        $userSendModel::startTrans();
        try {
            //   消息列表
            app()->make(MessageBusiness::class)->addMessage($data);
            // 发送消息列表
            app()->make(MessageSendBusiness::class)->addSend($data);
            // 接收消息列表
            app()->make(MessageReceiveBusiness::class)->addReceive($data);

            // 提交事务
            $userSendModel::commit();
            Log::write(date('Y-m-d H:i:s') . '_rabbitmq消费成功:','info');

            return true;
        } catch (Exception $e) {
            Log::write(date('Y-m-d H:i:s') . '_rabbitmq消费失败_' . $e->getMessage(), 'info');
            Log::write(date('Y-m-d H:i:s') . '_rabbitmq消费失败info_' , 'info');
            // 回滚事务
            $userSendModel::rollback();
            return false;
        }

    }
}
