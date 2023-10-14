<?php


namespace app\service\login;

class Login
{


    public string $className;

    public function __construct($className)
    {
        $className = ucfirst($className);
        $this->className =  "app\\service\\login\\$className";

    }

    public function getUserInfo()
    {
        if ($this->className) {

            return  app()->make($this->className);
        }
        return False;
    }

}
