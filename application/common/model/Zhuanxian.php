<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Zhuanxian extends Base {

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
    public function getZhuanxiansByCid($cid = 0, $limit = 0) {
        $whereCond = [
            'cid'=>['EQ',$cid],
            'status'=>['EQ',config('code.status_normal')]
        ];
        if ($limit) {
            $data = $this->where($whereCond)->order('update_time','desc')->limit($limit)->select();
        } else {
            $data = $this->where($whereCond)->order('update_time','desc')->select();
        }

        if ($data) {
            $data = $this->getShowZx($data);
        }
        return $data;
    }

    // 补全在客户端展示的 start point 值
    public function getShowZxSingle($data) {
        if (!empty($data['start'])) {
        } else if (!empty($data['start_city'])) {
            $data['start'] = $data['start_city'];
        } else if (!empty($data['start_prov'])) {
            $data['start'] = $data['start_prov'];
        } else {
            $data = false; // 过滤不正常数据
        }
        if ($data) {
            if (!empty($data['point'])) {
            } else if (!empty($data['point_city'])) {
                $data['point'] = $data['point_city'];
            } else if (!empty($data['point_prov'])) {
                $data['point'] = $data['point_prov'];
            } else {
                $data = false; // 过滤不正常数据
            }
        }
        return $data;
    }
    public function getShowZx($data) {
        $newData = [];
        foreach($data as $k=>$v) {
            if (!empty($v['start'])) {
            } else if (!empty($v['start_city'])) {
                $v['start'] = $v['start_city'];
            } else if (!empty($v['start_prov'])) {
                $v['start'] = $v['start_prov'];
            } else {
                continue; // 过滤不正常数据
            }

            if (!empty($v['point'])) {
            } else if (!empty($v['point_city'])) {
                $v['point'] = $v['point_city'];
            } else if (!empty($v['point_prov'])) {
                $v['point'] = $v['point_prov'];
            } else {
                continue; // 过滤不正常数据
            }
            $newData[$k] = $v;
        }
        return $newData;
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
     * API获取推荐专线数量
     */
    public function getTuiZhuanxianCount($condition = []) {
        $where = [
            'status'=>config('code.status_normal'),
            // 'isrecommend'=>config('code.recommend')
        ];
        if ($condition) {
            $where = array_merge($where, $condition);
        }
        return $this->where($where)->count();
    }
    /**
     * API获取推荐专线
     */
    public function getTuiZhuanxians($condition = [], $from = 0, $size = 10) {
        $where = [
            'status'=>config('code.status_normal'),
            // 'isrecommend'=>config('code.recommend')
        ];
        
        if ($condition) {
            $where = array_merge($where, $condition);
        }
        $order = ['istop'=>'DESC', 'create_time'=>'DESC'];
        // $zhuanxians = $this->field('id,start,point,cid,nickname,phone,address')->where($where)->group('cid')->order($order)->limit($from, $size)->select();
        $zhuanxians = $this->field('id,start_prov,start_city,start,point_prov,point_city,point,cid,nickname,phone,address')->where($where)->order($order)->select();
        // echo $this->getLastSql();
        $realZhuanxians = $this->getRealZhuanxians($zhuanxians);
        return $realZhuanxians;
    }

    /**
     * API获取专线总条数
     */
    public function getZhuanxiansCount($condition = []) {
        $count = $this->where($condition)->count();
        return $count;
    }

    /**
     * API获取专线，分页
     */
    public function getZhuanxiansByPage($condition = [], $from = 0, $size = 10) {
        $order = ['istop'=>'DESC', 'create_time'=>'DESC'];
        // echo $this->getLastSql();
        // $zhuanxians = $this->field('id,start,point,cid,nickname,phone,address')->where($condition)->order($order)->limit($from, $size)->select();
        $zhuanxians = $this->field('id,start_prov,start_city,start,point_prov,point_city,point,cid,nickname,phone,address')->where($condition)->order($order)->select();
        // echo $this->getLastSql();
        $realZhuanxians = $this->getRealZhuanxians($zhuanxians);
        return $realZhuanxians;
    }

    /**
     * 获取专线信息，带有联系人信息
     */
    public function getRealZhuanxians($zhuanxians = []) {
        if (empty($zhuanxians)) {
            return [];
        }
        
        $cids = [];
        foreach ($zhuanxians as $k=>$zx) {
            $cids[] = $zx->cid;
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
        foreach ($zhuanxians as $zx) {
            // dump($zx->toArray());
            $zx = $this->getShowZxSingle($zx);
            if ($zx) {
                $image = !empty($contactNames[$zx->cid]) ? model('contact')->getShowImg($contactNames[$zx->cid]) : '';
                $resultData[] = [
                    'id' => $zx['id'],
                    'start' => $zx['start'],
                    'point' => $zx['point'],
                    'cid' => $zx['cid'],
                    'nickname' => $zx['nickname'],
                    'phone' => $zx['phone'],
                    'address' => $zx['address'],
                    'company' => !empty($contactNames[$zx->cid]) ? $contactNames[$zx->cid]->company : '',
                    // 'nickname' => !empty($contactNames[$zx->cid]) ? $contactNames[$zx->cid]->nickname : '',
                    // 'phone' => !empty($contactNames[$zx->cid]) ? $contactNames[$zx->cid]->phone : '',
                    // 'address' => !empty($contactNames[$zx->cid]) ? $contactNames[$zx->cid]->address : '',
                    'image' => $image ? request()->domain().$image : '',
                ];
            }
        }
        return $resultData;
    }
}