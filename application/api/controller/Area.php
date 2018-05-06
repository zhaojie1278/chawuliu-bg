<?php
namespace app\api\controller;

use think\Controller;

/**
 * 微信权限相关信息:openid等
 */
class Area extends Controller {

    /**
     * 根据省市code动态获取数据
     */
    public function getArea() {
        // https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        $areaApiUrl = "http://japi.zto.cn/zto/api_utf8/baseArea?msg_type=GET_AREA&data=";
        $data = input('get.');
        // $data['code'] = '061kgJFD1QsbF306EKFD1QYYFD1kgJFC';
        if (empty($data)) {
            return show(config('code.error'), 'fail', [], 400);
        }
        $js_code = $data['code'];
        $areaApiUrl .= $js_code;
        $areaRes = file_get_contents($areaApiUrl);
        $areaResJson = json_decode($areaRes, true); //对JSON格式的字符串进行编码
        // halt($areaResJson);
        if (empty($areaResJson) || empty($areaResJson['status'])) {
            return show(config('code.error'), $areaResJson['message'], [], 400);
        }
        // $areaRes = get_object_vars($jsondecode);//转换成数组
        return show(config('code.success'), 'ok', $areaResJson['result'], 200);//输出openid
    }

    /**
     * 获取省份
     * @return json
     */
    public function getProvins() {
        $provinces = model('area')->getAllProvince();
        return show(config('code.success'), 'ok', $provinces, 200);
    }

    /**
     * 获取城市
     * @return json
     */
    public function getCitys() {
        $citys = model('area')->getCitysByCode();
        if (!empty($citys)) {
            $cCodes = array();
            foreach($citys as $p) {
                $cCodes[] = $p['code'];
            }
            // $cCodestr = implode(',', $cCodes);
            $areas = model('area')->getAreasByCodes($cCodes);
            if ($areas) {
                $areasKeyArr = array();
                foreach($areas as $a) {
                    $aCityCode = $a['citycode'];
                    $areasKeyArr[$aCityCode][] = $a;
                }

                foreach($citys as &$c) {
                    if (array_key_exists($c['code'], $areasKeyArr)) {
                        $c['areas'] = $areasKeyArr[$c['code']];
                    }
                }
            }
        }
        return show(config('code.success'), 'ok', $citys, 200);
    }

    /**
     * 获取县
     * @return json
     */
    public function getAreas() {
        $provinces = model('area')->getAreasByCode();
        return show(config('code.success'), 'ok', $provinces, 200);
    }

    public function getCode() {
        $code = model('area')->getCode();
        return show(config('code.success'), 'ok', $code, 200);
    }
}