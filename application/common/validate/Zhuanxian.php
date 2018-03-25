<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:15
 */
namespace app\common\validate;

use think\Validate;

class Zhuanxian extends Validate {
    protected $rule = ['start'=>'require|max:20','point'=>'require|max:20','price_zhonghuo'=>'float','price_paohuo'=>'float','cid'=>'require|number'];
    protected $message = [
        'start.require'=>'出发地不能为空',
        'start.max'=>'出发地最长不能超过20个字符',
        'point.require'=>'目的地不能为空',
        'point.max'=>'目的地最长不能超过20个字符',
        'price_zhonghuo.float'=>'重货价格必须为金额',
        'price_paohuo.float'=>'泡货价格必须为金额',
        'cid.require'=>'联系人不能为空',
        'cid.number'=>'联系人数据不正确',
    ];
}