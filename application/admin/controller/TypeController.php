<?php
namespace app\admin\controller;
use app\admin\model\Attribute;
use app\admin\model\Type;
class TypeController extends CommonController{
    public function add()
    {
        //1.判断是否是post请求
        if (request()->isPost()) {
            //2.接收post参数
            $postData = input('post.');
            //3.验证器验证
            $result = $this->validate($postData,'Type.add',[],true);
            if ($result !== true) {
                $this->error(implode(',',$result));
            }
            //4.判断入库是否成功
            $typeModel = new Type();
            if ($typeModel->save($postData)) {
                $this->success('添加成功!',url('/admin/type/index'));
            }else{
                $this->error('添加失败!');
            }
        }
        return $this->fetch('');
    }

    public function index()
    {
        //获取type的所有数据,分配到模板中
        $types = Type::select();
        return $this->fetch('',[
            'types' => $types,
        ]);
    }

    public function upd()
    {
        //接收get方式提交的type_id
        $type_id = input('type_id');
        $type = Type::find($type_id);
        return $this->fetch('',[
            'type' => $type,
        ]);
    }

    public function del()
    {
        $type_id = input('type_id');
        if (Type::destroy($type_id)) {
            $this->success('删除成功!',url('/admin/type/index'));
        }else{
            $this->error('删除失败!');
        }
    }

    public function getAttr()
    {
        //1.接收当前提交的type_id
        $type_id = input('type_id');

        //单独获取attribute表中的attr_name字段的值
        $type_name = Type::where('type_id','=',$type_id)->value('type_name');
        $attributes = Attribute::where('type_id','=',$type_id)->select()->toArray();
//        halt($attributes);
        return $this->fetch('',[
            'attributes' => $attributes,
            'type_name' => $type_name,
        ]);
    }
}