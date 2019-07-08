<?php
namespace app\admin\model;
use think\Db;
use think\Model;
class Goods extends Model{
    protected $pk = 'goods_id';
    protected $autoWriteTimestamp = true;

    protected static function init()
    {
        //入库前的事件before_insert,给表单提交到数据库添加货号一栏
        Goods::event('before_insert', function ($goods) {
            //添加货号的信息
            $goods['goods_sn'] = date('ymdhis').uniqid();
        });

        //入库后的事件after_insert,完成商品属性的入库到商品属性表中(sh_goods_attr)
        Goods::event('after_insert', function ($goods) {
//            halt($goods);
            //$goods 当表单对象数据入库成功后,返回表的记录数据对象,其中带着自增主键goods_id
            $goods_id = $goods['goods_id'];
            $postData = input('post.');
            $goodsAttrValue = $postData['goodsAttrValue']; //也是个数组
            $goodsAttrPrice = $postData['goodsAttrPrice']; //是个数组
            //因为有多个属性,所以需要循环向商品属性表(sh_goods_attr)进行入库
            foreach ($goodsAttrValue as $attr_id =>$attr_value) {
                //单选属性$attr_value是一个数组
                if (is_array($attr_value)) {
                    foreach ($attr_value as $k=>$singel_attr_value) {
                        $data = [
                            'goods_id' => $goods_id,
                            'attr_id' => $attr_id,
                            'attr_value' => $singel_attr_value,
                            //通过下标获取单选属性对应的价格
                            'attr_price' => $goodsAttrPrice[$attr_id][$k],
                            'create_time' => time(),
                            'update_time' => time()
                        ];
                        //入库到商品属性表中
                        Db::name('goods_attr')->insert($data);
                    }
                }else{
                    //唯一属性 $attr_value是一个字符串
                    $data = [
                        'goods_id' => $goods_id,
                        'attr_id' => $attr_id,
                        'attr_value' => $attr_value,
                        'create_time' => time(),
                        'update_time' => time()
                    ];
                    //入库到商品属性表中
                    Db::name('goods_attr')->insert($data);
                }
            }
        });

        //---------------删除商品表后同时把当前商品的商品属性进行删除--------------------
        Goods::event('after_delete', function ($goods) {
            $goods_id = $goods['goods_id'];
            Db::name('goods_attr')->where(['goods_id'=>$goods_id])->delete();
        });
    }

    //图片文件上传方法
    public function uploadImg()
    {
        $goods_img = []; //用于存储文件上传的路径

        //接收图片文件上传的内容   (接收上传文件的name的名称)
        $files = request()->file('img');
//        halt($files);
        //判断有没有上传文件
        if ($files) {
            //定义文件上传的要求
            $validates = [
                'size' => 3*1024*1024,  //3m 文件上传的大小
                'ext' => 'jpg,png,jpeg,gif',//文件上传的后缀
            ];
            //文件上传的目录
            $uploadDir = './static/upload';

            //开始上传文件,因为是多文件上传,所以要循环上传
            foreach ($files as $file) {
                $info = $file->validate($validates)->move($uploadDir);
                if ($info) {
                    //存储文件的路径到数组中去
                    //getsaveName()返回的路径是反斜杠的,把反斜杠换成正斜杠
                    $goods_img[] = str_replace('\\','/',$info->getsaveName());
                }
            }
        }
        return $goods_img;
    }

    //缩略图生成方法
    public function thumb($goods_img)
    {
        //创建一个接收缩略图路径的数组
        $goods_middle = [];
        $goods_thumb = [];

        //$goods_img是原图的数组,里面包含多张原图,需要遍历后才能获取到某一张原图
        ###########    生成350*350缩略图  中图
        foreach ($goods_img as $path) { //$middle ====>  20190702/afdfad.png
            $arr_path = explode('/',$path); //用竖线把字符串炸开
            //缩略图的路径
            $middle_path = $arr_path[0].'/middle_'.$arr_path[1];

            #-------------------------tp5缩略图生成------------------------------------
            //1.打开原图
            $image = \think\Image::open( "./static/upload/".$path );

            //2.保存缩略图
            $image->thumb(350,350,2)->save('./static/upload/'.$middle_path);
            //保存 --> 缩略图(中图)的路径信息
            $goods_middle[] = $middle_path;
        }

        ###########    生成50*50缩略图
        foreach ($goods_img as $path) {  //---->得到某一个原图的路径字符信息
            $arr_path = explode('/',$path); //把字符串炸开
            //缩略图的路径(小图)
            $thumb_path = $arr_path[0].'/thumb_'.$arr_path[1];
            #-------------------------tp5缩略图生成------------------------------------
            //打开原图
            $image = \think\Image::open('./static/upload/'.$path);
            //保存缩略图(小图)  thumb方法中,第三个参数为2 代表填充补白
            $image->thumb(50,50,2)->save('./static/upload/'.$thumb_path);
            //把缩略图的路径信息保存起来
            $goods_thumb[] = $thumb_path;
        }

        //返回两个缩略图的路径信息
        return ['goods_middle'=>$goods_middle,'goods_thumb'=>$goods_thumb];

    }
}