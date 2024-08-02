<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::group('user',function () {
    // 注册
    Route::post(':v/register',':v.user.Login/register');
    // 登录
    Route::post(':v/login',':v.user.Login/login');
    // gitee 登录 返回gitee code url
    Route::get(':v/get/auth',':v.user.Login/getAuth');
    //  登录
    Route::post(':v/auth/login',':v.user.Login/authLogin');
    // 第三方登录回调
    Route::get(':v/login/callback',':v.user.Login/callback');
    // 添加登录日志
    Route::post(':v/add/login/logs',':v.user.User/userLoginLogs');
    //logOut
    Route::post(':v/out',':v.user.User/loginOut');

})->option(['mark' => 'user','mark_name' => "用户模块"]);




