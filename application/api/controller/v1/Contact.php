<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class Contact extends Common {
    /**
     * 返回轮播图
     */
    public function index($id = 0) {
        // halt(input());
        // TODO
    }

    /**
     * 根据专线ID获取详细信息
     */
    public function detail() {
        $data = input('post.');
        if (empty($data['cid']) || empty($data['openid'])) {
            return show(config('code.error'), 'sorry, param error', [], 400);
        }
        
        $whereCond = ['status'=>['EQ',config('code.status_normal')]];
        
        try {
            $detailContact = model('contact')->getById($data['cid'], $whereCond);
            $openidContact = model('contact')->getByOpenid($data['openid'], $whereCond);
        } catch(\Exception $e) {
            return show(config('code.error'), 'id get user is empty.', [], 500);
        }

        if (!empty($detailContact)) {
            $detailContact = model('contact')->getRealContact($detailContact);
            $zhuanxians = model('zhuanxian')->getZhuanxiansByCid($data['cid']);
            // dump($zhuanxians);
            if (!empty($zhuanxians)) {
                $detailContact->zhuanxians = array_chunk($zhuanxians, 2);
            } else {
                $detailContact->zhuanxians = array();
            }
            // 获取收藏的物流公司
            // $openid

            $isfav = false;
            if (!empty($openidContact)) {
                $isfav = model('fav')->getByCidFavid($openidContact->id, $detailContact->id);
            }
            $detailContact->isfav = $isfav ? true : false;
            return show(config('code.success'), 'OK', $detailContact, 200);
        } else {
            return show(config('code.error'), 'User is not exist.', [], 401);
        }
    }

    /**
     * 根据OPENID获取用户信息
     */
    public function detailByOpenid() {
        $data = input('post.');
        if (empty($data['openid'])) {
            return show(config('code.error'), 'no openid', [], 401);
        }

        $openid = $data['openid'];
        $whereCond = ['status'=>['EQ',config('code.status_normal')]];
        try {
            $contact = model('contact')->getByOpenid($openid, $whereCond);
            $contact = model('contact')->getRealContact($contact);
            // sleep(2);
        } catch(\Exception $e) {
            return show(config('code.error'), 'openid get error.', [], 500);
        }
        if (!empty($contact)) {
            return show(config('code.success'), 'OK', $contact, 200);
        } else {
            return show(config('code.error'), 'openid get user is not exist.', [], 401);
        }
    }

    /**
     * 添加公司信息
     */
    public function addCompany() {
        $data = input('post.');
        if (empty($data['openid']) || empty($data['company']) || empty($data['nickname']) || empty($data['phone']) || empty($data['address'])) {
            return show(config('code.error'), '参数错误.', [], 400);
        }
        if (empty($data['img4']) && empty($data['img1']) && empty($data['img2']) && empty($data['img3'])) {
            return show(config('code.error'), '图片至少一张', [], 400);
        }

        // 正常的用户
        $whereCond = ['status'=>['EQ',config('code.status_normal')]];

        // 邀请人openid
        if (!empty($data['fromooid'])) {
            // 获取ID
            $ivContact = model('contact')->getByOpenid($data['fromooid'], $whereCond);
            if (!empty($ivContact)) {
                $data['iv_uid'] = $ivContact->id;
            }
        }

        // 去除ID key，否则 $this->id 获取不到自增ID
        if (array_key_exists('id', $data)) {
            unset($data['id']);
        }

        try {
            // 判断openid是否已存在
            $openid = $data['openid'];
            $contact = model('contact')->getByOpenid($openid, $whereCond);
            if ($contact) {
                return show(config('code.error'), '此用户已存在，添加失败', [], 400);
            }
            $rs = model('contact')->add($data);
        } catch(\Exceptioin $e) {
            return show(config('code.error'), $e->getMessage(), [], 500);
        }
        if (!$rs) {
            return show(config('code.error'), '抱歉，系统异常，添加失败，请稍后重试', [], 400);            
        }
        $data = ['newid'=>$rs];
        return show(config('code.success'), 'OK', $data, 200);
    }

    /**
     * 修改公司信息
     */
    public function updateCompany() {
        $data = input('post.');
        if (empty($data['id']) || empty($data['openid']) || empty($data['company']) || empty($data['nickname']) || empty($data['phone']) || empty($data['address'])) {
            return show(config('code.error'), '参数错误.', [], 400);
        }
        if (empty($data['img4']) && empty($data['img1']) && empty($data['img2']) && empty($data['img3'])) {
            return show(config('code.error'), '图片至少一张', [], 400);
        }

        try {
            // 判断ID是否存在
            $id = $data['id'];
            $whereCond = ['status'=>['EQ',config('code.status_normal')]];
            $contact = model('contact')->getById($id, $whereCond);
            if (!$contact) {
                return show(config('code.error'), '用户不存在，修改失败', [], 400);
            }
            $rs = model('contact')->edit($data);
        } catch(\Exceptioin $e) {
            return show(config('code.error'), $e->getMessage(), [], 500);
        }
        if (!$rs) {
            return show(config('code.error'), '抱歉，系统异常，添加失败，请稍后重试', [], 400);            
        }
        $data = ['newid'=>$rs];
        return show(config('code.success'), 'OK', $data, 200);
    }

    /**
     * 收藏/取消收藏操作
     */
    public function favCompany() {
        $data = input('post.');
        // halt($data);
        if (empty($data['cid']) || !isset($data['isfav']) || empty($data['openid'])) {
            return show(config('code.error'), '参数错误.', [], 400);
        }

        try {
            $isfav = $data['isfav'];
            // 收藏
            // 判断ID是否存在
            $whereCond = ['status'=>['EQ',config('code.status_normal')]];
            $openidContact = model('contact')->getByOpenid($data['openid'], $whereCond);
            if (!$openidContact) {
                return show(config('code.error'), '用户不存在，收藏失败', [], 400);
            }

            // 要收藏的物流公司ID
            $cid = $data['cid'];

            if ($isfav) {
                // 取消收藏
                $isfav = model('fav')->getByCidFavid($openidContact->id, $cid);
                if ($isfav) {
                    $delData = ['id' => $isfav->id];
                    $rs = model('fav')->del($delData, true);
                } else {
                    $rs = true;
                }
            } else {
                $saveData = [
                    'cid' => $openidContact->id,
                    'favcid' => $cid
                ];
                $rs = model('fav')->add($saveData);
            }
        } catch(\Exceptioin $e) {
            return show(config('code.error'), $e->getMessage(), [], 500);
        }
        if (!$rs) {
            return show(config('code.error'), '抱歉，系统异常，添加失败，请稍后重试', [], 400);            
        }
        return show(config('code.success'), 'OK', ['isfav'=>$isfav], 200);
    }
}