<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
error_reporting(0);

//发送短信验证码的方法
/**
 * 发送模板短信
 * @param to 手机号码集合,用英文逗号分开
 * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
 * @param $tempId 模板Id
 */
function sendSms($to,$datas,$tempId)
{
    // 初始化REST SDK
    include_once("../extend/sendSms/CCPRestSDK.php");
    //主帐号
    $accountSid= '8aaf07086bb6a176016bbbe5ff6d047e';
    //主帐号Token
    $accountToken= 'fb4506fb7da94098847e92f3703bd992';
    //应用Id
    $appId='8aaf07086bb6a176016bbbe5ffd00485';
    //请求地址，格式如下，不需要写https://
    $serverIP='app.cloopen.com';
    //请求端口
    $serverPort='8883';
    //REST版本号
    $softVersion='2013-12-26';

    $rest = new REST($serverIP,$serverPort,$softVersion);
    $rest->setAccount($accountSid,$accountToken);
    $rest->setAppId($appId);

    // 发送模板短信
//    echo "Sending TemplateSMS to $to <br/>";

    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
    return $result;
//    if($result == NULL ) {
//        echo "result error!";
//        break;
//    }
//    if($result->statusCode!=0) {
//        echo "error code :" . $result->statusCode . "<br>";
//        echo "error msg :" . $result->statusMsg . "<br>";
//        //TODO 添加错误处理逻辑
//    }else{
//        echo "Sendind TemplateSMS success!<br/>";
//        // 获取返回信息
//        $smsmessage = $result->TemplateSMS;
//        echo "dateCreated:".$smsmessage->dateCreated."<br/>";
//        echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
//        //TODO 添加成功处理逻辑
//    }
}
//Demo调用,参数填入正确后，放开注释可以调用
//                 手机号码     array('验证码','有限期')  '短信模板'  -->为上线的项目只有'1'
//sendTemplateSMS("18565277429",array('6666','3'),"1");

#邮件发送方法
function sendEmail($to,$title,$content){

    // 实例化
    include "../extend/sendEmail/class.phpmailer.php";
    $pm = new PHPMailer();
    // 服务器相关信息
    $pm->Host = 'smtp.qq.com'; // SMTP服务器
    $pm->IsSMTP(); // 设置使用SMTP服务器发送邮件
    $pm->SMTPAuth = true; // 需要SMTP身份认证
    $pm->Username = '574765035'; // 登录SMTP服务器的用户名，邮箱@前面一串字符
    $pm->Password = 'aizyejopjlefbcjf'; //授权码 登录SMTP服务器的密码

    // 发件人信息
//    $pm->From = 'manbawei@163.com'; //自己的邮箱
//    $pm->FromName = '曼巴'; // 发件人昵称，名字可以随便取
    $pm->From = '574765035@qq.com';
    $pm->FromName = ''; // 发件人昵称，名字可以随便取

    // 收件人信息
//    $pm->AddAddress('666666@qq.com', '6哥'); // 设置收件人邮箱和昵称，昵称名字随便取
    foreach ($to as $email) {
        $pm->AddAddress($email, ''); // 设置收件人邮箱和昵称，昵称名字随便取
    }
    //$pm->AddAddress('888888@qq.com', '8哥'); // 添加另一个收件人


    $pm->CharSet = 'utf-8'; // 内容编码
//    $pm->Subject = '邮件标题'; // 邮件标题
    $pm->Subject = $title; // 邮件标题
//    $pm->MsgHTML('<a href="http://www.itcast.cn" target="_blank">商城找回密码</a>！'); // 邮件内容
    $pm->MsgHTML($content); // 邮件内容

    //var_dump($pm->Send()); //成功返回true
    // 发送邮件
    if ($pm->Send()) {
//        echo 'ok';
        return true;
    } else {
//        echo $pm->ErrorInfo;
        return false;
    }
}