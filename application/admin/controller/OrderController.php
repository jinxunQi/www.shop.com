<?php
namespace app\admin\controller;
//use think\Controller; //引用核心类
use think\Db;

class OrderController extends CommonController{
    //展示订单列表
    public function index()
    {
        //获取订单的数据
        $orderData = Db::name('order')->select();
        return $this->fetch('',[
            'orderData' => $orderData
        ]);
    }

    //设置物流方法
    public function setWuliu()
    {
        if (request()->isPost()) {
            //接收参数
            $postData = input('post.');
            //验证器验证(自行补充)
            //写入数据到数据库更新订单表
            $postData['update_time'] = time(); //利用Db操作数据库不能自动对时间进行维护
            $postData['send_status'] = 1;
            if (Db::name('order')->update($postData)) {
                $this->success('配置物流成功!',url('/admin/order/index'));
            }else{
                $this->error('配置物流失败!');
            }
        }

        //通过order_id 获取详细某一个订单信息分配到模板
        $order_id = input('order_id');
        $orderData = Db::name('order')->where('order_id','=',$order_id)->find();
        return $this->fetch('',[
            'orderData'=>$orderData
        ]);
    }

    //查询物流信息的方法
    public function getWuliu()
    {
        if (request()->isAjax()) {
            //接收参数
            $company = input('company');
            $number = input('number');
            //快递100接口链接
            $url = "http://www.kuaidi100.com/applyurl?key={9d37bc6b0a41e6fe}&com={$company}&nu={$number}&show=0";
            echo file_get_contents($url); //此接口返回url地址,前台作为iframe的src属性即可
        }

    }
}