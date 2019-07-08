<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/


use think\Route;

//测试路由
Route::get('test','home/public/test');

################################################################################################
//前台首页路由
Route::get('/','home/index/index');
################################################################################################
//后台首页路由
Route::get('houtai','admin/index/index');

################################################################################################
//前台home分组路由
Route::group('home', function () {
    //订单付款的路由
    Route::any('order/paymoney','home/order/paymoney');
    //展示个人订单状态的路由
    Route::any('order/selforder','home/order/selforder');
    //支付宝支付成功的路由
    Route::any('order/orderDone','home/order/orderDone');
    //支付宝异步通知post方式的路由
    Route::any('order/notifyUrl','home/order/notifyUrl');
    //支付宝同步通知get方式的路由
    Route::any('order/returnUrl','home/order/returnUrl');
    //订单入库进行付款的路由
    Route::any('order/orderpay','home/order/orderpay');
     //展示订单阶段页面的路由
    Route::any('cart/orderaccount','home/cart/orderaccount');
    //更新购物车商品数量的路由
    Route::any('cart/updateCartGood','home/cart/updateCartGood');
    //清空购物车商品的路由
    Route::any('cart/clearcartgoods','home/cart/clearcartgoods');
    //删除购物车商品的路由
    Route::any('cart/delcartgood','home/cart/delcartgood');
    //购物车列表页的路由
    Route::any('cart/cartlist','home/cart/cartlist');
    //加入商品到购物车的路由
    Route::any('cart/addgoodstocart','home/cart/addgoodstocart');
    //商品详情页的路由
    Route::any('goods/detail','home/goods/detail');
    //前台导航栏分类路由
    Route::any('category/index','home/category/index');
    //qq登陆成功的路由地址
    Route::any('member/qqcallback','home/member/qqcallback');
    //qq登陆路由地址
    Route::any('member/qqlogin','home/member/qqlogin');
    //重置用户密码的路由地址
    Route::any('public/setnewpassword/:member_id/:time/:hash','home/public/setnewpassword');
    //发送邮件的ajax请求
    Route::get('public/sendemail','home/public/sendemail');
   //邮箱找回密码路由
    Route::get('public/forgetpassword','home/public/forgetpassword');
    //发送短信验证码路由
    Route::any('public/sendsms','home/public/sendsms');
   //前台注册路由
    Route::any('public/register','home/public/register');
    //前台用户登陆路由
    Route::any('public/login','home/public/login');
    //前台用户退出登陆路由
    Route::any('public/logout','home/public/logout');
});

################################################################################################
//后台admin分组路由
Route::group('admin',function(){
    /***************************订单管理路由***********************************/
    //查询物流信息的路由
    Route::any('order/getwuliu','admin/order/getwuliu');
    //给订单设置物流的路由
    Route::any('order/setwuliu','admin/order/setwuliu');
    //展示订单列表路由
    Route::any('order/index','admin/order/index');

    /***************************商品管理路由***********************************/
    //添加商品
    Route::any('goods/add','admin/goods/add');
    //商品列表
    Route::get('goods/index','admin/goods/index');
    //编辑商品
    Route::any('goods/upd','admin/goods/upd');
    //删除商品
    Route::get('goods/del','admin/goods/del');
    //ajax获取商品类型属性路由
    Route::get('goods/gettypeattr','admin/goods/gettypeattr');

    /***************************商品分类管理路由***********************************/
    //添加商品类型
    Route::any('category/add','admin/category/add');
    //商品类型列表
    Route::get('category/index','admin/category/index');
    //编辑商品类型
    Route::any('category/upd','admin/category/upd');
    //删除商品类型
    Route::get('category/del','admin/category/del');

    /***************************后台属性管理路由***********************************/
    //添加商品类型
    Route::any('attribute/add','admin/attribute/add');
    //商品类型列表
    Route::get('attribute/index','admin/attribute/index');
    //编辑商品类型
    Route::any('attribute/upd','admin/attribute/upd');
    //删除商品类型
    Route::get('attribute/del','admin/attribute/del');

    /***************************后台商品类型管理路由***********************************/
    //查看属性
    Route::get('type/getattr','admin/type/getattr');
    //添加商品类型
    Route::any('type/add','admin/type/add');
    //商品类型列表
    Route::get('type/index','admin/type/index');
    //编辑商品类型
    Route::any('type/upd','admin/type/upd');
    //删除商品类型
    Route::get('type/del','admin/type/del');

    /***************************后台角色管理路由***********************************/
    //添加权限
    Route::any('role/add','admin/role/add');
    //权限列表
    Route::get('role/index','admin/role/index');
    //编辑权限
    Route::any('role/upd','admin/role/upd');
    //删除权限
    Route::get('role/del','admin/role/del');

    /***************************后台权限管理路由***********************************/
    //添加权限
    Route::any('auth/add','admin/auth/add');
    //权限列表
    Route::get('auth/index','admin/auth/index');
    //编辑权限
    Route::any('auth/upd','admin/auth/upd');
    //删除权限
    Route::get('auth/del','admin/auth/del');

    /***************************后台用户管理路由***********************************/
    //编辑用户
    /*Route::any('user/upd','admin/user/upd');*/
    Route::any('user/upd','admin/user/upd');

    //用户删除路由
    /*Route::get('user/del','admin/user/del');*/

    //用户列表路由
    /*Route::get('user/index','admin/user/index');*/
    Route::get('user/index','admin/user/index');

    //添加用户
   /* Route::any('user/add','admin/user/add');*/
    Route::any('user/add','admin/user/add');
    //后台首页路由
    Route::get('index/top','admin/index/top');
    Route::get('index/left','admin/index/left');
    Route::get('index/main','admin/index/main');

    /**********************登录和退出的路由***************************************/
    //登录的路由
    /*Route::any('public/login','admin/public/login');*/
    Route::any('public/login','admin/public/login');

    //退出的路由
    /*Route::any('public/logout','admin/public/logout');*/
    Route::any('public/logout','admin/public/logout');
});


//Route::get('/test/test','admin/index/test');
//测试路由
Route::get('home/public/test','home/public/test');