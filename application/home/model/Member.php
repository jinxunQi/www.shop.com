<?php
namespace app\home\model;
use think\Model;
class Member extends Model{
    protected $pk = 'member_id';
    protected $autoWriteTimestamp = true; //因为要写入数据库,要自动维护时间

    protected static function init()
    {
        //member用户入库前的事件
        Member::event('before_insert', function ($member) {
            //使用QQ登陆的用户是没有登陆密码这项的
            if (isset($member['password'])) {
                $member['password'] = md5($member['password'].config('password_salt'));
            }
        });
    }

    //验证用户名和密码和member表中的用户信息是否匹配
    public function checkLogin($username,$password)
    {
        //定义匹配规则
        $where = [
            'username' => $username,
            'password' => md5($password.config('password_salt'))
        ];
        $userInfo = $this->where($where)->find();
        if ($userInfo) {
            //利用session记录登陆者的信息
            session('member_username',$userInfo['username']);
            session('member_id',$userInfo['member_id']);
            return true;
        }else{
            return false;
        }
    }
}