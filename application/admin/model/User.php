<?php
namespace app\admin\model;
use think\Model;
class User extends Model{
    //表的主键字段(指定用户表的主键)
    protected $pk = "user_id";
    //时间戳自动写入
    protected $autoWriteTimestamp = true;

    //事件的处理方法
    protected static function init()
    {
        //入库前的事件before_insert
        User::event('before_insert',function ($user){
            //return false; 就不会入库 不会执行save方法
            //参数$user 表单中的数据对象后面把此对象的数据进行入库
            //入库之前可以对其数据进行加密
            //给密码password字段进行加密
//            dump($user);
            $user['password'] = md5($user['password'].config('password_salt'));
//            halt($user);
            //之后入库是入库$user对象中的data属性的数据
        });

        User::event('before_update',function($user){
            //如果密码为空,不更新密码,删除password字段即可
            if ($user['password']=='') {
                unset($user['password']);
            }else{
                //不为空说明要更新密码.需要加密处理
                $user['password'] = md5($user['password'].config('password_salt'));
            }

        });

//        User::event('before_update',function ($user){
//            //如果密码为空,不更新密码,删除password字段就可以
//            if ($user['password']=='') {
//                unset($user['password']);
//            }else{
//                //不为空说明要更新密码.需要加密处理
//                $user['password'] = md5($user['password']).config('password_salt');
//            }
//        });
    }
    //检测用户名和密码是否匹配
    public function checkUser($username,$password)
    {
        $where = [
            'username'=>$username,
            'password'=>md5($password.config('password_salt'))
        ];
//        halt($where);
        $userInfo = $this->where($where)->find();
        if ($userInfo) {
            //用户名和密码匹配，把用户的信息写入到session中去
            session('user_id',$userInfo['user_id']);
            session('username',$userInfo['username']);
            
            //通过用户的角色role_id,把当前用户的权限写入到session中去
            $this->getAuthWriteSession($userInfo['role_id']);
            return true;
        }else{
            return false;
        }
    }

    public function getAuthWriteSession($role_id)
    {
        //通过用户表的role_id 去 找角色表的auth_ids_list 字段的值
        $auth_ids_list = Role::where(['role_id'=>$role_id])->value('auth_ids_list');  //value可以获得某个字段的值
//        halt($auth_ids_list);
        //如果是超级管理员 即$auth_ids_list == '*'
        if ($auth_ids_list == '*') {
            //超级管理员拥有权限表的所有权限
            $oldAuths = Auth::select()->toArray();
        }else{
            //如果是非超级管理员,只能取出已有的权限 $auth_ids_list => '1,2,3,7,8'
            $oldAuths = Auth::where("auth_id","in",$auth_ids_list)->select()->toArray();
        }

        //两个技巧取出数据
        //1.每个数组的auth_id作为二维数组的下标
        $auths = [];
        foreach ($oldAuths as $v) {
            $auths[ $v['auth_id'] ] = $v;
        }

        //通过pid进行分组
        $children = [];
        foreach ($oldAuths as $vv) {
            $children[ $vv['pid'] ][] = $vv['auth_id'];
        }
//        halt($children);
        //把这两个信息数据写入到session中去
        session('auths',$auths);
        session('children',$children);

        //写入管理员可访问的权限到session中去,用于后面的防翻墙
        if ($auth_ids_list == '*') {
            //超级管理员
            session('visitorAuth','*');
        }else{
            //非超级管理员 [auth/index,auth/add......]
            $visitorAuth = [];
            foreach ($oldAuths as $v) {
                $visitorAuth[] = strtolower($v['auth_c'].'/'.$v['auth_a']);
            }
            session('visitorAuth',$visitorAuth);
//            halt($visitorAuth);
        }
    }
}