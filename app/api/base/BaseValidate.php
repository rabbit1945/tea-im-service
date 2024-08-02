<?php


namespace app\api\base;


use think\facade\Validate;


class BaseValidate extends Validate
{

    public function goCheck($data, $scene = '')
    {

        if (!$this->scene($scene)->check($data)) {
            return $this->getError();
        }

        return true;
    }

}
