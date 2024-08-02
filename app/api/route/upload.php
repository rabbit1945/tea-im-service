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

Route::group('upload',function () {

    //uploadAudio
    Route::post('<v>/audio',':v.upload.Upload/uploadAudio');
    //uploadFiles
    Route::post('<v>/files',':v.upload.Upload/uploadFiles');

    Route::post('<v>/base64',':v.upload.Upload/uploadBase64');

    Route::post('<v>/put',':v.upload.Upload/uploadPut');

    Route::post('<v>/checkChunkExist',':v.upload.Upload/checkChunkExist');

    Route::post('<v>/chunk',':v.upload.Upload/chunk');

    Route::post('<v>/merge',':v.upload.Upload/merge');
    // 生成缩略图
    Route::post('<v>/create/thumb',':v.upload.Upload/createThumb');

})->option(['mark' => 'upload','mark_name' => "上传模块"]);




