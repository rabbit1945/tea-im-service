<?php


namespace app\service;
use app\service\ai\QianFan;

/**
 * ai类
 * Class AiService
 * @package app\service
 */

class AiService
{

    public function run($msg) {
        $qianFan = app()->make(QianFan::class);

       return $qianFan->getModel('chatglm2_6b_32k')->run($msg);
    }

}
