<?php
namespace app\api\controller;

use think\Controller;

/**
 * 微信权限相关信息:openid等
 */
class Area extends Controller {

    /**
     * 获取OPENID信息
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
}