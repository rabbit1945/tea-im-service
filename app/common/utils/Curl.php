<?php


namespace app\common\utils;
/*
 * 使用curl扩展发出http的get或post请求
 */
class Curl
{
    /**
     * @param string $url
     * @param array $data
     * @return bool|string
     */
    public static function send(string $url = "" , array $data = [])
    {
        //1\. 如果传递数据了，说明向服务器提交数据(post)，如果没有传递数据，认为从服务器读取资源(get)
        $ch = curl_init();

        //2\. 不管是get、post，跳过证书的验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //3\. 设置请求的服务器地址
        curl_setopt($ch, CURLOPT_URL,$url);

        //4\. 判断是get还是post
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //说明返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取页面内容，不直接输出到页面
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
