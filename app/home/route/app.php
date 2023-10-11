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

// 注册
Route::post(':v/register',':v.Login/register');
// 登录
Route::post(':v/login',':v.Login/login');
// gitee 登录 返回gitee code url
Route::get(':v/auth/gitee',':v.Login/auth');
// gittee 登录
Route::post(':v/gitee/login',':v.Login/thirdPartyLogin');
// 第三方登录回调
Route::get(':v/login/callback',':v.Login/callback');

// 聊天室信息
Route::post(':v/room/info',':v.Room/roomInfo');
// 聊天室的用户列表
Route::post(':v/room/user/list',':v.Room/roomUserList');

// 添加登录日志
Route::post(':v/add/login/logs',':v.User/userLoginLogs');
//logOut
Route::post(':v/log/out',':v.User/logOut');
// 用户离线消息
Route::post(':v/user/offline/msg',':v.Message/getOffLineMessageList');
// 用户消息
Route::post(':v/user/msg',':v.Message/getMessageList');
//uploadAudio
Route::post(':v/upload/audio',':v.Message/uploadAudio');




