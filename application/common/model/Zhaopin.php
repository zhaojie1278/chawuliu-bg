<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Zhaopin extends Base {

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
    public function getZhaopinsByCid($cid = 0) {
        $whereCond = [
            'cid'=>['EQ',$cid],
            'status'=>['EQ',config('code.status_normal')]
        ];
        $data = $this->where($whereCond)->order('update_time','desc')->select();
        foreach($data as &$v) {
            $v = $this->setShowQuyu($v);
        }
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
    public function getZhaopinsCount($condition = []) {
        /*if (!empty($prov) && $prov == $city) {
            $city = '';
        }
        if (!empty($prov) && !empty($city)) {
            $count = $this->where($condition)->where(function ($query) use($prov, $city) {
                $query->where('address', 'like', '%'.$prov.'%')->whereOr('address', 'like', '%'.$city.'%');
            })->count();
        } else if (!empty($prov)) {
            $condition['address'] = ['LIKE', '%'.$prov.'%'];
            $count = $this->where($condition)->count();
        } else if (!empty($city)) {
            $condition['address'] = ['LIKE', '%'.$city.'%'];
            $count = $this->where($condition)->count();
        } else {
            $count = $this->where($condition)->count();            
        }*/
        $count = $this->where($condition)->count();
        return $count;
    }

    /**
     * API获取专线，分页
     */
    public function getZhaopinsByPage($condition = [], $from = 0, $size = 10, $prov = '', $city = '') {
        $order = ['create_time'=>'DESC'];
        /*if (!empty($prov) && $prov == $city) {
            $city = '';
        }
        if (!empty($prov) && !empty($city)) {
            $data = $this->where($condition)->where(function ($query) use($prov, $city) {
                $query->where('address', 'like', '%'.$prov.'%')->whereOr('address', 'like', '%'.$city.'%');
            })->order($order)->select();
        } else if (!empty($prov)) {
            $condition['address'] = ['LIKE', '%'.$prov.'%'];
            $data = $this->where($condition)->order($order)->select();
        } else if (!empty($city)) {
            $condition['address'] = ['LIKE', '%'.$city.'%'];
            $data = $this->where($condition)->order($order)->select();
        } else {
            $data = $this->where($condition)->order($order)->select();            
        }*/
        $data = $this->where($condition)->order($order)->select();
        // halt($this->getLastSql());
        $data = $this->getRealData($data);
        return $data;
    }

    /**
     * 获取专线信息，带有联系人信息
     */
    public function getRealData($zhaopinsData = []) {
        if (empty($zhaopinsData)) {
            return [];
        }
        
        $cids = [];
        foreach ($zhaopinsData as $k=>$val) {
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
        foreach ($zhaopinsData as $val) {
            // $val = $this->getImgzhaopin($val);
            $image = !empty($contactNames[$val->cid]) ? model('contact')->getShowImg($contactNames[$val->cid], true) : '';
            $val->avatarurl = $image;
            $val->timebefore = time_ago(strtotime($val->create_time));
            $val->company = !empty($contactNames[$val->cid]) ? $contactNames[$val->cid]->company : '';
            $val->imgcount = $this->getImgcount($val);
            
            $resultData[] = $val;
        }
        return $resultData;
    }

    // 设置显示区域
    public function setShowQuyu($data) {
        if (!empty($data['area'])) {
            $data['quyu'] = $data['area'];
        } else if (!empty($data['city'])) {
            $data['quyu'] = $data['city'];
        } else if (!empty($data['prov'])) {
            $data['quyu'] = $data['prov'];
        } else {
            $data = false; // 过滤不正常数据
        }
        return $data;
    }

    /**
     * 获取专线信息，带有联系人信息
     */
    public function getRealDataSingle($zhaopin = null) {
        if (empty($zhaopin)) {
            return null;
        }
        
        $where['status'] = config('code.status_normal');
        $contact = model('contact')->getById($zhaopin->cid, $where);
        if (empty($contact)) {
            return null;
        }

        $image = model('contact')->getShowImg($contact, true);
        $zhaopin->avatarurl = $image;
        $zhaopin->timebefore = time_ago(strtotime($zhaopin->create_time));
        $zhaopin->company = $contact->company;
        $zhaopin->imgcount = $this->getImgcount($zhaopin);
        return $zhaopin;
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
    public function getImgZhaopin($data, $getshowImg = false) {
        // halt($ct);
        if (!empty($data)) {
            $data->domainimg1 = $data->img1 ? request()->domain().$data->img1 : '';
            $data->domainimg2 = $data->img2 ? request()->domain().$data->img2 : '';
            $data->domainimg3 = $data->img3 ? request()->domain().$data->img3 : '';
            $data->domainimg4 = $data->img4 ? request()->domain().$data->img4 : '';
            if ($getshowImg) {
                $showimage = $this->getShowImg($data);
                $data->showimage = $showimage ? request()->domain().$showimage : '';
            }
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