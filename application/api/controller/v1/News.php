<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class News extends Common {

    /**
     * index
     */
    public function indextui() {
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
        $where['istop'] = 1;
        $this->getPageAndSize($data);
        try {
            $total = model('news')->getNewsCount($where);
            $news = model('news')->getNewsByPage($where, $this->from, $this->size);
            if ($news) {
                foreach($news as &$new) {
                    $new = model('news')->getRealNews($new);
                }
            }
        } catch (\Exception $e) {
            return show(config('code.error'), $e->getMessage(), [], 400);
        }
        // dump(model('news')->getLastSql());
        $result = [
            'total' => $total,
            'page_num' => ceil($total / $this->size),
            'list' => $news
        ];
        return show(config('code.success'), 'OK', $result, 200);
    }

    /**
     * 根据专线ID获取详细信息
     */
    public function detail() {
        $data = input('put.');
        if (empty($data['id'])) {
            return show(config('code.error'), 'id not send1', [], 400);
        }
        $whereCond = ['status'=>['EQ',config('code.status_normal')]];
        $result = model('news')->getById($data['id'], $whereCond);
        if ($result) {
            $result = model('news')->getRealNews($result);
        }
        return show(config('code.success'), 'OK', $result, 200);
    }
}