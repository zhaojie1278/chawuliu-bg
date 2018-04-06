<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Sellmsg extends Base {

    // 获取所有可用专线
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
     * 根据联系人查询专线信息
     */
    public function getSellmsgsByCid($cid = 0) {
        $whereCond = [
            'cid'=>['EQ',$cid],
            'status'=>['EQ',config('code.status_normal')]
        ];
        $data = $this->where($whereCond)->order('update_time','desc')->select();
        return $data;
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
     * API获取专线总条数
     */
    public function getSellmsgsCount($condition = []) {
        $count = $this->where($condition)->count();
        return $count;
    }

    /**
     * API获取专线，分页
     */
    public function getSellmsgsByPage($condition = [], $from = 0, $size = 10) {
        $order = ['create_time'=>'DESC'];
        $data = $this->where($condition)->order($order)->select();
        $data = $this->getRealData($data);
        return $data;
    }

    /**
     * 获取专线信息，带有联系人信息
     */
    public function getRealData($sellmsgsData = []) {
        if (empty($sellmsgsData)) {
            return [];
        }
        
        $cids = [];
        foreach ($sellmsgsData as $k=>$val) {
            $cids[] = $val->cid;
        }
        $cids = array_unique($cids);
        $where['status'] = config('code.status_normal');
        $contacts = model('contact')->getAllContactInIds($cids, $where);

        if (empty($contacts)) {
            $contactNames = [];
        } else {
            foreach ($contacts as $v) {
                $contactNames[$v->id] = $v;
            }
        }

        $resultData = [];
        foreach ($sellmsgsData as $val) {
            // $val = $this->getImgSellmsg($val);
            $image = !empty($contactNames[$val->cid]) ? model('contact')->getShowImg($contactNames[$val->cid], true) : '';
            $val->avatarurl = $image;
            $val->timebefore = time_ago(strtotime($val->create_time));
            $val->company = !empty($contactNames[$val->cid]) ? $contactNames[$val->cid]->company : '';
            $val->imgcount = $this->getImgcount($val);
            
            $resultData[] = $val;
        }
        return $resultData;
    }

    /**
     * 获取图片数量
     */
    public function getImgcount($data) {
        $c = 0;
        if (!empty($data->img1)) {
            $c += 1;
        }
        if (!empty($data->img2)) {
            $c += 1;
        }
        if (!empty($data->img3)) {
            $c += 1;
        }
        if (!empty($data->img4)) {
            $c += 1;
        }
        return $c;
    }

    /**
     * API获取用户信息，拼装用户公司图片介绍URL
     */
    public function getImgSellmsg($data) {
        // halt($ct);
        if (!empty($data)) {
            $data->domainimg1 = $data->img1 ? request()->domain().$data->img1 : '';
            $data->domainimg2 = $data->img2 ? request()->domain().$data->img2 : '';
            $data->domainimg3 = $data->img3 ? request()->domain().$data->img3 : '';
            $data->domainimg4 = $data->img4 ? request()->domain().$data->img4 : '';
            $showimage = $this->getShowImg($data);
            $data->showimage = $showimage ? request()->domain().$showimage : '';
        }
        return $data;
    }
    
    /**
     * 获取展示的图片
     */
    public function getShowImg($data) {
        $img = '';
        if (!empty($data->img1)) {
            $img = $data->img1;
        } else if (!empty($data->img2)) {
            $img =  $data->img2;
        } else if (!empty($data->img3)) {
            $img =  $data->img3;
        } else if (!empty($data->img4)) {
            $img =  $data->img4;
        }
        return $img;
    }
}