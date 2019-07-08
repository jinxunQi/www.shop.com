<?php


namespace app\home\validate;
use think\Validate;

class Order extends Validate
{
    //验证规则
    protected $rule = [
        //表单name名称 => 规则1|规则2|规则3....
        'receiver' => 'require', //表前缀不用写
        'address' => 'require',
        'phone' => 'require',
        'zcode' => 'require',
    ];
    //验证不通过的提示信息
    protected $message = [
        'receiver.require' => '收件人必填',
        'address.require' => '收件地址必填',
        'phone.require' => '收件人联系方式必填',
        'zcode.require' => '邮政编码必填',
    ];
    //验证场景
    protected $scene = [
        //场景名 => [元素=>规则1|规则2..]
        //场景名 => [元素] 验证元素的所有规则
        'pay'=>['receiver','address','phone','zcode']
    ];

}