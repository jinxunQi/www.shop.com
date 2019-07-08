<?php
namespace app\home\model;
use think\Model;
class Goods extends Model{
    protected $pk = '';

    //获取推荐商品的方法
    public function getGoods($type,$limit)
    {
        //定义初始化查询条件
        $where = [
            'is_sale' => 1 //只有上架才有推荐商品
        ];
        /*switch ($type) {
            case 'is_crazy':
                $data = $this->where($where)->order('goods_price asc')->limit($limit)->select();
                break;
            default:
                //定义查询条件
                $where[$type] = ['=',1];
                $data = $this->where($where)->limit($limit)->select();
                break;
        }*/

        //-------------------或者使用if elseif--------------------------------
        if ($type == 'is_crazy') {
            $data = $this->where($where)->order('goods_price asc')->limit($limit)->select();
        }elseif ($type == 'is_new'){
            $where[$type] = ['=',1];
            $data = $this->where($where)->limit($limit)->select();
        } elseif ($type == 'is_best') {
            $where[$type] = ['=',1];
            $data = $this->where($where)->limit($limit)->select();
        } elseif ($type == 'is_hot') {
            $where[$type] = ['=',1];
            $data = $this->where($where)->limit($limit)->select();
        }
        return $data;
    }

    //商品加入浏览历史cookie的方法中
    public function addGoodsHistory($goods_id)
    {
        //由于加入$goods_id之前,cookie可能已经有history的浏览历史记录,要先判断下 把记录取出来
        $history = cookie('history')?cookie('history'):[]; //[3,4]
        if ($history) {
            //说明浏览历史记录中已经有数据了
            //1.把最近浏览的一个商品$goods_id记录添加到数组的前面(头部)
            array_unshift($history,$goods_id);
            //2.$history去掉重复的商品
            $history = array_unique($history);
            //3.判断$history是否超过指定长度 默认 :5
            if (count($history)>5) {
                //把history后面的记录(第5条)之后的数据移除数组
                array_pop($history);
            }
        }else{
            //说明浏览历史记录没有数据,直接把当前访问的商品的goods_id存进去即可
            //直接把$goods_id加到$history之后
            $history[] = $goods_id;
        }
        //把浏览历史记录重新写入到cookie中
        //tp5中cookie存数组默认底层已经帮助我们序列化(serialize)存储,取出来的时候默认会自动进行反序列化(unserialize)
        // 商品浏览历史有效期为7天
        cookie('history',$history,60*60*24*7);
        //返回给调用者
        return $history;
    }
}
