<?php
namespace app\api\controller;

use think\Controller;

/**
 * 国外地区获取
 */
class Foreign extends Controller {

    /**
     * 获取省份
     * @return json
     */
    public function getFirst() {
        $isFirst = true;
        $provinces = model('foreign')->getAllForeign($isFirst);
        return show(config('code.success'), 'ok', $provinces, 200);
    }

    /**
     * 获取城市
     * @return json
     */
    public function getSecond() {
        $data = input('get.');
        $firstcode = $data['code'];
        $provinces = model('foreign')->getSecondAreasByCode($firstcode);
        return show(config('code.success'), 'ok', $provinces, 200);
    }
/*
    public function getCode() {
        $code = model('area')->getCode();
        return show(config('code.success'), 'ok', $code, 200);
    }*/
}