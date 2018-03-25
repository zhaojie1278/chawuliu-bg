<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Yaoqing extends Base {

    /**
     * 根据联系人和邀请人获取邀请信息
     * @param $uid
     * @param $ivUid
     * @return array|false|\PDOStatement|string|Model
     */
    public function getByUidIvUid($uid,$ivUid) {
        $where['uid'] = $uid;
        $where['iv_uid'] = $ivUid;

        $iv = $this->where($where)->find();
        return $iv;
    }

    /**
     * 添加
     * @param $data
     * @param null $dupfield
     * @param null $dupmsg
     * @return mixed
     */
    public function add($data, $dupfield = null, $dupmsg = null)
    {
        $validate = validate('Yaoqing');
        if (!$validate->scene('add')->check($data)) {
            exception($validate->getError());
        }
        return parent::add($data, $dupfield, $dupmsg);
    }

    // 获取所有可用
    public function getAll($where) {
        $data = $this->where($where)->select();
        if($data) {
            $data = collection($data)->toArray();
        }
        return $data;
    }

    /**
     * 删除
     * @param $data
     * @return false|int
     */
    public function del($data, $isPhysical = true)
    {
        return parent::del($data, true);
    }

    /**
     * 根据ID获取值
     * @param $id
     * @return null|static
     */
    public function getById($id) {
        $whereCond = ['id'=>$id];
        $yaoqing = $this->get($whereCond);
        return $yaoqing;
    }
}