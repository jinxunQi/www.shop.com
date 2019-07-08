<?php
namespace app\admin\controller;
use app\admin\model\Auth;
use app\admin\model\Role;
use think\Db;

class RoleController extends CommonController{
    public function add()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'Role.add',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断是否入库成功
            $roleModel = new Role();
            if ($roleModel->save($postData)) {
                $this->success('添加成功!',url('admin/role/index'));
            }else{
                $this->error('添加失败!');
            }
        }

        //获取权限信息,分配到模版中
        $authModel = new Auth();
        $oldauths = $authModel->select()->toArray();

        //以auth_id作为$auths的二维数组下标
        $auths = [];
        foreach ($oldauths as $v) {
            $auths[ $v['auth_id'] ] = $v;
        }
//        halt($auth);
        //把所有的权限以pid进行分组
        $children = [];
        foreach ($oldauths as $vv) {
            $children[ $vv['pid'] ][] = $vv['auth_id'];
        }
//        halt($children);
        return $this->fetch('',[
            'auths'=>$auths,
            'children'=>$children,
        ]);
    }

    public function index()
    {
        //获取所有角色的信息,分配到模板中 利用DB构造器写原生sql语句
        $roles = Role::query("select t1.*,GROUP_CONCAT(t2.auth_name) as all_auth from sh_role t1 left join sh_auth t2 on FIND_IN_SET(t2.auth_id,t1.auth_ids_list) group by t1.role_id");
//        halt($roles);
        return $this->fetch('',['roles'=>$roles]);
    }

    public function upd()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'Role.upd',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断是否入库成功
            $roleModel = new Role();
            if ($roleModel->update($postData)) {
                $this->success('编辑成功!',url('/admin/role/index'));
            }else{
                $this->error('编辑失败!');
            }
        }

        //1.接收role_id参数
        $role_id = input('role_id');
        //取出所有权限
        $oldAuths = Auth::select()->toArray();
        //以每个权限的role_id作为$oldAuths二维数组的下标
        $auths = [];
        foreach ($oldAuths as $v) {
            $auths[ $v['auth_id'] ] = $v;
        }
//        halt($auths);
        //根据pid进行分组,把具有相应的pid分为同一组
        $children = [];
        foreach ($oldAuths as $vv) {
            $children[ $vv['pid'] ][] = $vv['auth_id'];
        }
//        halt($children);
        //取出当前角色已有的权限
        $role = Role::find($role_id);
//        halt($role);
        return $this->fetch('',[
            'auths'=>$auths,
            'children'=>$children,
            'role'=>$role,
        ]);
    }

    public function del()
    {
        //1.接收role_id
        $role_id = input('role_id');
//        halt($role_id);
        //判断是否删除成功
        if (Role::destroy($role_id)){
            $this->success('删除成功!',url('/admin/role/index'));
        }else{
            $this->error('删除失败!');
        }
    }
}