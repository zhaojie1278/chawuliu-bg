<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class Index extends Common {
    /**
     * 返回轮播图
     */
    public function index() {
        // TODO
    }

    /**
     * 获取首页推荐专线
     */
    public function read() {
        $zhuanxians = model('zhuanxian')->getTuiZhuanxians();
        return show(config('code.success'), 'OK', $zhuanxians, 200);
    }
}