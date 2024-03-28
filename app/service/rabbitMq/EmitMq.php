<?php

namespace app\service\rabbitMq;
use app\service\JsonService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery\Exception;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPConnectionBlockedException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use think\facade\Log;

/**
 * 消息队列发送
 */
class EmitMq extends RabbitMqService
{


    public function __construct()
    {

        parent::__construct();



    }

    /**
     * 发送消息
     * @param $data // 消息
     * @param $exchangeName
     * @param $queueName // 队列名称
     * @param string $routeKey
     * @return bool
     * @throws BindingResolutionException
     * @throws AMQPChannelClosedException
     * @throws AMQPConnectionBlockedException
     * @throws AMQPConnectionClosedException
     * @throws AMQPTimeoutException
     */

    public function sendMsg($data, $exchangeName,$queueName,string $routeKey): bool
    {

        try {
            $dead_letter_exchange = 'dlx.message.exchange';
            $dlq_routing_message = 'dlq.routing.message';
            $ch = $this->getChannel();
            /**
             *  $exchange: （字符串）要声明的交换机名称。
             *   $type: （字符串）交换机类型，常见的类型有：
             *      'direct'：直接交换机，根据路由键严格匹配。
             *      'topic'：主题交换机，支持模糊匹配。
             *      'fanout'：扇出交换机，将消息广播到所有绑定到它的队列。
             *      'headers'：基于消息头的匹配。
             *   $passive: （布尔值）如果设置为 true，RabbitMQ 将不会创建交换机，而是检查交换机是否已经存在。如果交换机不存在，则会返回一个错误。
             *   $durable: （布尔值）如果设置为 true，交换机会被持久化保存到磁盘，在 RabbitMQ 重启后仍然存在。如果设置为 false，则交换机仅存在于内存中，重启后会消失。
             *   $auto_delete: （布尔值）如果设置为 true，当最后一个与该交换机解除绑定的队列被删除后，交换机会自动被删除。如果设置为 false，交换机在没有绑定的情况下也会继续存在。
             *   $arguments: （数组）可选参数，用于传递给 RabbitMQ 的扩展属性，例如在高级场景下使用的额外配置项。
             */
            $args = [
                'x-dead-letter-exchange' => $dead_letter_exchange,
                // 可选：指定死信转发时使用的路由键
                'x-dead-letter-routing-key' => $dlq_routing_message
            ];
            // 声明交换机
            $ch->exchange_declare($exchangeName, 'topic',  false, true, false);
            // 声明死信交换机
            $this->channel->exchange_declare($dead_letter_exchange, 'direct', false, true, false);
            // 声明死信队列
            $this->channel->queue_declare('dead_message_letter_queue', false, true, false, false);
            // 将死信队列绑定到死信交换机
            $this->channel->queue_bind('dead_message_letter_queue', $dead_letter_exchange, $dlq_routing_message);
           // 声明队列
            /**
             * $queue：（字符串）队列名称。如果留空（默认为空字符串），RabbitMQ 将会生成一个唯一的队列名称并返回。
             * $passive：（布尔值）如果设置为 true，RabbitMQ 仅会检查队列是否存在，而不尝试创建它。如果队列不存在，将会抛出一个异常。
             * $durable：（布尔值）如果设置为 true，队列将会被持久化，即在 RabbitMQ 重启后队列仍然存在。如果设置为 false，则队列只存在于内存中，重启后会被清除。
             * $exclusive：（布尔值）如果设置为 true，队列将是排他的（Exclusive Queue），这意味着只有当前连接的应用程序可以访问它，并且在该连接关闭时队列将被自动删除。这种队列常用于临时性的、单个消费者的应用场景。
             * $auto_delete：（布尔值）如果设置为 true，队列将在没有任何消费者订阅的情况下自动删除（即最后一个消费者取消订阅之后）。
             * $arguments：（数组）可选参数，用于传递给 RabbitMQ 的扩展属性，例如用于设置死信交换机（Dead Letter Exchange）或其他高级队列特性。
             */
            $ch->queue_declare($queueName, false, true, false, false, false, new AMQPTable($args));
            // 绑定队列
            $ch->queue_bind($queueName, $exchangeName,$routeKey);

            $data['msg_content'] =  urlencode($data['msg']);
            $data['send_time']   = $data['send_timestamp'];
            $data['contact']     = $data['contactList']??"";
            $data['msg_form']    = $data['user_id'];

            $jsonService = app()->make(JsonService::class);
            $data = $jsonService->jsonEncode($data);
            // 发送消息  delivery_mode 属性设置为 2, 表示消息持久化
            $msg = new AMQPMessage($data, [
                    'delivery_mode' => 2,             // 设置消息持久化
            ]);
            $msg->set('application_headers', new AMQPTable(['x-delay' => 5000, 'x-retry-max-count' => 3]));

            $ch->basic_publish($msg, $exchangeName, $routeKey);


            Log::write(date('Y-m-d H:i:s') . '_发送成功_'.$data,'info');
            return true;
        } catch (Exception $e) {
            Log::write(date('Y-m-d H:i:s') . '_发送失败_'.$e->getMessage(),'info');
            return false;
        }
    }


}