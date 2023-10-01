<?php


namespace app\service;

class JsonService
{


    public function __construct() {

    }


    /**
     * 数组转json
     * @param array $param
     * @param int $flags
     * @return false|string
     */
    public function  jsonEncode(array $param = [], int $flags = JSON_UNESCAPED_UNICODE) {

        return json_encode($param, $flags);
    }

    /**
     * json 转 数组
     * @param string $json
     * @param bool $associative // 当该参数为 TRUE 时，将返回 array 而非 object
     * @return mixed
     */
    public function  jsonDecode(string $json, bool $associative = true ) {

        return json_decode($json,$associative);
    }

}
