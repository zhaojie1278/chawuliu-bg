<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:15
 */
namespace app\common\validate;

use think\Validate;

class Foreign extends Validate {
    protected $rule = ['areaname'=>'require|max:100']; // ,'code'=>'require|max:10'
    protected $message = [
        'areaname.require'=>'地区名称不能为空',
        'areaname.max'=>'地区名称不能超过100个字符',
        // 'code.require'=>'code不能为空',
        // 'code.max'=>'code最长不能超过10个字符',
    ];
}