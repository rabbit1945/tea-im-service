<?php


namespace app\service;
use app\service\ai\QianFan;

/**
 * ai类
 * Class aiService
 * @package app\service
 */

class aiService
{

    public function run($msg) {
        $qianFan = app()->make(QianFan::class);

       return $qianFan->getModel('chatglm2_6b_32k')->run($msg);
    }

}
