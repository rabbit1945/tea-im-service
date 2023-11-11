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

use Exception;
use think\facade\Lang;
use think\facade\Request;
use think\Response;

class  ImJson
{


    /**
     * @param $code // code
     * @param string $msg // 说明
     * @param  $data // 数据
     * @param array $vars // 动态变量
     * @param int $httpCode
     */

    public static function output(int $code = 10000, string $msg = '', ?array $data = [], array $vars = [], int $httpCode = 200,$type = 'json' ): Response
    {
        $method = Request::method();
        if (in_array($method,["POST","PUT","PATCH"])) {
            $httpCode = 201;
        } elseif ($method == "DELETE") {
            $httpCode = 204;
        }
        $getData = self::outData($code,$msg,$data,$vars);


        return Response::create($getData,$type,$httpCode);
    }


    public static function outData(int $code = 10000, string $msg = '', ?array $data = [], array $vars = [] ): array
    {

        try {
            if (empty($msg)) {
                //获取配置文件
                $msg = Lang::get($code, $vars);
            }
            // 1.调用整个数组
            $getData = [
                'code' => $code,
                'msg' => $msg,
                'data' => $data,
            ];
        } catch (Exception $e) {
            $getData = [
                'code' => '20001',
                'msg' => $e->getMessage(),
                'data' => [],
            ];
        }

        return $getData;

    }


}
