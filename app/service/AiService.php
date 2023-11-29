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

    /**
     * @param $msg
     * @return bool|string
     * @throws \Exception
     */
    public function run($msg): bool|string
    {
        $qianFan = app()->make(QianFan::class);
        return $qianFan->getModel('chatglm2_6b_32k')->run($msg);
    }

}
