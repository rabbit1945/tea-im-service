<?php


namespace app\common\utils;


class Ip
{
    /**
     * 获取IP详情
     */
    public static function getIp() {
        try {
            return Curl::send('http://ip.42.pl/raw');
        } catch (\Exception $ex) {
            return false;

        }

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

                $info = json_decode($info,true);

                if ($info['status'] === "0") {
                    return $info['data'];

                }
            }
            return  false;
        } catch (\Exception $ex) {
            return false;

        }


    }

}
