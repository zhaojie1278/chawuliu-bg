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
            // $data['point_area2'] = str_replace('区', '', $data['point_area2']);
            if (mb_strlen($data['start_prov'],'utf-8') > 2) {
                $data['start_prov'] = preg_replace('/(.*)省/u','$1', $data['start_prov']);
                $data['start_prov'] = preg_replace('/(.*)市/u','$1', $data['start_prov']);
            }
        } else {
            $data['start_prov'] = '';
        }
        if (!empty($data['start_city'])) {
            if (stripos($data['start_city'], '辖区') !== false || stripos($data['start_city'], '辖县') !== false) {
                $data['start_city'] = '';
            } else {
                if (mb_strlen($data['start_city'],'utf-8') > 2) {
                    // $data['start_city'] = preg_replace('/(.*)市/u','$1', $data['start_city']);
                    $data['start_city'] = preg_replace('/(.*)市/u','$1',$data['start_city']);
                    $data['start_city'] = preg_replace('/(.*)县/u','$1',$data['start_city']);                
                }
            }
        } else {
            $data['start_city'] = '';
        }
        if (!empty($data['start_area'])) {
            if (stripos($data['start_area'], '辖区') !== false || stripos($data['start_area'], '辖县') !== false) {
                $data['start_area'] = '';
            } else {
                if (mb_strlen($data['start_area'],'utf-8') > 2) {
                    $data['start_area'] = preg_replace('/(.*)市/u','$1',$data['start_area']);
                    $data['start_area'] = preg_replace('/(.*)区/u','$1',$data['start_area']);
                }
            }
            $data['start'] = $data['start_area'];
        } else {
            $data['start'] = '';
        }
        if (!empty($data['point_prov'])) {
            if (mb_strlen($data['point_prov'],'utf-8') > 2) {
                $data['point_prov'] = preg_replace('/(.*)省/u','$1',$data['point_prov']);
                $data['point_prov'] = preg_replace('/(.*)市/u','$1',$data['point_prov']);
            }
        } else {
            $data['point_prov'] = '';
        }
        if (!empty($data['point_city'])) {
            if (stripos($data['point_city'], '辖区') !== false || stripos($data['point_city'], '辖县') !== false) {
                $data['point_city'] = '';
            } else {
                if (mb_strlen($data['point_city'],'utf-8') > 2) {
                    $data['point_city'] = preg_replace('/(.*)市/u','$1',$data['point_city']);
                    $data['point_city'] = preg_replace('/(.*)县/u','$1',$data['point_city']);
                }
            }
        } else {
            $data['point_city'] = '';
        }
        if (!empty($data['point_area'])) {
            if (stripos($data['point_area'], '辖区') !== false || stripos($data['point_area'], '辖县') !== false) {
                $data['point_area'] = '';
            } else {
                if (mb_strlen($data['point_area'],'utf-8') > 2) {
                    $data['point_area'] = preg_replace('/(.*)市/u','$1',$data['point_area']);
                    $data['point_area'] = preg_replace('/(.*)区/u','$1',$data['point_area']);
                }
            }
        } else {
            $data['point_area'] = '';
        }
        if (!empty($data['point_prov2'])) {
                if (mb_strlen($data['point_prov2'],'utf-8') > 2) {
                    $data['point_prov2'] = preg_replace('/(.*)省/u','$1', $data['point_prov2']);
                    $data['point_prov2'] = preg_replace('/(.*)市/u','$1', $data['point_prov2']);
                }
        } else {
            $data['point_prov2'] = '';
        }
        if (!empty($data['point_city2'])) {
            if (stripos($data['point_city2'], '辖区') !== false || stripos($data['point_city2'], '辖县') !== false) {
                $data['point_city2'] = '';
            } else {
                if (mb_strlen($data['point_city2'],'utf-8') > 2) {
                    $data['point_city2'] = preg_replace('/(.*)市/u','$1', $data['point_city2']);
                    $data['point_city2'] = preg_replace('/(.*)县/u','$1', $data['point_city2']);
                }
            }
        } else {
            $data['point_city2'] = '';
        }
        if (!empty($data['point_area2'])) {
            if (stripos($data['point_area2'], '辖区') !== false || stripos($data['point_area2'], '辖县') !== false) {
                $data['point_area2'] = '';
            } else {
                if (mb_strlen($data['point_area2'],'utf-8') > 2) {
                    $data['point_area2'] = preg_replace('/(.*)市/u','$1', $data['point_area2']);
                    $data['point_area2'] = preg_replace('/(.*)区/u','$1', $data['point_area2']);
                }
            }
        } else {
            $data['point_area2'] = '';
        }
        if (!empty($data['point_prov3'])) {
                if (mb_strlen($data['point_prov3'],'utf-8') > 2) {
                    $data['point_prov3'] = preg_replace('/(.*)省/u','$1', $data['point_prov3']);
                    $data['point_prov3'] = preg_replace('/(.*)市/u','$1', $data['point_prov3']);
                }
        } else {
            $data['point_prov3'] = '';
        }
        if (!empty($data['point_city3'])) {
            if (stripos($data['point_city3'], '辖区') !== false || stripos($data['point_city3'], '辖县') !== false) {
                $data['point_city3'] = '';
            } else {
                if (mb_strlen($data['point_city3'],'utf-8') > 2) {
                    $data['point_city3'] = preg_replace('/(.*)市/u','$1', $data['point_city3']);
                    $data['point_city3'] = preg_replace('/(.*)县/u','$1', $data['point_city3']);
                }
            }
        } else {
            $data['point_city3'] = '';
        }
        if (!empty($data['point_area3'])) {
            if (stripos($data['point_area3'], '辖区') !== false || stripos($data['point_area3'], '辖县') !== false) {
                $data['point_area3'] = '';
            } else {
                if (mb_strlen($data['point_area3'],'utf-8') > 2) {
                    $data['point_area3'] = preg_replace('/(.*)市/u','$1', $data['point_area3']);
                    $data['point_area3'] = preg_replace('/(.*)区/u','$1', $data['point_area3']);
                }
            }
        } else {
            $data['point_area3'] = '';
        }
        if (!empty($data['point_prov4'])) {
                if (mb_strlen($data['point_prov4'],'utf-8') > 2) {
                    $data['point_prov4'] = preg_replace('/(.*)省/u','$1', $data['point_prov4']);
                    $data['point_prov4'] = preg_replace('/(.*)市/u','$1', $data['point_prov4']);
                }
        } else {
            $data['point_prov4'] = '';
        }
        if (!empty($data['point_city4'])) {
            if (stripos($data['point_city4'], '辖区') !== false || stripos($data['point_city4'], '辖县') !== false) {
                $data['point_city4'] = '';
            } else {
                if (mb_strlen($data['point_city4'],'utf-8') > 2) {
                    $data['point_city4'] = preg_replace('/(.*)市/u','$1', $data['point_city4']);
                    $data['point_city4'] = preg_replace('/(.*)县/u','$1', $data['point_city4']);
                }
            }
        } else {
            $data['point_city4'] = '';
        }
        if (!empty($data['point_area4'])) {
            if (stripos($data['point_area4'], '辖区') !== false || stripos($data['point_area4'], '辖县') !== false) {
                $data['point_area4'] = '';
            } else {
                if (mb_strlen($data['point_area4'],'utf-8') > 2) {
                    $data['point_area4'] = preg_replace('/(.*)市/u','$1', $data['point_area4']);
                    $data['point_area4'] = preg_replace('/(.*)区/u','$1', $data['point_area4']);
                }
            }
        } else {
            $data['point_area4'] = '';
        }
        if (!empty($data['point_prov5'])) {
                if (mb_strlen($data['point_prov5'],'utf-8') > 2) {
                    $data['point_prov5'] = preg_replace('/(.*)省/u','$1', $data['point_prov5']);
                    $data['point_prov5'] = preg_replace('/(.*)市/u','$1', $data['point_prov5']);
                }
        } else {
            $data['point_prov5'] = '';
        }
        if (!empty($data['point_city5'])) {
            if (stripos($data['point_city5'], '辖区') !== false || stripos($data['point_city5'], '辖县') !== false) {
                $data['point_city5'] = '';
            } else {
                if (mb_strlen($data['point_city5'],'utf-8') > 2) {
                    $data['point_city5'] = preg_replace('/(.*)市/u','$1', $data['point_city5']);
                    $data['point_city5'] = preg_replace('/(.*)县/u','$1', $data['point_city5']);
                }
            }

        } else {
            $data['point_city5'] = '';
        }
        if (!empty($data['point_area5'])) {
            if (stripos($data['point_area5'], '辖区') !== false || stripos($data['point_area5'], '辖县') !== false) {
                $data['point_area5'] = '';
            } else {
                if (mb_strlen($data['point_area5'],'utf-8') > 2) {
                    $data['point_area5'] = preg_replace('/(.*)市/u','$1', $data['point_area5']);
                    $data['point_area5'] = preg_replace('/(.*)区/u','$1', $data['point_area5']);
                }
            }
            
            // $data['point_area5'] = preg_replace('/(.*)县/u','$1', $data['point_area5']);
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
        if (!empty($data['start_prov']) && false === stripos($data['start_prov'], '辖区') && false === stripos($data['start_prov'], '辖县')) {
                if (mb_strlen($data['start_prov'],'utf-8') > 2) {
                    $data['start_prov'] = preg_replace('/(.*)省/u','$1', $data['start_prov']);
                    $data['start_prov'] = preg_replace('/(.*)市/u','$1', $data['start_prov']);
                }
                $where['start_prov'] = $data['start_prov'];
        }
        if (!empty($data['start_city']) && false === stripos($data['start_city'], '辖区') && false === stripos($data['start_city'], '辖县')) {
                if (mb_strlen($data['start_city'],'utf-8') > 2) {
                    $data['start_city'] = preg_replace('/(.*)市/u','$1', $data['start_city']);
                    $data['start_city'] = preg_replace('/(.*)县/u','$1', $data['start_city']);
                }
            $where['start_city'] = $data['start_city'];
        }
        if (!empty($data['start_area']) && false === stripos($data['start_area'], '辖区') && false === stripos($data['start_area'], '辖县')) {
                if (mb_strlen($data['start_area'],'utf-8') > 2) {
                    $data['start_area'] = preg_replace('/(.*)市/u','$1', $data['start_area']);
                    $data['start_area'] = preg_replace('/(.*)县/u','$1', $data['start_area']);
                }
            // $where['start'] = $data['start_area'];
            $where['start'] = ['LIKE',$data['start_area'].'%'];
        }
        if (!empty($data['point_prov']) && false === stripos($data['point_prov'], '辖区') && false === stripos($data['point_prov'], '辖县')) {
                if (mb_strlen($data['point_prov'],'utf-8') > 2) {
                    $data['point_prov'] = preg_replace('/(.*)省/u','$1', $data['point_prov']);
                    $data['point_prov'] = preg_replace('/(.*)市/u','$1', $data['point_prov']);
                }
            $where['point_prov'] = $data['point_prov'];
        }
        if (!empty($data['point_city']) && false === stripos($data['point_city'], '辖区') && false === stripos($data['point_city'], '辖县')) {
                if (mb_strlen($data['point_city'],'utf-8') > 2) {
                    $data['point_city'] = preg_replace('/(.*)市/u','$1', $data['point_city']);
                    $data['point_city'] = preg_replace('/(.*)县/u','$1', $data['point_city']);
                }
            // $where['point_city'] = $data['point_city'];
            $where['point_city'] = ['LIKE',$data['point_city'].'%'];
        }
        if (!empty($data['point_area']) && false === stripos($data['point_area'], '辖区') && false === stripos($data['point_area'], '辖县')) {
                if (mb_strlen($data['point_area'],'utf-8') > 2) {
                    $data['point_area'] = preg_replace('/(.*)市/u','$1', $data['point_area']);
                    $data['point_area'] = preg_replace('/(.*)县/u','$1', $data['point_area']);
                }
            // $where['point'] = $data['point_area'];
            $where['point'] = ['LIKE',$data['point_area'].'%'];
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
        if (!empty($data['prov']) && false === stripos($data['prov'], '辖区') && false === stripos($data['prov'], '辖县')) {
                if (mb_strlen($data['prov'],'utf-8') > 2) {
                    $data['prov'] = preg_replace('/(.*)省/u','$1', $data['prov']);
                    $data['prov'] = preg_replace('/(.*)市/u','$1', $data['prov']);
                }
            $where['prov'] = $data['prov'];
        }
        if (!empty($data['city']) && false === stripos($data['city'], '辖区') && false === stripos($data['city'], '辖县')) {
                if (mb_strlen($data['city'],'utf-8') > 2) {
                    $data['city'] = preg_replace('/(.*)市/u','$1', $data['city']);
                    $data['city'] = preg_replace('/(.*)县/u','$1', $data['city']);
                }
            // $where['city'] = $data['city'];
            $where['city'] = ['LIKE',$data['city'].'%'];
        }
        if (!empty($data['area']) && false === stripos($data['area'], '辖区') && false === stripos($data['area'], '辖县')) {
                if (mb_strlen($data['area'],'utf-8') > 2) {
                    $data['area'] = preg_replace('/(.*)市/u','$1', $data['area']);
                    $data['area'] = preg_replace('/(.*)县/u','$1', $data['area']);
                }
            // $where['area'] = $data['area'];
            $where['area'] = ['LIKE',$data['area'].'%'];
        }
        return $where;
    }

    /**
     * 地区数据处理 - 买卖、招聘
     * @param  array $data
     * @return array
     */
    public function addAreaHandle($data) {
        if (!empty($data['prov'])) {
                if (mb_strlen($data['prov'],'utf-8') > 2) {
                    $data['prov'] = preg_replace('/(.*)省/u','$1', $data['prov']);
                    $data['prov'] = preg_replace('/(.*)市/u','$1', $data['prov']);
                }
        }
        if (!empty($data['city'])) {
            if (stripos($data['city'], '辖区') !== false || stripos($data['city'], '辖县') !== false) {
                $data['city'] = '';
            } else {
                if (mb_strlen($data['city'],'utf-8') > 2) {
                    $data['city'] = preg_replace('/(.*)市/u','$1', $data['city']);
                    $data['city'] = preg_replace('/(.*)县/u','$1', $data['city']);
                }
            }
        }
        if (!empty($data['area'])) {
            if (stripos($data['area'], '辖区') !== false || stripos($data['area'], '辖县') !== false) {
                $data['area'] = '';
            } else {
                // $data['area'] = preg_replace('/(.*)市/u','$1', $data['area']);
                if (mb_strlen($data['area'],'utf-8') > 2) {
                    $data['area'] = preg_replace('/(.*)区/u','$1', $data['area']);
                    $data['area'] = preg_replace('/(.*)县/u','$1', $data['area']);
                }
            }
        }
        return $data;
    }
}