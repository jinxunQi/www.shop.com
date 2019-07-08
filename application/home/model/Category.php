<?php
namespace app\home\model;
use think\Model;
class Category extends Model{
    protected $pk = 'cat_id';
    protected $autoWriteTimestamp = true;

    //获取导航栏的分类数据
    public function getNavData($limit)
    {
        return $this->where("is_show",'=','1')->limit($limit)->select();

    }

    //面包屑导航分类的方法  获取父分类
    public function getFamilyCats($data,$cat_id)
    {
        static $result = [];
        //
        foreach ($data as $k=>$v) {
            //首先先找到自己
            if ($v['cat_id'] == $cat_id) {
                $result[] = $v;
                //移除已经判断过的元素
                unset($data[$k]);
                //递归找自己的祖先
                $this->getFamilyCats($data,$v['pid']);
            }
        }
        //返回递归好的元素
        //反转数组 array_reverse()
        return array_reverse($result);
    }


    //找子孙分类
    public function getSonsCatId($data,$cat_id)
    {
        static $sonsId = [];
        foreach ($data as $v) {
            if ($v['pid'] == $cat_id) {//找子孙分类
                $sonsId[] = $v['cat_id'];//只存储子孙的cat_id
                //递归调用,继续找子孙
                $this->getSonsCatId($data,$v['cat_id']);
            }
        }
        //返回递归数据
        return $sonsId;
    }
}