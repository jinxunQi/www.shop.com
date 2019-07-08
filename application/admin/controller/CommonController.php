<?php
namespace app\admin\controller;
use think\Controller;
class CommonController extends Controller{
    public function _initialize()
    {
        if (!session('user_id')) {
            //没有session提示用户登录
            $this->error('请先登录后再操作！',url('/admin/public/login'));
        }else{
            //登陆没有翻墙,但是有可能权限翻墙
            //获取session数据
            $visitorAuth = session('visitorAuth');
//            halt($visitorAuth);
            //拼接获取到当前访问的控制器的控制器名和方法名,转为小写
            $now_ca = strtolower(request()->controller().'/'.request()->action());
//            echo $now_ca;die;
            //2.判断访问的权限是否存在session记录的权限中存在
            //2.1 超级管理员 不做权限控制 直接放行 ; 或者访问index控制器也放行
            if ($visitorAuth == '*' || strtolower(request()->controller()) == 'index') {
                return ; //不在判断下面的条件
            }
            //2.2非超级管理员 判断访问的权限是否存在session所记录的权限中存在
            if (!in_array($now_ca,$visitorAuth)) {
                exit('you cannot come in!');
            }
        }
    }
}