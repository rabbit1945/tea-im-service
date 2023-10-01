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
use think\Response;
class  ImJson
{


    /**
     * @param string $code // code
     * @param string $msg // 说明
     * @param  $data // 数据
     * @param array $vars // 动态变量
     * @param int $httpCode
     */

    public static function output(string $code = '10000', string $msg = '', ?array $data = [], array $vars = [], int $httpCode = 200): Response
    {
        try {
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
        } catch (\Exception $e) {
            $getData = [
                'code' =>  '20001',
                'msg'  => $e->getMessage(),
                'data' =>  [],
            ];
        }

        return Response::create($getData,'json',$httpCode);
    }
}
