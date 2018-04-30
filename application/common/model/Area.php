<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Area extends Model {
    /**
     * 获取所有省份
     * @return array
     */
    public function getAllProvince() {
        $provinces = Db::name('province')->field('areaname,code')->select();
        return $provinces;
    }

    /**
     * 根据省获取市
     * @return array
     */
    public function getCitysByCode() {
        $data = input('get.');
        $where = [
            'provincecode' => $data['code']
        ];
        $citys = Db::name('city')->where($where)->field('areaname,code')->select();
        return $citys;
    }

    /**
     * 根据省获取市
     * @return array
     */
    public function getAreasByCode() {
        $data = input('get.');
        $where = [
            'citycode' => $data['code']
        ];
        $citys = Db::name('area')->where($where)->field('areaname,code')->select();
        return $citys;
    }

    /**
     * 获取省市代码
     * @return array
     */
    public function getCode() {
        $data = input('get.');
        $data['areaname'] = str_replace('市', '', $data['areaname']);
        $data['areaname'] = str_replace('地区', '', $data['areaname']);
        $where = [
            'areaname' => ['LIKE', $data['areaname'].'%']
        ];
        $areaCat = $data['areacat'];
        if ($areaCat == 'province') {
            $code = Db::name('province')->where($where)->field('code')->find();   
        } else if ($areaCat == 'city') {
            $code = Db::name('city')->where($where)->field('code')->find();   
        }
        return $code;
    }
}