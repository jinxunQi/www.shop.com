<?php
namespace app\admin\model;
use think\Model;
class Auth extends Model{
    protected $pk = 'auth_id';
    protected $autoWriteTimestamp = true;
    protected static function init()
    {
        Auth::event('before_update', function ($auth) {
            //当前为顶级权限的时候,需要把控制器名和方法名给清空之后再写入数据库中
            if ($auth['pid'] == 0) {
                $auth['auth_c'] = '';
                $auth['auth_a'] = '';
            }
        });
    }

    public function getSonAuth($data, $pid = 0, $level = 1)
    {
        static $result = [];
        foreach ($data as $k=>$v) {
            if ($v['pid']==$pid) {
                $v['level'] = $level;
                $result[] = $v;
                //移除已经判断过的元素 ,提升效率
                unset($data[$k]);
                //递归调用
                $this->getSonAuth($data,$v['auth_id'],$level+1);
            }
        }
        //返回递归后的结果
        return $result;
    }
}