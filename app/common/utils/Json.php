<?php
/**
 * +-------------------------------------------
 * | api json
 * +-------------------------------------------
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/07/5
 * Time: 4:00
 * +-------------------------------------------
 */

namespace app\common\utils;

use think\facade\Lang;
use function header;

/**
 * 自定义json工具
 * Class Json
 * @package app\common\utils
 */
class Json
{
    /**
     * @param array $data  // 数据
     * @param string $code // code
     * @param string $msg // 说明
     * @param array $vars  // 动态变量
     */

    public static function json(string $code = '10000',  string $msg = '',  array $data = [], array $vars = []): void
    {
        if (empty($msg)) {
            //获取配置文件
            $msg = Lang::get($code, $vars);
        }

        // 1.调用整个数组

        $getData = [
            'code' =>  $code,
            'msg'  => $msg,
            'data' =>  $data,
        ];

        header('Content-Type:application/json; charset=utf-8', false, 200);
        exit(json_encode($getData, JSON_UNESCAPED_UNICODE));


    }
}
