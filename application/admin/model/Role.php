<?php
namespace app\admin\model;
use think\Model;

class Role extends Model{
    protected $pk = 'role_id';
    protected $autoWriteTimestamp = true;
    protected static function init()
    {
        //添加入库前的事件
        Role::event('before_insert', function ($role) {
//            halt($role);
            //把权限数组形式变为字符串进行入库
            //防止没有分配权限 没有auth_ids_list属性导致报错
            if (isset($role['auth_ids_list'])) {
                $role['auth_ids_list'] = implode(',',$role['auth_ids_list']);
            }
        });
        //编辑入库前的事件
        Role::event('before_update', function ($role) {
//            halt($role);
            //把权限数组形式变为字符串进行入库
            //防止没有分配权限 没有auth_ids_list属性导致报错
            if (isset($role['auth_ids_list'])) {
                $role['auth_ids_list'] = implode(',',$role['auth_ids_list']);
            }
        });
    }


}