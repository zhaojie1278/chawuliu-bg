<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class Zhuanxian extends Common {
    /**
     * 返回轮播图
     */
    public function index($id = 0) {
        // halt(input());
        // TODO
    }

    // 发布专线
    public function add() {
        $data = input('post.');
        // TODO 程序的健壮性处理
        //入库操作
        try {
            if(empty($data['catname'])) {
                return show(config('code.error'), 'sorry, param error', [], 400);
            }
            $cats = config('zhuanxian.cat_flip');
            $data['cat'] = $cats[$data['catname']];
            // halt($data['cat']);
            if (empty($data['id'])) {
                unset($data['id']);
                $allList = array();
                $allList[] = $data;

                // 配载吊车处理多个目的地
                if (!empty($data['point2'])) {
                    $point2 = $data['point2'];
                    $data['point'] = $point2;

                    $allList[] = $data;
                }
                if (!empty($data['point3'])) {
                    $point3 = $data['point3'];
                    $data['point'] = $point3;
                    $allList[] = $data;
                }
                // 添加
                $id = model('common/Zhuanxian')->addAll($allList);
            } else {
                // 修改
                $id = model('common/Zhuanxian')->edit($data);
            }
        }catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        return show(config('code.success'), 'OK', ['id'=>$id], 200);
    }

    /**
     * 根据ID和用户ID删除专线(软删除)
     */
    public function delByIdCid() {
        $data = input('post.');
        if (empty($data['id']) || empty($data['cid'])) {
            return show(config('code.error'), 'sorry, param error', [], 400);
        }
        //入库操作
        try {
            $where['status'] = config('code.status_normal');
            $where['cid'] = $data['cid'];
            $zx = model('common/Zhuanxian')->getById($data['id'], $where);
            if ($zx) {
                $res = model('common/Zhuanxian')->del($data); // 软删除
            } else {
                $res = true;
            }
        }catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        return show(config('code.success'), 'OK', ['res'=>$res], 200);
    }
    
    /**
     * 首页推荐专线
     */
    public function indexTui() {
        try {
            $data = input('post.');
            if (!empty($data['start'])) {
                $condition['start'] = $data['start'];
            }
            if (!empty($data['point'])) {
                $condition['point'] = $data['point'];
            }

            $total = model('zhuanxian')->getTuiZhuanxianCount($condition);
            $this->getPageAndSize(input('get.'));
            $tuis = model('zhuanxian')->getTuiZhuanxians($condition, $this->from, $this->size);
            $total = count($tuis);
        } catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }

        $result = [
            'total' => $total,
            'page_num' => ceil($total / $this->size),
            'list' => $tuis
        ];
        return show(config('code.success'), 'OK', $result, 200);
    }

    /**
     * 获取查询专线
     */
    public function search() {
        // halt(request()->header());
        $data = input('post.');
        // halt($data);
        $where['status'] = config('code.status_normal');
        if (!empty($data['start'])) {
            $where['start'] = $data['start'];
        }
        if (!empty($data['point']) && $data['point'] != '请选择') {
            $where['point'] = $data['point'];
        }
        /*
        if (!empty($data['start_prov'])) {
            $where['start_prov'] = $data['start_prov'];
        }
        if (!empty($data['point_prov'])) {
            $where['point_prov'] = $data['point_prov'];
        }*/
        if (!empty($data['cat'])) {
            $where['cat'] = $data['cat'];
        } else {
            return show(config('code.error'), 'error param.', [], 400);
        }
        $this->getPageAndSize($data);
        try {
            $total = model('zhuanxian')->getZhuanxiansCount($where);
            $zhuanxians = model('zhuanxian')->getZhuanxiansByPage($where, $this->from, $this->size);
        } catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        // dump(model('zhuanxian')->getLastSql());
        $result = [
            'total' => $total,
            'page_num' => ceil($total / $this->size),
            'list' => $zhuanxians
        ];
        return show(config('code.success'), 'OK', $result, 200);
    }
    
    /**
     * 根据Openid获取用户所有专线
     */
    public function getAllByOpenid() {
        $data = input('post.');
        if (empty($data['openid'])) {
            return show(config('code.error'), 'sorry, param error', [], 400);
        }
        $where['status'] = config('code.status_normal');
        $zhuanxians = [];
        try {
            // 获取用户信息
            $openidContact = model('contact')->getByOpenid($data['openid'], $where);
            // halt($openidContact->toArray());
            if ($openidContact) {
                $zhuanxians = model('zhuanxian')->getZhuanxiansByCid($openidContact->id);
            } else {
                return show(config('code.error'), 'openid is not exist', [], 400);
            }
        } catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        // dump(model('zhuanxian')->getLastSql());
        return show(config('code.success'), 'OK', ['list' => $zhuanxians], 200);
    }

    /**
     * 根据专线ID获取详细信息
     */
    public function detail() {
        $data = input('put.');
        if (empty($data['id']) || empty($data['cid'])) {
            return show(config('code.error'), 'id not send1', [], 400);
        }
        $whereCond = ['status'=>['EQ',config('code.status_normal')], 'cid'=>$data['cid']];
        $result = model('zhuanxian')->getById($data['id'], $whereCond);
        // $result = model('zhuanxian')->getRealZhuanxian($result);
        return show(config('code.success'), 'OK', $result, 200);
    }
}