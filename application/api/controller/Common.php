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

    /**
     * 地区数据处理 - 专线
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function zhuanxianAddArea($data) {
        if (!empty($data['start_prov'])) {
            $data['start_prov'] = str_replace('省', '', $data['start_prov']);
            $data['start_prov'] = str_replace('市', '', $data['start_prov']);
        } else {
            $data['start_prov'] = '';
        }
        if (!empty($data['start_city'])) {
            $data['start_city'] = str_replace('市', '', $data['start_city']);
            // $data['start_city'] = str_replace('区', '', $data['start_city']);
            $data['start_city'] = str_replace('县', '', $data['start_city']);
        } else {
            $data['start_city'] = '';
        }
        if (!empty($data['start_area'])) {
            $data['start_area'] = str_replace('市', '', $data['start_area']);
            // $data['start_area'] = str_replace('区', '', $data['start_area']);
            $data['start_area'] = str_replace('县', '', $data['start_area']);
            $data['start'] = $data['start_area'];
        } else {
            $data['start'] = '';
        }
        if (!empty($data['point_prov'])) {
            $data['point_prov'] = str_replace('省', '', $data['point_prov']);
            $data['point_prov'] = str_replace('市', '', $data['point_prov']);
        } else {
            $data['point_prov'] = '';
        }
        if (!empty($data['point_city'])) {
            $data['point_city'] = str_replace('市', '', $data['point_city']);
            // $data['point_city'] = str_replace('区', '', $data['point_city']);
            $data['point_city'] = str_replace('县', '', $data['point_city']);
        } else {
            $data['point_city'] = '';
        }
        if (!empty($data['point_area'])) {
            $data['point_area'] = str_replace('市', '', $data['point_area']);
            // $data['point_area'] = str_replace('区', '', $data['point_area']);
            $data['point_area'] = str_replace('县', '', $data['point_area']);
            $data['point'] = $data['point_area'];
        } else {
            $data['point'] = '';
        }
        if (!empty($data['point_prov2'])) {
            $data['point_prov2'] = str_replace('省', '', $data['point_prov2']);
            $data['point_prov2'] = str_replace('市', '', $data['point_prov2']);
        } else {
            $data['point_prov2'] = '';
        }
        if (!empty($data['point_city2'])) {
            $data['point_city2'] = str_replace('市', '', $data['point_city2']);
            // $data['point_city2'] = str_replace('区', '', $data['point_city2']);
            $data['point_city2'] = str_replace('县', '', $data['point_city2']);
        } else {
            $data['point_city2'] = '';
        }
        if (!empty($data['point_area2'])) {
            $data['point_area2'] = str_replace('市', '', $data['point_area2']);
            // $data['point_area2'] = str_replace('区', '', $data['point_area2']);
            $data['point_area2'] = str_replace('县', '', $data['point_area2']);
        } else {
            $data['point_area2'] = '';
        }
        if (!empty($data['point_prov3'])) {
            $data['point_prov3'] = str_replace('省', '', $data['point_prov3']);
            $data['point_prov3'] = str_replace('市', '', $data['point_prov3']);
        } else {
            $data['point_prov3'] = '';
        }
        if (!empty($data['point_city3'])) {
            $data['point_city3'] = str_replace('市', '', $data['point_city3']);
            // $data['point_city3'] = str_replace('区', '', $data['point_city3']);
            $data['point_city3'] = str_replace('县', '', $data['point_city3']);
        } else {
            $data['point_city3'] = '';
        }
        if (!empty($data['point_area3'])) {
            $data['point_area3'] = str_replace('市', '', $data['point_area3']);
            // $data['point_area3'] = str_replace('区', '', $data['point_area3']);
            $data['point_area3'] = str_replace('县', '', $data['point_area3']);
        } else {
            $data['point_area3'] = '';
        }
        if (!empty($data['point_prov4'])) {
            $data['point_prov4'] = str_replace('省', '', $data['point_prov4']);
            $data['point_prov4'] = str_replace('市', '', $data['point_prov4']);
        } else {
            $data['point_prov4'] = '';
        }
        if (!empty($data['point_city4'])) {
            $data['point_city4'] = str_replace('市', '', $data['point_city4']);
            // $data['point_city4'] = str_replace('区', '', $data['point_city4']);
            $data['point_city4'] = str_replace('县', '', $data['point_city4']);
        } else {
            $data['point_city4'] = '';
        }
        if (!empty($data['point_area4'])) {
            $data['point_area4'] = str_replace('市', '', $data['point_area4']);
            // $data['point_area4'] = str_replace('区', '', $data['point_area4']);
            $data['point_area4'] = str_replace('县', '', $data['point_area4']);
        } else {
            $data['point_area4'] = '';
        }
        if (!empty($data['point_prov5'])) {
            $data['point_prov5'] = str_replace('省', '', $data['point_prov5']);
            $data['point_prov5'] = str_replace('市', '', $data['point_prov5']);
        } else {
            $data['point_prov5'] = '';
        }
        if (!empty($data['point_city5'])) {
            $data['point_city5'] = str_replace('市', '', $data['point_city5']);
            // $data['point_city5'] = str_replace('区', '', $data['point_city5']);
            $data['point_city5'] = str_replace('县', '', $data['point_city5']);
        } else {
            $data['point_city5'] = '';
        }
        if (!empty($data['point_area5'])) {
            $data['point_area5'] = str_replace('市', '', $data['point_area5']);
            // $data['point_area5'] = str_replace('区', '', $data['point_area5']);
            $data['point_area5'] = str_replace('县', '', $data['point_area5']);
        } else {
            $data['point_area5'] = '';
        }
        return $data;
    }

    /**
     * 根据传递参数获取出发地目的地
     * @param  array $data
     * @return array
     */
    public function getZhuanxianAreaWhere($data) {
        $where = [];
        if (!empty($data['start_prov'])) {
            $data['start_prov'] = str_replace('省', '', $data['start_prov']);
            $data['start_prov'] = str_replace('市', '', $data['start_prov']);
            $where['start_prov'] = $data['start_prov'];
        }
        if (!empty($data['start_city'])) {
            $data['start_city'] = str_replace('市', '', $data['start_city']);
            // $data['start_city'] = str_replace('区', '', $data['start_city']);
            $data['start_city'] = str_replace('县', '', $data['start_city']);
            $where['start_city'] = $data['start_city'];
        }
        if (!empty($data['start_area'])) {
            $data['start_area'] = str_replace('市', '', $data['start_area']);
            // $data['start_area'] = str_replace('区', '', $data['start_area']);
            $data['start_area'] = str_replace('县', '', $data['start_area']);
            $where['start'] = $data['start_area'];
        }
        if (!empty($data['point_prov'])) {
            $data['point_prov'] = str_replace('省', '', $data['point_prov']);
            $data['point_prov'] = str_replace('市', '', $data['point_prov']);
            $where['point_prov'] = $data['point_prov'];
        }
        if (!empty($data['point_city'])) {
            $data['point_city'] = str_replace('市', '', $data['point_city']);
            // $data['point_city'] = str_replace('区', '', $data['point_city']);
            $data['point_city'] = str_replace('县', '', $data['point_city']);
            $where['point_city'] = $data['point_city'];
        }
        if (!empty($data['point_area'])) {
            $data['point_area'] = str_replace('市', '', $data['point_area']);
            // $data['point_area'] = str_replace('区', '', $data['point_area']);
            $data['point_area'] = str_replace('县', '', $data['point_area']);
            $where['point'] = $data['point_area'];
        }
        return $where;
    }

    /**
     * 根据传递参数获取地区
     * @param  array $data
     * @return array
     */
    public function getAreaWhere($data) {
        $where = [];
        if (!empty($data['prov'])) {
            $data['prov'] = str_replace('省', '', $data['prov']);
            $data['prov'] = str_replace('市', '', $data['prov']);
            $where['prov'] = $data['prov'];
        }
        if (!empty($data['city'])) {
            $data['city'] = str_replace('市', '', $data['city']);
            // $data['city'] = str_replace('区', '', $data['city']);
            $data['city'] = str_replace('县', '', $data['city']);
            $where['city'] = $data['city'];
        }
        if (!empty($data['area'])) {
            $data['area'] = str_replace('市', '', $data['area']);
            // $data['area'] = str_replace('区', '', $data['area']);
            $data['area'] = str_replace('县', '', $data['area']);
            $where['area'] = $data['area'];
        }
        return $where;
    }
}