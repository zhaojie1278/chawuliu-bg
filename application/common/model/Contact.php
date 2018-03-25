<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Contact extends Base {

    /**
     * 获取邀请人ID
     * @param $id
     * @return int
     */
    public function getIvUid($id) {
        $contact = $this->get($id);
        if ($contact) {
            return $contact->iv_uid;
        } else {
            return 0;
        }
    }

    // 获取所有可用联系人
    public function getAll($where) {
        $whereCond = ['status'=>['NEQ',config('code.status_delete')]];
        if ($where) {
            $whereCond = array_merge($whereCond,$where);
        }
        $data = $this->where($whereCond)->select();
        if($data) {
            $data = collection($data)->toArray();
        }
        return $data;
    }

    /**
     * 所有可用的联系人
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllContact() {
        $where = ['status'=>['NEQ',config('code.status_delete')]];
        $contacts = $this->field('id,openid,nickname,phone,address,company')->where($where)->select();
        if($contacts) {
            $contacts = collection($contacts)->toArray();
        }
        return $contacts;
    }

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

    /**
     * 新增
     * @param $data
     * @return mixed
     */
    public function add($data,$f=null,$m=null) {
//        return parent::add($data,'openid','此微信号已存在');
        return parent::add($data);
    }

    /**
     * 根据ID获取值
     * @param $id
     * @return null|static
     */
    public function getById($id, $whereCond = []) {
        if (empty($whereCond)) {
            $whereCond = ['status'=>['NEQ',config('code.status_delete')]];
        }
        $whereCond['id'] = $id;
        return $this->get($whereCond);
    }

    /**
     * API根据OPENID获取用户信息
     * @param $id
     * @return null|static
     */
    public function getByOpenid($openid, $whereCond = []) {
        if (empty($whereCond)) {
            $whereCond = ['status'=>['NEQ',config('code.status_delete')]];
        }
        $whereCond['openid'] = $openid;
        return $this->get($whereCond);
    }

    /**
     * API获取用户信息，拼装用户公司图片介绍URL
     */
    public function getRealContact($ct) {
        // halt($ct);
        if (!empty($ct)) {
            $ct->domainimg1 = $ct->img1 ? request()->domain().$ct->img1 : '';
            $ct->domainimg2 = $ct->img2 ? request()->domain().$ct->img2 : '';
            $ct->domainimg3 = $ct->img3 ? request()->domain().$ct->img3 : '';
            $ct->domainimg4 = $ct->img4 ? request()->domain().$ct->img4 : '';
        }
        return $ct;
    }
}