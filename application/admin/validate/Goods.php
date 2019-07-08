<?php


namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{
    //验证规则
    protected $rule = [
        //表单name名称 => 规则1|规则2|规则3....
        'goods_name' => 'require|unique:goods', //表前缀不用写
        //正则验证,前面必须先加require规则,正则已经约束了开头^和结尾$
        'goods_price' => 'require|regex:\d+',  //使用gt:0 有bug
        //正则验证,前面必须先加require规则,正则已经约束了开头^和结尾$
        'goods_number' => 'require|regex:\d+', //相当于 ^\d+$
        'cat_id' => 'require',

    ];
    //验证不通过的提示信息
    protected $message = [
        'goods_name.require' => '商品名称必填',
        'goods_name.unique' => '商品名称重复',
        'goods_price.require' => '商品价格必填',
        'goods_price.regex' => '商品价格必须大于0',
        'goods_number.require' => '商品库存必填',
        'goods_number.regex' => '商品库存必须大于0',
        'cat_id.require' => '商品分类必填',
    ];
    //验证场景
    protected $scene = [
        //场景名 => [元素=>规则1|规则2..]
        //场景名 => [元素] 验证元素的所有规则
        'add' => ['goods_name', 'goods_price','goods_number','cat_id'],
    ];

}