<?php
/**
 * +-------------------------------------------
 * |
 * +-------------------------------------------
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/10/20
 * Time: 16:41
 * +-------------------------------------------
 */


return [

    'key' => 'jidlaksdi1jj23k4./;odw', //自定义key的值
    'iat' => time(),   // 签发时间
    'nbf' => time(),   // 生效时间
    'exp' => time()+86400*64 , // 过期时间
    'rsa_private_key' => base_path()."common/cret/rsa_private_key.pem",
    'rsa_public_key'  => base_path()."common/cret/rsa_public_key.pem",
];
