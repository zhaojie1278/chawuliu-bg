<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use app\common\lib\Aes;
use app\common\lib\IAuth;
use app\common\lib\exception\ApiException;
use app\common\lib\Time;
use think\Cache;

/**
 * API模块公共控制器
 */
class Common extends Controller {

    // 分页相关
    public $page = 1;
    public $size = 5;
    public $from = 0;

    /**
     * Header 头
     */
    public $headers = [];

    /**
     * 初始化
     */
    public function _initialize() {
        // $this->checkRequestAuth(); // todo
        // $this->testAes();
    }

    /**
     * 校验数据是否合法
     */
    public function checkRequestAuth() {
        // 获取header version/sign/did 等

        Time::get13Timestamp();

        $headers = request()->header();
        //halt($headers);

        // 基础参数校验
        if (empty($headers['sign'])) {
            throw new ApiException('sign不存在', 400);
        }

        if (!in_array($headers['apptype'], config('app.apptypes'))) {
            throw new ApiException('apptype不合法', 400);
        }

        // sign 校验

        // sign 是否正确/且是否已过期
        if (!IAuth::checkSignPass($headers)) {
            throw new ApiException('授权码sign失败', 401);
        }
        // sign 是否已使用 file(一台机器)/mysql/redis(量大/分布式)
        Cache::set($headers['sign'], 1, config('app.app_sign_cache_time'));



        $this->headers = $headers;


        return false;
    }

    /**
     * 测试AES
     */
    public function testAes() {
        $str = 'did=123456&version=1.0';
        // echo ((new Aes())->encrypt($str));

        // $sign = "LsJ+wL4JwlXaEno2VSK/e4y+dv/YDtIAIVxoqLpl+7I=";
        // echo ((new Aes())->decrypt($sign));

        $data = [
            'did' => '123456',
            'version' => '1.0',
            'time' => Time::get13TimeStamp()
        ];
        // halt(Time::get13TimeStamp());
        $aesStr = IAuth::setSign($data);
        // echo $aesStr;
        // 79Uru+LWmzqVwBJRvgwfQwBxNlB05GrIpcTxeItgQiZQ7WXeUXo8LkFvIGPos8dy

        $str = "79Uru+LWmzqVwBJRvgwfQwBxNlB05GrIpcTxeItgQiZQ7WXeUXo8LkFvIGPos8dy";
        echo (new Aes())->decrypt($str);
        
        // echo '<br/';

        exit;
    }

    // 设置分页数据
    public function getPageAndSize($data) {
        $this->page = !empty($data['page']) ? $data['page'] : 1;
        $this->size = !empty($data['size']) ? $data['size'] : config('paginate.list_rows');
        $this->from = ($this->page - 1) * $this->size;
    }
}