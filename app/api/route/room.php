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

Route::group('room',function () {
    // 聊天室聊表
    Route::get("<v>/room/index",':v.room.Room/index');
    // 聊天室信息
    Route::get("<v>/room/info/<id>",':v.room.Room/show');
    // 聊天室的用户列表
    Route::get('<v>/room/user/list/<room_id>/<pages?>/<size?>/<nickName?>',':v.room.Room/roomUserList');

})->option(['mark' => 'room','mark_name' => "房间模块"]);




