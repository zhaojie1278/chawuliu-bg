<?php
namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;
use app\api\controller\Common;

class Lanmu extends Common {
    /**
     * 返回栏目
     */
    public function read() {
        $lanmus = config('lanmu.lists');
        $result = [];
        foreach ($lanmus as $k=>$v) {
            $result[] = [
                'id' => $k,
                'name' => $v
            ];
        }
        return show(config('code.success'), 'OK', $result, 200);
    }
}