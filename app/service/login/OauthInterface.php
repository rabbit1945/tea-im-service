<?php

namespace app\service\login;

use think\facade\Config;

interface OauthInterface
{

    /**
     *   授权返回URL 获取code
     * @return string
     */
    public function authorization():string ;

    /**
     * 获取token
     * @param $code  // code
     * @return mixed
     */
    public function getAccessToken($code): mixed;


    /**
     * 获取用户详情
     * @param $access_token
     * @return mixed
     */
    public function getUserInfo($access_token): mixed;



}