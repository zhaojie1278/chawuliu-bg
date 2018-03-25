<?php
/**
 * Created by PhpStorm.
 * User: 24200
 * Date: 2018/3/18
 * Time: 12:54
 */
return [
    'default_return_type'=>'json',
    'wafer_config' => [
        'appid' => 'wx12de72cf50e9bb0c',
        'appsecret' => 'e0072f74012ce315413e23d3cc5c2723',
        'wxurl' => 'https://api.weixin.qq.com/sns/jscode2session?',
        'grant_type' => 'authorization_code',
    ],
    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 10,
    ],
];