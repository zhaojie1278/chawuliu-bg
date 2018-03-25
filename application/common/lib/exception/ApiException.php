<?php

namespace app\common\lib\exception;
use think\Exception;

class ApiException extends Exception {

    public $code = 0;
    public $message = '';
    public $httpCode = 500;
    public function __construct($message = '', $httpCode = 0, $code = 0) {
        $this->code = $code;
        $this->message = $message;
        $this->httpCode = $httpCode;
    }
}