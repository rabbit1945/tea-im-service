<?php


namespace app\service\login;
use app\service\login\Gitee;

class Login
{

    public function __construct()
    {

    }

    public function getUserInfo($className)
    {
        $className = ucfirst($className);

        if ($className) {

            return app()->make(Gitee::class);
        }
        return False;
    }

}
