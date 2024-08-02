<?php


namespace app\service;

use think\facade\Session;

class SessionService
{
    /**
     * @param $name
     * @param $value
     * @return bool
     */
    public function set($name,$value): bool
    {
        Session::set($name,$value);
        Session::save();
        if (!Session::has($name)) return False;
        return True;
    }


    /**
     * session获取
     * @param $name
     * @return false|mixed
     */
    public function get($name) {
        if (!Session::has($name)) return False;
        return Session::get($name);
    }


}
