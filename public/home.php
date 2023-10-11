<?php
/**
 * +-------------------------------------------
 * |
 * +-------------------------------------------
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/19
 * Time: 20:34
 * +-------------------------------------------
 */

// [ 应用入口文件 ]
namespace think;


require __DIR__ . '/../vendor/autoload.php';
//header("Access-Control-Allow-Origin:*");
//header('Access-Control-Allow-Methods:*');
//header('Access-Control-Allow-Headers:x-requested-with, content-type,token');
// 执行HTTP应用并响应
$http = (new  App())->http;

$response = $http->name('home')->run();

$response->send();

$http->end($response);

