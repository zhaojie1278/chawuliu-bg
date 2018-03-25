<?php
namespace app\common\lib\exception;

use think\exception\Handle;

class ApiHandleException extends Handle {

    // http 状态吗
    protected $httpCode = 500;

    public function render(\Exception $e) {
        if (config('app_debug') === true) {
            return parent::render($e);
        }
        if ($e instanceof ApiException) {
            $this->httpCode = $e->httpCode;
        }
        return show(0, $e->getMessage(), [], $this->httpCode);
    }
}