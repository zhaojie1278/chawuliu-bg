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

    /**
     * 根据ID获取联系人
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllContactInIds($ids) {
        $where = ['status'=>['NEQ',config('code.status_delete')]];
        if (empty($ids)) {
            exception('参数异常！获取信息失败！');
        }
        $where['id'] = ['IN',$ids];
        $contacts = $this->field('id,nickname,phone,company,address,img1')->where($where)->select();
        return $contacts;
    }
}