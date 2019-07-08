<?php
namespace app\home\controller;
use app\home\model\Category;
use app\home\model\Goods;
use think\Controller;
use think\Db; //引用核心类
class GoodsController extends Controller{
    public function detail()
    {
        //接收goods_id的值
        $goods_id = input('goods_id');
        //从goods数据表中查找到该goods_id下的商品
        $good = Goods::find($goods_id)->toArray();
        $good['goods_img'] = json_decode($good['goods_img']);
        $good['goods_middle'] = json_decode($good['goods_middle']);
        $good['goods_thumb'] = json_decode($good['goods_thumb']);
        //halt($good);

        ####   取出商品的单选属性数据
        $_singleAttrDatas = Db::name('goods_attr')
                        ->alias('t1')
                        ->field('t1.*,t2.attr_name')
                        ->join('sh_attribute t2','t1.attr_id = t2.attr_id','left')
                        ->where("goods_id =".$goods_id.' and t2.attr_type = 1 ')
            //attr_type = 1表示是单选属性
                        ->select();

        //通过attr_id把单选属性进行分组,为了方便后续在模板中遍历
        //halt($_singleAttrData);
        $singleAttrDatas = [];
        foreach ($_singleAttrDatas as $attr) {
            $singleAttrDatas[ $attr['attr_id'] ][] = $attr;
        }

        ####   取出商品的唯一属性数据
        $onlyAttrDatas = Db::name('goods_attr')
            ->alias('t1')
            ->field('t1.*,t2.attr_name')
            ->join('sh_attribute t2','t1.attr_id = t2.attr_id','left')
            ->where("goods_id =".$goods_id.' and t2.attr_type = 0 ')
            //attr_type = 0表示是唯一属性
            ->select();

        //halt($onlyAttrDatas);
        ##获取面包屑导航分类数据
        $categoryModel = new Category();
        $cats = $categoryModel->select()->toArray();
        $familyCats = $categoryModel->getFamilyCats($cats,$good['cat_id']);

        ## 把访问过的商品goods_id加入浏览历史cookie中
        $goodsModel = new Goods();
        $history = $goodsModel->addGoodsHistory($goods_id); //[1,5]
//        halt($history);
        $where = [
            'is_delete' => 0,
            'is_sale' => 1,
            'goods_id' => ['in',$history] //取出goods_id在$history数组里的浏览历史商品
        ];
        //把$history数组炸开成字符串
        $history_to_str = implode(',',$history);
        $historyDatas = $goodsModel->where($where)->order("field(goods_id,$history_to_str)")->select()->toArray();
        //halt($historyDatas);
        return $this->fetch('',[
            'familyCats' => $familyCats,
            'good' => $good,
            'singleAttrDatas' => $singleAttrDatas,
            'onlyAttrDatas' => $onlyAttrDatas,
            'historyDatas' => $historyDatas
        ]);
    }
}