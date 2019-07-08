<?php
namespace app\admin\controller;
use app\admin\model\Role;
use think\Controller;
use app\admin\model\User;
class UserController extends CommonController{

    public function index()
    {
        //1.获取数据
        $users = User::alias('t1')
            ->field('t1.*,t2.role_name')
            ->join('sh_role t2','t1.role_id = t2.role_id','left')
            ->paginate(2); //tp5框架的分页,直接利用paginate替换select
        //2.输出模版
        return $this->fetch('',[
            'users' => $users,
        ]);
    }
    public function add()
    {
        $userModel = new User();
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'user.add',[],true);
            if ($result !== true) {
                //表示验证失败
                $this->error(implode(',',$result));
            }
            //4.编辑update或添加入库save

            if ($userModel->allowField(true)->save($postData)) {
                $this->success('入库成功',url('admin/user/index'));
            }else{
                $this->error('入库失败');
            }
        }
        //取出所有角色数据,分配到模板中
        $roles = Role::select();
        return $this->fetch('',['roles'=>$roles]);
    }
    public function del()
    {
        //1.接收参数
        $user_id = input('user_id');//获取表单|url提交的数据
        //2.判断是否删除成功
        if (User::destroy($user_id)) {
            $this->success('删除成功!',url('admin/user/index'));
        }else{
            $this->error('删除失败!');
        }
    }
    public function upd()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            $userModel = new User();
            //2.接收参数
            $postData = input('post.');
            //3.验证器进行验证
            //当前密码和确认密码都为空的时候,只验证username,保留原密码
            if ($postData['password']==''&&$postData['repassword']=='') {

                $result = $this->validate($postData,'User.onlyUsername',[],true);
                if ($result !== true) {
                    $this->error(implode(',',$result));
                }
            }else{
                //说明其中有一个密码是不为空,则进行UsernamePassword场景验证
                $result = $this->validate($postData,'User.UsernamePassword',[],true);
                if ($result !== true) {
                    $this->error(implode(',',$result));
                }
            }
            //4.判断是否编辑成功入库
            if ($userModel->allowField(true)->isUpdate(true)->save($postData)) {
                $this->success('编辑成功!',url('/admin/user/index'));
            }else{
                $this->error('编辑失败!');
            }
        }

        //获取数据,回显到模版中
        //1.获取数据
        $user_id = input('user_id');
        $userData = User::find($user_id);
        //2.回显到模板中
        return $this->fetch('',[
            'userData' => $userData
        ]);
    }
}