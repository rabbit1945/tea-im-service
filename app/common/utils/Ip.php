<?php


namespace app\common\utils;

use Exception;

/**
 * 获取IP地址工具
 * Class Ip
 * @package app\common\utils
 */
class Ip
{
    /**
     * 获取IP详情
     */

    public static function getIP(): bool|array|string
    {

        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realIp = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realIp = getenv("HTTP_CLIENT_IP");
        } else {
            $realIp = getenv("REMOTE_ADDR");
        }
        return $realIp;
    }

    /**
     * 获取IP的详细信息
     * @param $ip
     * @return bool|string
     */

    public static function getIpInfo($ip) {
        try {

            if ($ip) {
                $info = Curl::send("https://opendata.baidu.com/api.php?query=$ip&co=&resource_id=6006&oe=utf8");

                $info = json_decode($info, true);

                if ($info['status'] === "0") {
                    return $info['data'];

                }
            }
            return false;
        } catch (Exception $ex) {
            return false;

        }


    }

}
