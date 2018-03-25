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
        //入库操作
        try {
            $id = model('common/Zhuanxian')->add($data);
        }catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        return show(config('code.success'), 'OK', ['id'=>$id], 200);
    }
    
    /**
     * 首页推荐专线
     */
    public function indexTui() {
        try {
            $total = model('zhuanxian')->getTuiZhuanxianCount();
            $this->getPageAndSize(input('get.'));
            $tuis = model('zhuanxian')->getTuiZhuanxians($this->from, $this->size);
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
        if (!empty($data['start'])) {
            $where['start'] = $data['start'];
        }
        if (!empty($data['point'])) {
            $where['point'] = $data['point'];
        }
        $where['status'] = config('code.status_normal');
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
     * 根据专线ID获取详细信息
     */
/*     public function detail($id) {
        if (empty($id)) {
            return show(config('code.error'), 'id not send', [], 400);
        }
        $whereCond = ['id'=>$id, 'status'=>['EQ',config('code.status_normal')]];
        $result = model('zhuanxian')->getById($id, $whereCond);
        // $result = model('zhuanxian')->getRealZhuanxian($result);
        return show(config('code.success'), 'OK', $result, 200);
    } */
}