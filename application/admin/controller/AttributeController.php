<?php
namespace app\admin\controller;
use app\admin\model\Attribute;
use app\admin\model\Type;

class AttributeController extends CommonController{
    public function add()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            //属性录入方式为列表输入的时候，需要验证属性值为必填
            if ($postData['attr_input_type'] == 1) {
                $result = $this->validate($postData,'Attribute.add',[],true);
            }else{
                //属性录入方式为手工输入的时候，不需要验证属性值
                $result = $this->validate($postData,'Attribute.except_attr_values',[],true);
            }

            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断入库是否成功
            $typeModel = new Attribute();
            if ($typeModel->allowField(true)->save($postData)) {
                $this->success('添加成功!',url('/admin/attribute/index'));
            }else{
                $this->error('添加失败!');
            }
        }

        //取出所有商品类型,分配到模板中去
        $types = Type::select();
        return $this->fetch('',[
            'types'=>$types,
        ]);
    }

    public function index()
    {
        //从Attribute表中获取所有属性数据，回显到模板中去
        $attributes = Attribute::alias('t1')
            ->field('t1.*,t2.type_name')
            ->join('sh_type t2','t1.type_id = t2.type_id','left')
            ->select();
        return $this->fetch('',[
            'attributes' => $attributes,
        ]);
    }

    public function upd()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            //根据属性录入方式 使用不同的验证器验证
            //  手工输入
            // 列表选择
            if ($postData['attr_input_type'] == 0) {
                $result = $this->validate($postData,'Attribute.except_attr_values',[],true);
            }else{
                $result = $this->validate($postData,'Attribute.upd',[],true);
            }
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断是否入库成功
            $attributeModel = new Attribute();
            if ($attributeModel->update($postData)) {
                $this->success('编辑成功!',url('/admin/attribute/index'));
            }else{
                $this->error('编辑失败!');
            }
        }

        //1.接收index页get方式提交的attr_id参数
        $attr_id = input('attr_id');
        //把商品类型表type的所有商品类型回显到模板中
        $types = Type::select();
        //把属性表attribute表中的当前行的属性回显到模板中
        $attribute = Attribute::find($attr_id);
        return $this->fetch('',[
            'types' => $types,
            'attribute' => $attribute,
        ]);
    }

    public function del()
    {
        //1.接收attr_id参数,记录当前条的信息
        $attr_id = input('attr_id');
        //2.判断是否删除失败
        if (Attribute::destroy($attr_id)) {
            $this->success('删除成功!',url('/admin/attribute/index'));
        }else{
            $this->error('删除失败!');
        }
    }
}