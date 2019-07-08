<?php
namespace app\admin\controller;
use app\admin\model\User;
use think\Controller;
class PublicController extends Controller{
    public function login()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收数据
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'User.login',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断用户是否登陆成功（把验证逻辑写在模型中）
            $userModel = new User();
            if ($userModel->checkUser($postData['username'],$postData['password'])) {
                //登录成功，到后台首页去
                $this->redirect('/houtai');
            }else{
                $this->error('用户名或者密码有误,请重新登陆!');
            }
        }
        return $this->fetch('');
    }
    public function logout()
    {
        //清除登陆成功保存的session信息
        session('user_id',null);
        session('username',null);
        //重定向到登陆页
        $this->redirect('/admin/public/login');
    }
}