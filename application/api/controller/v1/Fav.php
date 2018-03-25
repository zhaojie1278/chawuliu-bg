<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class Fav extends Common {

    /**
     * 根据openid获取用户收藏的物流公司
     */
    public function getFavorsByCid() {
        $data = input('post.');
        if (empty($data['openid'])) {
            return show(config('code.error'), 'no openid', [], 401);
        }

        $openid = $data['openid'];
        $whereCond = ['status'=>['EQ',config('code.status_normal')]];
        try {
            $openidContact = model('contact')->getByOpenid($openid, $whereCond);
            // sleep(2);
        } catch(\Exception $e) {
            return show(config('code.error'), 'openid get error.', [], 500);
        }
        if (!empty($openidContact)) {
            try {
                // 获取收藏的物流公司
                $favors = model('fav')->getByCid($openidContact->id);
            } catch(\Exception $e) {
                return show(config('code.error'), 'openid get error.', [], 500);
            }
            if ($favors) {
                $favors = collection($favors)->toArray();
                $favcids = array_column($favors, 'favcid');
                $favcids = array_filter($favcids);
                // $favcidsStr = implode(',', $favcids);
                $where['status'] = config('code.status_normal');
                try {
                    $favors = model('contact')->getAllContactInIds($favcids, $where);
                } catch(\Exception $e) {
                    return show(config('code.error'), 'favor\'s cids get error.', [], 500);
                }
                
            }
            return show(config('code.success'), 'OK', ['list'=>$favors], 200);
        } else {
            return show(config('code.error'), 'openid get user is not exist.', [], 401);
        }
    }
}