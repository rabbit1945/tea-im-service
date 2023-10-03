<?php


namespace app\service\login;


class Login
{

    public function __construct()
    {

    }

    public function getUserInfo($className)
    {
        $className = ucfirst($className);
        if ($className and class_exists($className)) {
            return app()->make("{$className}::class");
        }
        return False;
    }

}
