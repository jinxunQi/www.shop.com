<?php
namespace app\home\controller;
use app\home\model\Goods;
use think\Controller; //引用核心类
use think\Db;
use app\home\model\Order;
use app\home\model\OrderGoods;
class OrderController extends Controller{

    //获取购物车详细的商品数据
    public function getCartGoods()
    {
        //1.通过购物车类的getCart方法获取购物车的数据
        $cart = new \cart\Cart();
        $carts = $cart->getCart();
        //2.构造一定的数组结构
        $cartData = [];
        foreach ($carts as $key=>$goods_number) {
            $arr = explode('-',$key);
            $goods_id = $arr[0];
            $goods_attr_ids = $arr[1];
            $cartData[] = [
                'goods_id' => $goods_id,
                'goods_attr_ids' => $goods_attr_ids,
                'goods_number' => $goods_number,
                'goods_info' => Db::name('goods')->find($goods_id),
                'attr' => Db::name('goods_attr')
                    ->field("sum(t1.attr_price) attrTotalprice,group_concat(t2.attr_name,':',t1.attr_value separator '<br/>') as attr_info")
                    ->alias('t1')
                    ->join('sh_attribute t2','t1.attr_id = t2.attr_id','left')
                    ->where('t1.goods_id = '.$goods_id.' and t2.attr_type = 1 and t1.goods_attr_id in '.'('.$goods_attr_ids.')')
                    ->find()
            ];
        }
        return $cartData;
    }

    //完成订单表和订单商品表的入库
    public function orderPay()
    {
        //判断用户是否登陆
        $member_id = session('member_id');
        if (!$member_id) {
            $this->error('请先登陆!');
        }
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
           //3.验证器验证post的数据
            $result = $this->validate($postData,'Order.pay',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.获取订单的总价和生成唯一的订单号order_id
            $order_id = date('ymdhis').time().uniqid();
            //获取购物车数据,得到总价total_price
            $cartData = $this->getCartGoods();
//            halt($cartData);
            if (!$cartData) {
                $this->error('购物车数据不能为空!',url('/'));
            }
            $total_price = 0;
            foreach ($cartData as $v) {
                $total_price += ($v['goods_info']['goods_price']+$v['attr']['attrTotalprice'])*$v['goods_number'];
            }
            //5.开启事务,先入库到订单表
            //准备订单表的入库数据
            $postData['order_id'] = $order_id;
            $postData['total_price'] = $total_price;
            $postData['member_id'] = $member_id;
            Db::startTrans();
            //6.订单表入库成功后才可以入库到订单商品表
            try {
                //先对订单表进行入库
                $order_result = Order::create($postData);
                if (!$order_result) {
                    //说明订单表入库失败,抛出异常,触发滚回事件
                    throw new \Exception('订单表入库失败!');
                }
                //上面成功之后,才可以把订单商品入库到订单商品表中
                $goodsModel = new Goods();
                foreach ($cartData as $v) {
                    $goods_price = ($v['goods_info']['goods_price']+$v['attr']['attrTotalprice'])*$v['goods_number'];
                    $order_goods_result = OrderGoods::create([
                        'order_id' => $order_id,
                        'goods_id' => $v['goods_id'],
                        'goods_attr_ids' => $v['goods_attr_ids'],
                        'goods_number' => $v['goods_number'],
                        'goods_price' => $goods_price
                    ]);
                    //还要减少对应的商品的库存(只有商品表的数量大于我们购买的数量才库存)
                    //sh_goods 50   >=   sh_cart 100
                    $where = [
                        'goods_id' => $v['goods_id'],
                        'goods_number' => ['>=',$v['goods_number']]
                    ];
                    //更新商品表,减去库存
                    $goods_result = $goodsModel->where($where)->setDec('goods_number',$v['goods_number']);
                    //只有订单商品表和商品表减去对应库存才可以进行下一次循环,失败就抛出异常,进行回滚
                    if (!$order_goods_result || !$goods_result) {
                        throw new \Exception('订单商品表失败,或库存不足');
                    }
                }
                //上面都成功之后,提交事务
                Db::commit();
            } catch (\Exception $e) {
                //上面抛出异常,这里进行回滚操作
                Db::rollback();
            }
            //7.清空购物车
            $cart = new \cart\Cart();
            $cart->clearCart($member_id);
            //8.唤起支付宝进行支付
            //echo '支付宝支付....';die;
            //调用唤醒支付宝支付的方法
            $this->_payMoney($total_price,$order_id,'支付测试页','');
        }
    }

    //支付宝同步通知(post方式)跳转地址(线上)
    public function notifyUrl()
    {
        //./表示网站根目录  public
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';

        //$arr=$_GET; 改成tp5框架接收get参数的方式
        $arr = input('post.');
        //halt($arr);
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

        if($result) {//验证成功

            //商户订单号
            $out_trade_no = htmlspecialchars($arr['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($arr['trade_no']);

            //更新订单的状态为已支付
            $orderModel = new Order();
            $result = $orderModel->where(['order_id'=>$out_trade_no])->update([
                'pay_status' => 1,
                'ali_order_id' => $trade_no
            ]);
            if ($result) {
                //异步通知,支付成功只能输出success
                echo 'success';
            }

        }
        else {
            //验证失败
            echo "验证失败";
        }
    }

    //支付宝同步通知(get方式)跳转地址
    public function returnUrl()
    {
        //./表示网站根目录  public
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';

        //$arr=$_GET; 改成tp5框架接收get参数的方式
        $arr = input('get.');
        //halt($arr);
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

        if($result) {//验证成功

            //商户订单号
            $out_trade_no = htmlspecialchars($arr['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($arr['trade_no']);

            //更新订单的状态为已支付
            $orderModel = new Order();
            $result = $orderModel->where(['order_id'=>$out_trade_no])->update([
                'pay_status' => 1,
                'ali_order_id' => $trade_no
            ]);
            if ($result) {
                //支付成功
                $this->success('支付成功!',url('/home/order/orderDone'));
            }else{
                //支付失败
                $this->error('支付失败!',url('/'));
            }

        }
        else {
            //验证失败
            echo "验证失败";
        }
    }

    //展示个人订单的方法
    public function selfOrder()
    {
        //获取订单表中的数据
        $orderData = Order::where(['member_id'=>session('member_id')])->select();
        return $this->fetch('',[
            'orderData' => $orderData
        ]);
    }

    //某个未付款订单付款方法
    public function paymoney()
    {
        //接收order_id的值
        $order_id = input('order_id');
        //查询该条订单的订单金额
        $total_price = Order::where(['order_id'=>$order_id])->value('total_price');
        //调用支付宝支付方法
        $this->_payMoney($total_price,$order_id,'测试遗漏未支付的订单','有钱就是任性');
    }

    //支付宝支付完成的页面
    public function orderDone()
    {
        return $this->fetch('');
    }

    //唤起支付宝进行支付
    private function _payMoney($total_price,$order_id,$title='',$body=''){
        $payData = [
            'WIDtotal_amount' => $total_price,
            'WIDout_trade_no' => $order_id,
            'WIDsubject' => $title,
            'WIDbody' => $body
        ];
        //引入唤醒支付宝支付
        include "../extend/alipay/pagepay/pagepay.php";
    }
}