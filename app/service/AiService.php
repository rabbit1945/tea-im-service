<?php


namespace app\service;
use app\service\ai\QianFan;
use think\facade\Log;

/**
 * ai类
 * Class AiService
 * @package app\service
 */

class AiService
{

    public function run($msg) {
        $qianFan = app()->make(QianFan::class);
        $data = $qianFan->getModel('chatglm2_6b_32k')->run($msg);
        Log::write(date('Y-m-d H:i:s').'_chatglm2_6b_32k机器人_'.json_encode($data),'info');
        return $data;
    }

}
