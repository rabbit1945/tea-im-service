<?php

namespace app\api\business;

use app\service\JsonService;
use app\service\RedisService;
use think\App;

class Business
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app     = $app;

    }

    /**
     * 获取缓存列表
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|false
     */
    public function getMsgCacheList(string $key, int $start = 0, int $end = -1): array|false
    {
        $redis = $this->app->make(RedisService::class);
        $key = $redis->getPrefix().$key;
        if ($redis->exists($key)) {
            $json = $this->app->make(JsonService::class);
            $list = [];
            $cacheList = $redis->zrevrange($key, $start, $end);
            if (!empty($cacheList)) {
                foreach ($cacheList as $key => $val) {
                    $val = $json->jsonDecode($val);
                    $info = $this->getMsg($val);
                    $list[$key] = $info;
                }
            }
            return $list;
        }

        return false;

    }

}