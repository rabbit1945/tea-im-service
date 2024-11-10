<?php


namespace app\service;
use app\service\ai\QianFan;
use Exception;
use think\App;
use think\facade\Log;

/**
 * ai类
 * Class AiService
 * @package app\service
 */

class AiService
{

    public array $models = [
        'app\service\ai\QianFan',
        'app\service\ai\ZhiPu'
    ];

    /**
     * 设置模型
     * @param mixed $model

     */
    public function setModel( string $model)
    {
        return $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getModel(): mixed
    {
        return $this->model;
    }

    private App $app;
    public mixed $model;

    public function __construct(App $app)
    {
        $this->app = $app;

    }

    /**
     * @param $msg
     * @param string $user_id
     * @return bool|string|array
     */
    public function run($msg, string $user_id = ""): bool|string|array
    {
        return $this->app->make($this->getModel())->run($msg,$user_id);
    }

    /**
     * 随机发送
     * @throws Exception
     */
    public function randomSend($msg,$user_id): bool|array|string
    {
        $this->randomModel();
        return $this->run($msg,$user_id);
    }

    /**
     * 随机model
     * @return void
     */
    public function randomModel(): void
    {
        $model = $this->models[array_rand($this->models)];
        $this->setModel($model);
    }

}
