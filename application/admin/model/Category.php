<?php
namespace app\admin\model;
use think\Model;
class Category extends Model{
    protected $pk = 'cat_id';
    protected $autoWriteTimestamp = true;

    //无限极分类的方法
    public function getSonCats($data, $pid = 0, $level = 1)
    {
        static $result = [];
        foreach ($data as $k=>$v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level;
                //让$v的cat_id作为$result的下标
                $result[ $v['cat_id'] ] = $v;
                //移除已经判断过的元素
                unset($data[$k]);
                //递归调用自己
                $this->getSonCats($data,$v['cat_id'],$level+1);
            }
        }
        //返回递归好的分类
        return $result;
    }

}