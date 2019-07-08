<?php

namespace app\home\controller;
use app\home\model\Category;
use think\Controller;
use app\home\model\Goods;
class IndexController extends Controller
{
    public function index()
    {
        //获取商品分类数据,显示到前台导航栏上  is_show = 1
        $categoryModel = new Category();
        $navDatas = $categoryModel->getNavData(5);
        //取出首页的三级分类筛选的数据
        $oldCat = Category::select()->toArray();
        $cats = [];
        //1.以每个分类的cat_id主键作为cats二维数组的下标
        foreach ($oldCat as $cat_id => $cat) { //cat是个一维数组了,那么$cat['cat_id']表示取cat_id的值
            $cats[$cat['cat_id']] = $cat;
        }
        //2.根据pid进行分类
        $children = [];
        foreach ($oldCat as $cat) {
            $children[$cat['pid']][] = $cat['cat_id'];
        }
//        dump($cats);
//        halt($children);

        //取出前台推荐的商品
        $goodsModel = new Goods();
        $crazyDatas = $goodsModel->getGoods('is_crazy',5);
        $hotDatas = $goodsModel->getGoods('is_hot',5);
        $bestDatas = $goodsModel->getGoods('is_best',5);
        $newDatas = $goodsModel->getGoods('is_new',5);

        return $this->fetch('', [
            'navDatas' => $navDatas,
            'children' => $children,
            'cats' => $cats,
            'crazyDatas' => $crazyDatas,
            'hotDatas' => $hotDatas,
            'bestDatas' => $bestDatas,
            'newDatas' => $newDatas
        ]);
    }
}