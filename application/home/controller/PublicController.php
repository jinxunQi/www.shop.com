<?php
namespace app\home\controller;
use think\Controller;
use app\home\model\Member;
class PublicController extends Controller{

    public function test()
    {
        halt($_SERVER);
        //实例化购物车类
        $cart = new \cart\Cart();
        halt($cart);
        //        var_dump(sendSms('18565277429',array('1111','3'),'1'));
        //$to = ['819308959@qq.com','1595712204@qq.com'];
        //dump(sendEmail($to,'测试标题','测试内容22222222222'));
    }

    public function setNewPassword($member_id,$time,$hash)
    {
       /* echo $member_id;
        echo '<hr />';
        echo $time;
        echo '<hr />';
        echo $hash;*/

        //判断邮件地址是否被篡改过, 判断$hash 和加密字符串的结果进行比较,不一样则地址被篡改过
        if (md5($member_id.$time.config('email_salt')) !== $hash) {
            exit('你对地址做了什么');
        }
        //判断是否在有效期内 30分钟 操作
        //当前时间和  $time+有效时间 进行比较
        if (time()>$time+1800) {
            exit('改密链接时间超时,请重新尝试');
        }

        //判断是否是post请求
        if (request()->isPost()) {
            //1.接收post数据
            $postData = input('post.');
            //2.验证器验证重置密码
            $result = $this->validate($postData,'Member.setnewpassword',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //重置密码操作
            #------------------后面应该进行优化----------因为邮件中重置密码的链接还在--------------
            $data = [
                'member_id' => $member_id, //更新必须带更新主键id
                'password' => md5($postData['password'].config('password_salt'))
            ];
            $memberModel = new Member();
            if ($memberModel->update($data)) {
                empty($time);
                $this->success('重置密码成功!',url('/home/public/login'));
            }else{
                $this->error('重置密码失败!');
            }

        }
        return $this->fetch('');
    }
    
    public function register()
    {
        if (request()->isPost()) {
            $postData = input('post.');
            $result = $this->validate($postData,'Member.register',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            ###判断短信验证码是否正确  与发送短信验证码记录的cookie数据进行对比
            if (md5($postData['phoneCaptcha'].config('sms_salt')) !== cookie('phone')) {
                $this->error('输入的短信验证码有误,请重新输入!');
            }

            //判断入库是否成功
            $memberModel = new Member();
            if ($memberModel->allowField(true)->save($postData)) {
                //注册信息入库的同时 可以把短信验证码的cookie数据清空
                cookie('phone',null);
                $this->success('注册成功!',url('/'));
            }else{
                $this->error('注册失败!');
            }
        }
        return $this->fetch('');
    }

    public function login()
    {
        if (request()->isPost()) {
            $postData = input('post.');
            $result = $this->validate($postData,'Member.login',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //调用模型下的checkLogin方法,匹配用户名和密码是否正确
            $memberModel = new Member();
            $flag = $memberModel->checkLogin($postData['username'],$postData['password']);
            if ($flag) {
                //判断用户登录页是否携带goods_id,携带的话,跳转到该商品的详情页
                $goods_id = input('goods_id');
                if ($goods_id) {
                    //登陆成功后重定向 跳转到该商品的详情页
                    $this->redirect('/home/goods/detail',['goods_id'=>$goods_id]);
                }
                //登陆成功
                $this->redirect('/');
            }else{
                //登陆失败
                $this->error('用户名或者密码有误,请重新输入!');
            }
        }
        return $this->fetch('');
    }

    public function logout()
    {
        //退出登陆,直接清除前台用户登陆的session数据   ---->member_username  member_id  -->null
        session('member_username',null);
        session('member_id',null);
        //重定向到登陆页
        $this->redirect('/home/public/login');
    }

    public function sendSms()
    {
        //1.判断是否是ajax请求
        if (request()->isAjax()) {
            //2.接收手机号码信息
            $phone = input('phone');
            //3.验证器验证该号码是否曾经注册过
            $result = $this->validate(['phone'=>$phone],'Member.sendsms',[],false);
            if ($result !== true) {
                //把错误信息(响应信息$response)通过json格式返回给前端
                $response = ["code"=>-1,"message"=>"该手机号码被占用,请重新输入一个手机号码!"];
                echo json_encode($response);die;
               /* $this->error('该手机号码被占用,请重新输入一个手机号码!');*/
            }
            //发送验证码到手机上
            $rand = mt_rand(1000,9999);//设置一个随机的4位验证数字
            $sendInfo = sendSms($phone,array($rand,'5'),'1');
//            halt($sendInfo);
            //判断短信是否发送成功,并且返回json数据
            if ($sendInfo->statusCode == '000000') {
                //给手机验证码加密处理,设置有效期为5分钟
                cookie('phone',md5($rand.config('sms_salt')),300);
                $response = ["code"=>200,"message"=>"发送短信成功"];
                echo json_encode($response);die;
            }else{
                //
                $response = ["code"=>-2,"message"=>"网络异常,请重试或".$sendInfo->statusMsg];
                echo json_encode($response);die;

            }

        }
    }

    public function forgetPassword()
    {
        return $this->fetch('');
    }

    public function sendEmail()
    {
        //1.判断是否ajax请求
        if (request()->isAjax()) {
//            halt($_SERVER);
            //2.接收用户输入的邮箱  -->找回密码的注册邮箱
            $email = input('email');
            //3.验证member数据表中是否存在这个注册过的邮箱  :::该邮箱必须存在系统表中
            $result = Member::where(['email'=>$email])->find();  //where('email','=',$email)
//            halt($result);
            if ( !$result ) {
                //把响应的数据返回给前端  json格式   code  message  错误代码和错误提示信息
                $response = ["code"=>-1,"message"=>"该邮箱没有注册过用户信息!"];
                echo json_encode($response);die;  //返回json数据后die一下,结束下面代码的执行
            }
            //构造找回密码的链接地址
            $member_id = $result['member_id'];
            $time = time();
            $hash = md5($member_id.$time.config('email_salt'));
            $href = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/index.php/home/public/setnewpassword/'.$member_id.'/'.$time.'/'.$hash;
            $content = "<a href='{$href}'>京西商城-找回密码</a>";

            //发送邮件
            if (sendEmail([$email],'找回密码',$content)) {
                //把响应的数据返回给前端  json格式   code  message  错误代码和错误提示信息
                $response = ["code"=>200,"message"=>"发送邮件成功!"];
                echo json_encode($response);die;  //返回json数据后die一下,结束下面代码的执行
            }else{
                //把响应的数据返回给前端  json格式   code  message  错误代码和错误提示信息
                $response = ["code"=>-2,"message"=>"网络异常,请稍后再试!"];
                echo json_encode($response);die;  //返回json数据后die一下,结束下面代码的执行
            }
        }
    }
}