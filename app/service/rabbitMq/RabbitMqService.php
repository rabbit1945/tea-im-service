<?php

namespace app\service\rabbitMq;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPOutOfBoundsException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use think\facade\Log;
use PhpAmqpLib\Connection\AMQPConnection;

/**
 * rabbitmq服务
 */
class RabbitMqService
{
    public AMQPStreamConnection $connection; // 连接
    public string $port = '5672'; // 端口
    public string $host; // 主机
    public string $username; // 用户名
    public string $password; // 密码
    public string $vhost; // 虚拟主机
    public mixed $channel; // 通道


    /**
     * @throws AMQPConnectionClosedException
     * @throws AMQPOutOfBoundsException
     * @throws AMQPRuntimeException
     * @throws AMQPTimeoutException
     */
    public function __construct() {

       $this->connection =  new AMQPStreamConnection(
           config('rabbitmq.host'),
           config('rabbitmq.port'),
           config('rabbitmq.username'),
           config('rabbitmq.password'),
           config('rabbitmq.vhost')
       );

       //创建通道
       $this->channel = $this->connection->channel();
   }

    /**
     * 获取通道
     * @return AMQPChannel
     */
    public function getChannel():AMQPChannel
    {
        return $this->channel;
    }

    /**
     * 设置主机域名ip
     * @param string $host
     * @return string
     */
    public function setHost(string $host ):string
    {
       $this->host = $host ?? config('rabbitmq.host');

    }


    /**
     * 获取主机域名ip
     * @return string
     */
   public function getHost(): string
   {
       return $this->host;
   }

    /**
     * 设置端口
     * @param string $port
     * @return string
     */
    public function setPort(string $port = '5672'): string
    {
       $this->port = $port ?? config('rabbitmq.port');

    }

    /**
     * 获取端口号
     * @return string
     */
   public function getPort(): string
   {
       return $this->port;
   }


    /**
     * 设置用户名称
     * @param string $username
     * @return string
     */
   public function setUserName(string $username = ""):string
   {
       $this->username = $username ?? config('rabbitmq.username');
   }

    /**
     * 获取用户名称
     * @return string
     */
   public function getUserName():string
   {
       return $this->username;
   }

    /**
     * 设置密码
     * @param string $password
     * @return string
     */
   public function setPassword(string $password = ""):string
   {
       $this->password = $password ?? config('rabbitmq.password');
   }

   /**
    * 获取密码
    * @return string
    */
   public function getPassword():string
   {
       return $this->password;
   }

    /**
     * 设置虚拟主机
     * @param string $vhost
     * @return string
     */
   public function setVhost(string $vhost = ""):string
   {
       $this->vhost = $vhost ?? config('rabbitmq.vhost');
   }

  /**
   * 获取虚拟主机
   * @return string
   */
   public function getVhost():string
   {
       return $this->vhost;
   }


}