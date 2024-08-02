<?php


namespace app\service\ai;

/**
 * Interface AiChat
 * @package app\service\ai
 */
interface AiChat
{
    /**
     * 接口运行
     *
     */
    public function run($messages);

    /**
     * 获取凭证
     *
     */
    public function getAccessToken();

}
