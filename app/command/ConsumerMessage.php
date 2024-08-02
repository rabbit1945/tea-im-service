<?php
declare (strict_types = 1);

namespace app\command;

use app\service\rabbitMq\ReceiveMq;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPOutOfBoundsException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class ConsumerMessage extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('consumerMessage')
            ->setDescription('the consumerMessage command');
    }

    /**
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $receiveMq = new ReceiveMq('messageExchange','MessageJobQueue','message.info');
        try {
            $receiveMq->consumer();
        } catch (AMQPConnectionClosedException|AMQPOutOfBoundsException|AMQPRuntimeException|AMQPTimeoutException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
        // 指令输出
        $output->writeln('consumerMessage');

    }
}
