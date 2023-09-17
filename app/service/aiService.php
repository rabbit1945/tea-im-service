<?php


namespace app\service;
/**
 * ai类
 * Class aiService
 * @package app\service
 */

class aiService
{
    public function run() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/chatglm2_6b_32k?access_token=24.c91560076ff03741a0a547964ee16fb5.2592000.1697521087.282335-39493501",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_CUSTOMREQUEST => 'POST',

            CURLOPT_POSTFIELDS =>'{"messages":[{"role":"user","content":"你好"}]}',

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),

        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }





}
