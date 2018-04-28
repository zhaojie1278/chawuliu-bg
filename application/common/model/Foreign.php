<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Foreign extends Base {

    // 获取所有
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
     * API获取专线总条数
     */
    public function getForeignCount($condition = []) {
        $count = $this->where($condition)->count();
        return $count;
    }

    /**
     * API获取专线，分页
     */
    public function getForeignByPage($condition = [], $from = 0, $size = 10) {
        $order = ['create_time'=>'DESC'];
        $foreign = $this->field('id,title,titleimg,content,img1,img2,img3,img4,istop,status,update_time')->where($condition)->order($order)->select();
        return $foreign;
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
     * 获取最大code
     * @param $id
     * @return null|static
     */
    public function getMaxCode() {
        return $this->max('code');
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
     * 根据code获取值
     * @param $id
     * @return null|static
     */
    public function getByCode($code, $whereCond = []) {
        if (empty($whereCond)) {
            $whereCond = ['status'=>['NEQ',config('code.status_delete')]];
        }
        $whereCond['code'] = $code;
        return $this->get($whereCond);
    }

    /**
     * 根据code获取值
     * @param $id
     * @return null|static
     */
    public function getSecondAreasByCode($code, $whereCond = []) {
        if (empty($whereCond)) {
            $whereCond = ['status'=>['NEQ',config('code.status_delete')]];
        }
        $whereCond['firstcode'] = $code;
        return $this->where($whereCond)->select();
    }

    /**
     * 所有可用的一级地区
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllForeign($isFirst = false) {
        $where = ['status'=>['NEQ',config('code.status_delete')]];
        if ($isFirst) {
            $where['firstcode'] = ['EQ', '0'];
        }
        $foreigns = $this->field('id,code,areaname,firstcode,firstname')->where($where)->select();
        if($foreigns) {
            $foreigns = collection($foreigns)->toArray();
        }
        return $foreigns;
    }
}