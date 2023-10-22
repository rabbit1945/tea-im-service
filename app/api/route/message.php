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

Route::group('message',function () {
    // 用户离线消息
//    Route::get(':v/user/offline/msg',':v.message.Message/getOffLineMessageList');
    // 用户消息
    Route::get('<v>/user/msg/<room_id>/<page?>/<size?>',':v.message.Message/getMessageList');
})->option(['mark' => 'message','mark_name' => "消息模块"]);




