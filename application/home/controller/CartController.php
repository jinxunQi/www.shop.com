<?php
namespace app\home\controller;
use think\Controller;
use think\Db; //引用核心类
class CartController extends Controller{

    //展示订单阶段页面
    public function orderAccount()
    {
        //先判断用户是否登陆
        if (!session('member_id')) {
            $this->error('请先进行登陆!',url('/'));
        }
        //调用getCartGood方法获取购物车数据
        $cart = new \cart\Cart();
        $cartData = $this->getCartGoods();
        //判断购物车中是否有商品,没有提示返回首页
        if (!$cart->getCart()) {
            $this->error('购物车为空,请先添加商品再结算!',url('/'));
        }
        //halt($cartData);
        return $this->fetch('',[
            'cartData' => $cartData
        ]);
    }

    //更新购物车商品数量
    public function updateCartGood()
    {
        //1.判断是否是ajax请求
        if (request()->isAjax()) {
            //2.判断用户是否已经登陆过
            if (!session('member_id')) {
                $response = ['code'=>-1,'message'=>'登陆信息已失效,请重新登录!'];
                echo json_encode($response);die;
            }
            //3.接收参数
            $goods_id = input('goods_id');
            $goods_attr_ids = input('goods_attr_ids');
            $goods_number= input('goods_number');
            //4.调用购物车类中相对应的方法,更新数据库
            $cart = new \cart\Cart();
            $result = $cart->changeCartNum($goods_id,$goods_attr_ids,$goods_number);
            if ($result) {
                $response = ['code'=>200,'message'=>'success'];
                echo json_encode($response);die;
            }else{
                $response = ['code'=>-2,'message'=>'fail'];
                echo json_encode($response);die;
            }
        }
    }

    //清空购物车
    public function clearCartGoods()
    {
        if (request()->isAjax()) {
            //调用cart购物车类
            $cart = new \cart\Cart();
            $result = $cart->clearCart();
            if ($result) {
                //表示清空购物车成功
                $response = ['code'=>200,'message'=>'清空购物车成功!'];
                echo json_encode($response);die;
            }else{
                //表示清空购物车失败
                $response = ['code'=>-1,'message'=>'清空购物车失败,请稍后尝试!'];
                echo json_encode($response);die;
            }
        }
    }

    //删除购物车商品
    public function delcartgood()
    {
        if (request()->isAjax()) {
            //1.接收参数
            $goods_id = input('goods_id');
            $goods_attr_ids = input('goods_attr_ids');
            //2.调用购物车类的delcart方法
            $cart = new \cart\Cart();
            $result = $cart->delCart($goods_id,$goods_attr_ids);
            if ($result) {
                //表示删除购物车商品成功
                $response = ['code'=>200,'message'=>'删除成功!'];
                echo json_encode($response);die;
            }else{
                //表示删除失败
                $response = ['code'=>-1,'message'=>'删除失败!'];
                echo json_encode($response);die;
            }
        }
    }

    //添加商品到购物车
    public function addGoodsToCart()
    {
       ## 1.判断是否是ajax请求
        if (request()->isAjax()) {
            ##   2.先判断用户是否已经登陆
            $member_id = session('member_id');
            if (!$member_id) {
                //表示用户没有登陆就进行加入购物车操作,响应用户先进行登陆
                $response = ["code"=>-1,"message"=>"请先登录后再尝试!"];
                echo json_encode($response); die;
            }
            ##  3.接收参数
            $goods_id = input('goods_id');
            $goods_attr_ids = input('goods_attr_ids');
            $goods_number = input('goods_number');

            ##  4.调用购物车方法,进行商品的入库
            $cart = new \cart\Cart();
            $result = $cart->addCart($goods_id,$goods_attr_ids,$goods_number);
            if ($result) {
                //加入购物车成功,返回json数据
                $response = ["code"=>200,"message"=>"加入购物车成功!"];
                echo json_encode($response); die;
            }else{
                //加入购物车失败,同样也返回json数据
                $response = ["code"=>-2,"message"=>"加入购物车失败,请稍后再试!"];
                echo json_encode($response); die;
            }
        }
    }

    //购物车列表页数据
    public function cartList()
    {
        //先判断用户是否已经登陆
        $member_id = session('member_id');
        if (!$member_id) {
            $this->error('请先登陆后再操作!');
        }
        //1.通过购物车类的getCart方法获取购物车的数据
        $cart = new \cart\Cart();
        $carts = $cart->getCart();
        //2.构造一定的数组结构
        $cartData = [];
        foreach ($carts as $key=>$goods_number) {
            $arr = explode('-',$key);
            $goods_id = $arr[0];
            $goods_attr_ids = $arr[1];
            $data = [
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
            $cartData[] = $data;
        }
        //halt($cartData);
        return $this->fetch('',[
            'cartData' => $cartData
        ]);
    }

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
}