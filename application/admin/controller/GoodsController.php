<?php
namespace app\admin\controller;

use app\admin\model\Attribute;
use app\admin\model\Category;
use app\admin\model\Goods;
use app\admin\model\Type;
use think\Db;

class GoodsController extends CommonController{
    public function add()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
//            halt($postData);
            //3.验证器验证
            $result = $this->validate($postData,'Goods.add',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }

            //开始图片上传
            $goodsModel = new Goods();
            $goods_img = $goodsModel->uploadImg(); //$goods_img是指多个原图 []
//            halt($goods_img);

            if ($goods_img) { //表示有原图,则执行缩略图的生成
                $thumb = $goodsModel->thumb($goods_img); //调用生成缩略图的方法
//                halt($thumb);
                //把路径写入数据库(存json格式)
                $postData['goods_img'] = json_encode($goods_img); //把原图路径
                $postData['goods_middle'] = json_encode($thumb['goods_middle']); //中图路径
                $postData['goods_thumb'] = json_encode($thumb['goods_thumb']); //小图路径
            }

            //4.判断入库是否成功
            if ($goodsModel->allowField(true)->save($postData)) {
                $this->success('添加成功!',url('/admin/goods/index'));
            }else{
                $this->error('添加失败!');
            }
        }

        //取出所有无限极分类的数据
        $categoryModel = new Category();
        $cats = $categoryModel->getSonCats($categoryModel->select()->toArray());
        //取出商品的类型
        $types = Type::select();
        return $this->fetch('',[
            'cats'=>$cats,
            'types'=>$types,
        ]);
    }

    public function getTypeAttr()
    {
        //1.判断是否是ajax请求
        if (request()->isAjax()) {
            //2.接收type_id的参数
            $type_id = input('type_id');
            //从商品类型属性表中  获取属于当前 商品  的类型属性 -->取出属性表中对应类型的数据,返回json格式
            $where = [
                'type_id'=>$type_id
            ];
            $attributes = Attribute::where($where)->select();
            //把符合的数据通过json形式返回给前端
            echo json_encode($attributes);
        }
    }

    public function index()
    {

        //获取商品基本数据
        $goods = Goods::alias('t1')
            ->field('t1.*,t2.cat_name')
            ->join('sh_category t2','t1.cat_id = t2.cat_id','left')
            ->select();

        return $this->fetch('',[
            'goods'=>$goods
        ]);
    }

    public function del()
    {
        //接收goods_id参数
        $goods_id = input('goods_id');

        //判断是否删除成功
        if (Goods::destroy($goods_id)) {
            //同时要删除商品属性表内的属性
            //这个可以写在good模型的删除事件之后执行(自己考虑的)

            $this->success('删除商品成功!',url('/admin/goods/index'));
        }else{
            $this->error('删除商品失败!');
        }
    }
}