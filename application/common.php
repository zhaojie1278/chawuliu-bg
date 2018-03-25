<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 通用化API接口输出
 * @param int $status 业务状态码
 */
function show($status, $message, $data = [], $httpcode = 200) {
    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
    return json($data, $httpcode);
}