<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:15
 */
namespace app\common\validate;

use think\Validate;

class Yaoqing extends Validate {
    protected $rule = ['id'=>'require|number','uid'=>'require|max:10','nickname'=>'require|max:20','iv_uid'=>'require|max:10','iv_nickname'=>'require|max:20'];
    protected $message = [
        'uid.require'=>'被邀请人ID不能为空',
        'uid.max'=>'被邀请人ID最长不能超过10个字符',
        'nickname.require'=>'被邀请人昵称不能为空',
        'nickname.max'=>'被邀请人昵称最长不能超过20个字符',
        'iv_uid.require'=>'邀请人ID不能为空',
        'iv_uid.max'=>'邀请人ID最长不能超过20个字符',
        'iv_nickname.require'=>'邀请人昵称不能为空',
        'iv_nickname.max'=>'邀请人昵称最长不能超过20个字符',
    ];

    protected $scene = [
        'add' => ['uid','nickname','iv_uid','iv_nickname'],
        'edit' => ['id','iv_uid','iv_nickname'],
    ];
}