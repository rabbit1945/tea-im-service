<?php


namespace app\common\utils;
use app\service\RedisService;
use Exception;
use Godruoyi\Snowflake\RedisSequenceResolver;
use Godruoyi\Snowflake\Sonyflake;
use think\facade\App;
use think\facade\Cache;

/**
 * 生成id
 * Class IdRedisGenerator
 * @package app\common\utils
 */
class IdRedisGenerator
{

    public $sonyflake;
    public $sequence;
    public function __construct(Sonyflake $snowflake)
    {
        $this->sonyflake = $snowflake;

    }





    /**
     * 生成器
     * @param  $datacenterId
     * @param  $time
     * @throws Exception
     */
    public function generator( $datacenterId, $time): Sonyflake
    {
        $snowflake = new sonyflake ($datacenterId);
        $snowflake->setStartTimeStamp($time);

        $snowflake->setSequenceResolver(
            new RedisSequenceResolver(App::make(RedisService::class)->handler())
        );

        $this->sequence = $snowflake->id();
        $this->clockDiff();
        return $snowflake;

    }

    /**
     * 获取序列号
     * @return mixed
     */
    public function getSequence()
    {

        return $this->sequence;
    }


    /**
     * 时钟回拨检测
     * @throws Exception
     */
    public function clockDiff(): bool
    {
        $oldId = Cache::get("get_id");

        $newId = $this->sequence ?? 0;
        if ($oldId) {
            if ($newId <= $oldId )   throw new Exception('当前ID小于等于当前最大的ID。');

        }

        Cache::set("get_id",$this->sequence);

        return Cache::has("get_id");
    }

}

