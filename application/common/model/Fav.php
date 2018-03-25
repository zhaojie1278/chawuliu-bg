<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Fav extends Base {

    protected $updatetime = false;

    /**
     * get favors by contact id
     */
    public function getByCid($cid) {
        if (empty($cid)) {
            exception('参数异常！获取信息失败！');
        }
        $where['cid'] = ['EQ',$cid];
        $favors = $this->where($where)->order('create_time','DESC')->select();
        return $favors;
    }

    /**
     * get favors by open-contact id and contact id
     */
    public function getByCidFavid($cid, $favid) {
        if (empty($cid) || empty($favid)) {
            exception('参数异常！获取信息失败！');
        }
        $where['cid'] = ['EQ',$cid];
        $where['favcid'] = ['EQ',$favid];
        $favor = $this->get($where);
        return $favor;
    }
}