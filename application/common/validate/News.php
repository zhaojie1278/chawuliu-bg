<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:15
 */
namespace app\common\validate;

use think\Validate;

class News extends Validate {
    protected $rule = ['title'=>'require|max:100','content'=>'require|max:2000'];
    protected $message = [
        'title.require'=>'标题不能为空',
        'title.max'=>'标题不能超过100个字符',
        'content.require'=>'内容不能为空',
        'content.max'=>'内容最长不能超过2000个字符',
    ];
}