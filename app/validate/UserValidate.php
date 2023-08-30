<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class UserValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'nick_name' => 'require|min:2',
        'login_name' => 'require|min:6|email',
        'password'   => 'require|min:6',
        'confirm_password' => 'require|confirm:password',

    ];


    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'nick_name.require' => '昵称必填',
        'nick_name.min'     => '昵称必须超过2个字符',
        'login_name.require'=> '账号必填',
        'login_name.min'    => '账号必须超过6个字符',
        'password.require'  => '密码必填',
        'password.min'      => '密码必须超过6个字符',
        'confirm_password.require'  => '密码必填',
        'confirm_password.confirm'      => '密码不相等'
    ];

    protected $scene = [
        'edit'  =>  ['login_name','password'],
        'create' =>  ['nick_name','login_name','password','confirm_password'],
    ];

}
