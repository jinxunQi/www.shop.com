<?php
namespace app\home\controller;
use app\home\model\Member;
use think\Controller;
class MemberController extends Controller{
    public function qqlogin()
    {
        //调用qq登陆的库文件,出现一个qq登陆框或者扫码框
        require_once("../extend/qqLogin/qqConnectAPI.php");
        //实例化一个全局类和系统内置类,需要加反斜杠 \
        $qc = new \QC();
        $qc->qq_login();

    }

    //登陆qq成功后的回调方法
    public function qqCallback()
    {
        require_once("../extend/qqLogin/qqConnectAPI.php");
        $qc = new \QC();
        $token = $qc->qq_callback();
        $openid = $qc->get_openid();
        session('openid',$openid);

        $memberModel = new Member();
        $userInfo = $memberModel->where(['openid'=>$openid])->find();
        if ($userInfo) {
            //表示用户之前用qq登录过,获取用户信息存储到session中,用于返回回显
            session('member_username',$userInfo['username']?$userInfo['username']:$userInfo['nickname']);
            session('member_id',$userInfo['member_id']);
        }else{
            //表示用户第一次使用qq登陆
            $qc = new \QC($token,$openid);
            $qqUserInfo = $qc->get_user_info();
            //把用户的openid和nickname写入到数据库中
            $data = [
                'openid'=>$openid,
                'nickname'=>$qqUserInfo['nickname']
            ];
            //通过模型名静态调用crate(),成功返回入库后的数据对象 这个与save()有所区别
            $result = Member::create($data);
            if ($result) {
                //写入成功(第一次登陆是没有username的值,所以,把nickname写入到session中 方便回显)
                session('member_username',$result['nickname']);
                session('member_id',$result['member_id']);
                $this->redirect('/');
            }else{
                //写入失败
                $this->error('qq登陆失败!');
            }
        }




       /* echo $token.'<hr>';
        echo $openid.'<hr>';
        //重新实例化对象，获取qq登录用户的用户信息
        $qc = new \QC($token,$openid);
        $qqUserInfo = $qc->get_user_info();
        halt($qqUserInfo);*/
    }
}