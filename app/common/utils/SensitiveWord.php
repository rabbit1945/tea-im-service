<?php

namespace app\common\utils;
use app\service\JsonService;
use think\facade\Cache;
use think\facade\Config;


/**
 * 敏感词
 * Class SensitiveWord
 * @package app\common\utils
 */

class SensitiveWord
{

    private  $dict = []; //字典

    /**
     * 缓存key
     * @var string
     */
    private  $cacheKey = "imSensitiveWord";
    /**
     * @var mixed
     */
    private $sensitiveWord;


    public function __construct()
    {
        $this->sensitiveWord = Config::get('sensitiveWord');
        $this->dict = [];

    }

    /**
     * 添加敏感词
     * @param bool $cache
     * @return $this
     */
    public function addWords($cache = true)
    {

        $cacheKey = $this->cacheKey;
        if (!$cache) {
            Cache::delete($cacheKey);
        }

        $jsonService = app()->make(JsonService::class);
        if (Cache::has($cacheKey)) {
            return $this->dict = $jsonService->jsonDecode(Cache::get($cacheKey)) ;
        }

        // 获取屏蔽词 创建Trie
        $sensitiveWord = $this->sensitiveWord;
        foreach ($sensitiveWord as $val) {
            $this->createTrie(trim($val));
        }
        if ($this->dict && $cache) {
            Cache::set($cacheKey, $jsonService->jsonEncode($this->dict));
        }
        return  $this;


    }



    /**
     * 插入单词
     * @param $word  // 屏蔽词组
     * @return void
     */
    public function createTrie($word)
    {
        $wordArr = $this->splitStr($word); // 把字符串分割成数组
        $curNode = &$this->dict;
        foreach ($wordArr as $char) {
            if (!isset($curNode)) {
                $curNode[$char] = [];
            }

            $curNode = &$curNode[$char];

        }
        // 标记到达当前节点完整路径为"敏感词"

        $curNode['end'] = true;

    }

    /**
     * 分割文本(注意ascii占1个字节, unicode...)
     *
     * @param string $str
     *
     * @return string[]
     */
    protected function splitStr( string $str): array
    {
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }


    /**
     * 过滤文本
     *
     * @param string $str 原始文本
     * @param string $replace 敏感字替换字符
     * @param int $skipDistance 严格程度: 检测时允许跳过的间隔
     *
     * @return string 返回过滤后的文本
     */
    public function filter(string $str, string $replace = '*', int $skipDistance = 0): string
    {
        $maxDistance = max($skipDistance, 0) + 1;
        $strArr = $this->splitStr($str);
        $length = count($strArr);
        for ($i = 0; $i < $length; $i++) {
            $char = $strArr[$i];

            if (!isset($this->dict[$char])) {
                continue;
            }

            $curNode = &$this->dict[$char]; // 当前节点
            $dist = 0; // 跳过的次数
            $matchIndex = [$i]; // 匹配的标识
            for ($j = $i + 1; $j < $length && $dist < $maxDistance; $j++) {
                if (!isset($curNode[$strArr[$j]])) {
                    $dist ++;
                    continue;
                }

                $matchIndex[] = $j;
                $curNode = &$curNode[$strArr[$j]];
            }

            // 匹配
            if (isset($curNode['end'])) {
//                Log::Write("match ");
                foreach ($matchIndex as $index) {

                    $strArr[$index] = $replace;
                }
//                $i = max($matchIndex);
            }
        }
        return implode('', $strArr);
    }



}
