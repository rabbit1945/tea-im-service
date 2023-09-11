<?php
/**
 * +-------------------------------------------
 * | 生成token类
 * +-------------------------------------------
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/07/20
 * Time: 19:57
 * +-------------------------------------------
 */

namespace app\common\utils;

use Firebase\JWT\JWT;
use think\facade\Config;

/**
 * jwt 工具
 * Class JwToken
 * @package app\common\utils
 */
class JwToken extends JWT
{

    /**
     * 获取登录凭证
     * @param  $user_id
     * @return string
     */
    public static function getAccessToken($user_id,$exp = "" ): string
    {


        $privateKey = Config::get('jwt.rsa_private_key');
        $payload = array(
            "user_id" => $user_id,
            "key"     => Config::get('jwt.key'),
            "iat"     => Config::get('jwt.iat'),//签发时间
            "nbf"     => Config::get('jwt.nbf'),//在什么时候jwt开始生效  （这里表示生成10秒后才生效）
            "exp"     => $exp ??Config::get('jwt.exp') //token 过期时间
        );

        return static::creatTokenEncode($privateKey, $payload);

    }

    /**
     * 加密 获取token
     * @param string $privateKeyAddr
     * @param array $payload
     * @return string
     */

    public static function creatTokenEncode(string $privateKeyAddr = "", array $payload = []): string
    {

        $privateKey = static::getKey($privateKeyAddr);

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    /**
     * 验证token
     * @param $sign
     * @return array
     */
    public static function verifyToken($sign): array
    {
        $rsaPublicKey = Config::get('jwt.rsa_public_key');
        return static ::decryptTokenDecode($rsaPublicKey, $sign);
    }


    /**
     * 解密
     * @param string $publicKeyAddr
     * @param string $sign
     * @return array
     */

    public static function decryptTokenDecode(string $publicKeyAddr,string $sign): array
    {


        $publicKey = static::getKey($publicKeyAddr);

        $decoded = JWT::decode($sign, $publicKey, array('RS256'));

        /*
         NOTE: This will now be an object instead of an associative array. To get
         an associative array, you will need to cast it as such:
        */

        return (array) $decoded;
    }

    /**
     * 获取秘钥
     * @param $key
     * @return false|string
     */
    public static function getKey($key)
    {

        try {
            $privateKey = file_get_contents($key);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $privateKey;
    }






}
