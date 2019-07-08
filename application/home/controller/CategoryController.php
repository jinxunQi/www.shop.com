<?php
namespace app\home\controller;
use app\home\model\Goods;
use think\Controller; //引用核心类
use app\home\model\Category;
class CategoryController extends Controller{
    public function index()
    {
        //接收传递过来的cat_id
        $cat_id = input('cat_id');
        //实例化category对象,调用模型对象下的getFamilyCat方法,找到祖先分类
        $categoryModel = new Category();
        $cats = $categoryModel->select()->toArray();
        $familyCats = $categoryModel->getFamilyCats($cats,$cat_id);

//        halt($familyCats);
        //1.构建一个数组$catData , 以$cats的cat_id(主键)作为他的下标
        $catData = [];
        foreach ($cats as $v) {
            $catData[ $v['cat_id'] ] = $v;
        }
        //2.构建一个二维数组$children ,以$cats的pid进行分组
        $children = [];
        foreach ($cats as $vv) {
            $children[ $vv['pid'] ][] = $vv['cat_id'];
        }


        //获取当前分类的子孙分类的cat_id
        $sonsCatId = $categoryModel->getSonsCatId($cats,$cat_id);
//        halt($sonsCatId);

        //把当前分类也要加上
        $sonsCatId[] = $cat_id;
        //查询在子孙分类下的所有商品即可  要满足:1.是上架商品 既is_sale =1 ;2.不在回收站中 is_delete =0
        //商品的分类cat_id 在$sonsCatId 下  1 in [1,23,45,]
        $where = [
            'is_sale'=>1,
            'is_delete'=>0,
            'cat_id'=>['in',$sonsCatId]
        ];
        $goodsData = Goods::where($where)->select()->toArray();
        //halt($goodsData);
//        halt($familyCats);
        return $this->fetch('',[
            'familyCats' => $familyCats,
            'catData' => $catData,
            'children' => $children,
            'goodsData' => $goodsData
        ]);
    }
}