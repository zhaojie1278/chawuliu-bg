<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class Zhaopin extends Common {
    // 发布
    public function add() {
        $data = input('post.');
        // TODO 程序的健壮性处理
        //入库操作
        try {
            if(empty($data['catname'])) {
                return show(config('code.error'), 'sorry, param error', [], 400);
            }
            $cats = config('zhaopin.zhaopin_flip');
            $data['cat'] = $cats[$data['catname']];

            // 判断图片至少一张
            /* if (empty($data['img1']) && empty($data['img2']) && empty($data['img3']) && empty($data['img4'])) {
                return show(config('code.error'), '请上传至少一张图片', [], 400);
            } */

            $data = $this->addAreaHandle($data);
            
            foreach($data as $k=>$v) {
                if ($v == '请选择') {
                    $data[$k] = '';
                    // dump($v);
                }
            }
            // dump($data['sex']);

            // halt($data['cat']);
            if (empty($data['id'])) {
                // 添加
                $id = model('common/Zhaopin')->add($data);
            } else {
                // 修改
                $id = model('common/Zhaopin')->edit($data);
            }
        }catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        return show(config('code.success'), 'OK', ['id'=>$id], 200);
    }

    /**
     * 根据ID和用户ID删除(软删除)
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
            $zhaopin = model('common/Zhaopin')->getById($data['id'], $where);
            if ($zhaopin) {
                $res = model('common/Zhaopin')->del($data); // 软删除
            } else {
                $res = true;
            }
        }catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        return show(config('code.success'), 'OK', ['res'=>$res], 200);
    }
    

    /**
     * 获取查询超级买卖
     */
    public function search() {
        // halt(request()->header());
        $data = input('post.');
        // halt($data);
        $where['status'] = config('code.status_normal');
        $prov = '';
        $city = '';

        if (!empty($data['prov'])) {
            // $where['address'] = ['LIKE', '%'.$data['prov'].'%'];
            $prov = $data['prov'];

            $prov = str_replace('市', '', $prov);
            $prov = str_replace('省', '', $prov);
        }
        if (!empty($data['city'])) {
            /* if (!empty($data['prov'])) {
                $where2['address'] = ['LIKE', '%'.$data['city'].'%'];
            } else {
                $where['address'] = ['LIKE', '%'.$data['city'].'%'];
            } */
            $city = str_replace('市', '', $city);
            $city = str_replace('省', '', $city);
        }
        if (!empty($data['cat'])) {
            $where['cat'] = $data['cat'];
        }
        if (!empty($data['worktype'])) {
            $where['worktype'] = $data['worktype'];
        }
        $this->getPageAndSize($data);
        try {
            $total = model('zhaopin')->getZhaopinsCount($where, $prov, $city);
            // halt(model('zhaopin')->getLastSql());
            $zhaopins = model('zhaopin')->getZhaopinsByPage($where, $this->from, $this->size, $prov, $city);
        } catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        // dump(model('zhaopin')->getLastSql());
        $result = [
            'total' => $total,
            'page_num' => ceil($total / $this->size),
            'list' => $zhaopins
        ];
        return show(config('code.success'), 'OK', $result, 200);
    }
    
    /**
     * 根据Openid获取用户所有超级买卖
     */
    public function getAllByOpenid() {
        $data = input('post.');
        if (empty($data['openid'])) {
            return show(config('code.error'), 'sorry, param error', [], 400);
        }
        $where['status'] = config('code.status_normal');
        $zhaopins = [];
        try {
            // 获取用户信息
            $openidContact = model('contact')->getByOpenid($data['openid'], $where);
            // halt($openidContact->toArray());
            if ($openidContact) {
                $zhaopins = model('zhaopin')->getZhaopinsByCid($openidContact->id);
            } else {
                return show(config('code.error'), 'openid is not exist', [], 400);
            }
        } catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        // dump(model('zhuanxian')->getLastSql());
        return show(config('code.success'), 'OK', ['list' => $zhaopins], 200);
    }

    /**
     * 获取详细信息
     */
    public function detail() {
        $data = input('put.');
        if (empty($data['id']) || empty($data['cid'])) {
            return show(config('code.error'), 'id not send1', [], 400);
        }
        $whereCond = ['status'=>['EQ',config('code.status_normal')], 'cid'=>$data['cid']];
        $data = model('zhaopin')->getById($data['id'], $whereCond);
        $result = model('zhaopin')->getImgZhaopin($data);
        return show(config('code.success'), 'OK', $result, 200);
    }

    /**
     * 获取详细信息
     */
    public function detailById() {
        $data = input('put.');
        if (empty($data['id']) || empty($data['openid'])) { // Openid 只做判断是否传递处理，其他严格校验未做验证
            return show(config('code.error'), 'id not send222', [], 400);
        }
        $whereCond = ['status'=>['EQ',config('code.status_normal')]];
        $data = model('zhaopin')->getById($data['id'], $whereCond);
        $data = model('zhaopin')->getRealDataSingle($data);
        $data = model('zhaopin')->setShowQuyu($data);
        $result = model('zhaopin')->getImgZhaopin($data);
        return show(config('code.success'), 'OK', $result, 200);
    }
}