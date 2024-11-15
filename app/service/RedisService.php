<?php
namespace app\service;
use think\App;
use think\facade\Cache;

/**
 * redis 服务
 * Class Redis
 * @package app\service
 */
class RedisService
{
    /**
     * @var string
     */
    protected string $prefix = 'teaIm:';

    public function __construct() {

    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }



    /**
     * 通过句柄，获取高级方法
     * @return object|null
     */
    public function handler(): ?object
    {
        return Cache::store('redis')->handler();
    }

    public function set($key,$val)
    {
        return $this->handler()->set($key,$val);
    }

    /**
     * 返回回有序集中，指定区间内的成员
     * @param string $key           // 键名
     * @param int $start         // 开始区间 0
     * @param int $stop         // 结束区间 -1
     * @param string $cores // 分数
     * @return mixed        // 指定区间内，带有分数值(可选)的有序集成员的列表。
     */

    public function zrange(string $key, int $start, int $stop, string $cores = ""): mixed
    {
       return $this->handler()->ZRANGE($key,$start,$stop,$cores);
    }

    /**
     * 将一个或多个成员元素及其分数值加入到有序集当中。
     * 被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员
     * @param string $key  // 键名
     * @param int $cores   // 分数
     * @param string $data  // 数据
     * @return mixed
     */
    public function zadd(string $key,int $cores,string $data): mixed
    {
        return $this->handler()->ZADD($key,$cores,$data);
    }

    /**
     * 检测key是否存在
     * @param $key
     * @return mixed
     */
    public function exists($key): mixed
    {
        return $this->handler()->EXISTS($key);
    }


    /**
     * 获取key
     * @param string $key
     * @param string $table
     * @return string
     */
    public function getKey(string $key, string $table): string
    {
        return "{$this->prefix}{$table}:{$key}";
    }

    public function __call($method, $parameters)
    {
        return $this->handler()->$method(...$parameters);
    }
}
