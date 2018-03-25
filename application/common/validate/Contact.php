<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:15
 */
namespace app\common\validate;

use think\Validate;

class Contact extends Validate {
    protected $rule = ['nickname'=>'require|max:20','phone'=>'require|max:11','company'=>'require|max:100','address'=>'require|max:200'];
    protected $message = [
        'nickname.require'=>'联系人不能为空',
        'nickname.max'=>'联系人最长不能超过20个字符',
        'phone.require'=>'手机号码不能为空',
        'phone.max'=>'手机号码最长不能超过11个字符',
        'company.require'=>'公司名称不能为空',
        'company.max'=>'公司名称最长不能超过100个字符',
        'address.require'=>'地址不能为空',
        'address.max'=>'地址最长不能超过200个字符',
    ];
}