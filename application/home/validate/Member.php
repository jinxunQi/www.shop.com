<?php

namespace app\home\validate;
use think\Validate;
class Member extends Validate
{
    //验证规则
    protected $rule = [
        //表单name名称 => 规则1|规则2|规则3....
        'username' => 'require|unique:member', //表前缀不用写
        'email' => 'require|email|unique:member',
        'password' => 'require',
        'repassword' => 'require|confirm:password',
        'captcha' => 'require|captcha',
        //captcha_src()    captcha | captcha_src(2)  captcha:2
        'login_captcha' => 'require|captcha:2',

        'phone' => 'require|unique:member',

    ];
    //验证不通过的提示信息
    protected $message = [
        'username.require' => '用户名必填',
        'username.unique' => '用户名占用',
        'email.require' => '邮箱必填',
        'email.email' => '邮箱格式非法',
        'email.unique' => '邮箱占用',
        'password.require' => '密码必填',
        'repassword.confirm' => '两次密码不一致',
        'captcha.require' => '验证码必填',
        'captcha.captcha' => '验证码有误',
        'login_captcha.require' => '验证码必填',
        'login_captcha.captcha' => '验证码有误',

        'phone.require' => '手机号码必填',
        'phone.unique' => '该手机号码已被注册',
    ];
    //验证场景
    protected $scene = [
        //场景名 => [元素=>规则1|规则2..]
        //场景名 => [元素] 验证元素的所有规则
        'register' => ['username', 'email', 'password','repassword','captcha','phone'],
        //登陆不用验证用户名是否重复
        'login' => ['username'=>'require',  'password','login_captcha'],
        'sendsms' => ['phone'=>'require|unique:member'],
        'setnewpassword' => ['password','repassword']
    ];

}