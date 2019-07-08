<?php
namespace app\admin\validate;
use think\Validate;
class User extends Validate{
    //验证规则
    protected $rule = [
        //表单name名称 => 规则1|规则2|规则3....
        'username' => 'require|unique:user', //表前缀不用写
        'password' => 'require|min:5',
        'repassword' => 'require|confirm:password'

    ];
    //验证不通过的提示信息
    protected $message = [
        'username.require' => '用户名必填',
        'username.unique' => '用户名重复',
        'password.require' => '密码必填',
        'password.min' => '密码最少5位',
        'repassword.require' => '重复密码必填',
        'repassword.confirm' => '两次密码不一致',
    ];
    //验证场景
    protected $scene = [
        //场景名 => [元素=>规则1|规则2..]
        //场景名 => [元素] 验证元素的所有规则
        'add' => ['username','password','repassword'],
        'onlyUsername' => ['username'],
        'UsernamePassword' => ['username','password','repassword'],
        'login' => ['username'=>'require','password'],
    ];

}