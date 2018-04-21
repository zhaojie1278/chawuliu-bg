<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class News extends Base {

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
    public function getNewsCount($condition = []) {
        $count = $this->where($condition)->count();
        return $count;
    }

    /**
     * API获取专线，分页
     */
    public function getNewsByPage($condition = [], $from = 0, $size = 10) {
        $order = ['create_time'=>'DESC'];
        $news = $this->field('id,title,titleimg,content,img1,img2,img3,img4,istop,status,update_time')->where($condition)->order($order)->select();
        return $news;
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
     * API获取用户信息，拼装用户公司图片介绍URL
     */
    public function getRealNews($ct) {
        // halt($ct);
        if (!empty($ct)) {
            $uptime = date('Y-m-d',strtotime($ct->update_time));
            $ct->uptime = $uptime;
            $ct->domaintitleimg = $ct->titleimg ? request()->domain().$ct->titleimg : '';
            $ct->domainimg1 = $ct->img1 ? request()->domain().$ct->img1 : '';
            $ct->domainimg2 = $ct->img2 ? request()->domain().$ct->img2 : '';
            $ct->domainimg3 = $ct->img3 ? request()->domain().$ct->img3 : '';
            $ct->domainimg4 = $ct->img4 ? request()->domain().$ct->img4 : '';
        }
        return $ct;
    }
}