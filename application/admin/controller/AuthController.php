<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Auth;
class AuthController extends CommonController{
    public function index()
    {
        //实例化auth模型,取出数据,分配到模板中,输出模板
        $auths = Auth::field('t1.*,t2.auth_name p_name')
            ->alias('t1')
            ->join('sh_auth t2','t1.pid=t2.auth_id','left')
            ->select();
        $authModel = new Auth();
        $auths = $authModel->getSonAuth($auths);
        return $this->fetch('',[
            'auths'=>$auths,
        ]);
    }
    public function add()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证,如果是顶级权限,即pid=0,验证onlyAuthName
            if ($postData['pid'] == 0) {
                $result = $this->validate($postData,'Auth.onlyAuthName',[],true);
            }else{
                $result = $this->validate($postData,'Auth.add',[],true);
            }
            //否则验证add场景
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断入库是否成功
            $AuthModel = new Auth();
            if ($AuthModel->save($postData)) {
                $this->success('添加权限成功!',url('/admin/auth/index'));
            }else{
                $this->error('添加权限失败!');
            }
        }

        //获取所有的权限分配到模板中
        $authModel = new Auth();
//        halt($authModel->select()->toArray());
        $auths = $authModel->getSonAuth($authModel->select());
//        halt($auths);
        return $this->fetch('',[
            'auths'=>$auths,
        ]);
    }

    public function upd()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            if ($postData['pid'] == 0) {
                //判断是否是顶级,若是顶级,则进行onlyAuthName场景验证
                $result = $this->validate($postData,'Auth.onlyAuthName',[],true);
            }else{
                //否则进行upd场景验证
                $result = $this->validate($postData,'Auth.upd',[],true);
            }
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断写入(添加,编辑,删除)数据库是否成功
            $authModel = new Auth();
            if ($authModel->update($postData)) {
                $this->success('编辑成功!',url('admin/auth/index'));
            }else{
                $this->error('编辑失败!');
            }
        }
        //获取当前权限的数据,分配到模板中,输出模版
        $auth_id = input('auth_id');
        $auth = Auth::find($auth_id);
        //取出所有的无限级分类的权限
        $authModel = new Auth();
        $auths = $authModel->getSonAuth($authModel->select());
        return $this->fetch('',[
            'auth'=>$auth,
            'auths'=>$auths,
        ]);
    }

    public function del()
    {
        //1.接收auth_id参数
        $auth_id = input('auth_id');
//        halt($auth_id);
        //2.判读是否删除成功
        if (Auth::destroy($auth_id)) {
            $this->success('删除成功!',url('admin/auth/index'));
        }else{
            $this->error('删除失败!');
        }
    }

}