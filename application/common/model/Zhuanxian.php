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
    public function getZhuanxiansByCid($cid = 0) {
        $whereCond = [
            'cid'=>['EQ',$cid],
            'status'=>['EQ',config('code.status_normal')]
        ];
        $data = $this->where($whereCond)->select();
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
     * API获取推荐专线数量
     */
    public function getTuiZhuanxianCount() {
        $where = [
            'status'=>config('code.status_normal'),
            'isrecommend'=>config('code.recommend')
        ];
        return $this->where($where)->count();
    }
    /**
     * API获取推荐专线
     */
    public function getTuiZhuanxians($from = 0, $size = 10) {
        $where = [
            'status'=>config('code.status_normal'),
            'isrecommend'=>config('code.recommend')
        ];
        $order = ['create_time'=>'DESC'];
        $zhuanxians = $this->field('id,start,point,cid,nickname,phone,address')->where($where)->group('cid')->order($order)->limit($from, $size)->select();
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
        $order = ['create_time'=>'DESC'];
        // echo $this->getLastSql();
        $zhuanxians = $this->field('id,start,point,cid,nickname,phone,address')->where($condition)->order($order)->limit($from, $size)->select();
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
        return $resultData;
    }
}