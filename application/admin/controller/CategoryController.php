<?php
namespace app\admin\controller;
use app\admin\model\Category;
class CategoryController extends CommonController{
    public function add()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'Category.add',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断入库是否成功
            $typeModel = new Category();
            if ($typeModel->allowField(true)->save($postData)) {
                $this->success('添加成功!',url('/admin/category/index'));
            }else{
                $this->error('添加失败!');
            }
        }

        //取出无限极分类的数据
        $catModel = new Category();
        $categorys = $catModel->getSonCats( $catModel->select() );
        return $this->fetch('',[
            'categorys'=>$categorys,

        ]);
    }

    public function index()
    {
        //获取无限极分类数据
        $categoryModel = new Category();
        $cats = $categoryModel->getSonCats( $categoryModel->select()->toArray() );
//       halt($cats);
       return $this->fetch('',[
            'cats'=>$cats
        ]);
    }

    public function upd()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'Category.upd',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断入库是否成功
            $categoryModel = new Category();
            if ($categoryModel->update($postData)) {
                $this->success('添加成功!',url('/admin/category/index'));
            }else{
                $this->error('添加失败!');
            }
        }

        //接收cat_id,记录当前条的数据
        $cat_id = input('cat_id');
        $categoryModel = new Category();
        //获取当前编辑条信息的内容
        $cat = $categoryModel->find($cat_id);
        //获取无限极分类----->用于显示父级分类的下拉内容
        $categorys = $categoryModel->getSonCats( $categoryModel->select() );
        return $this->fetch('',[
            'categorys'=>$categorys,
            'cat'=>$cat,
        ]);
    }

    public function del()
    {
        //1.接收cat_id获取当前条的信息
        $cat_id = input('cat_id');
        //2.判断是否删除成功
        if (Category::destroy($cat_id)) {
            $this->success('删除成功!',url('/admin/category/index'));
        }else{
            $this->error('删除失败!');
        }
    }
}