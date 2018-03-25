<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 3:01
 */

return [
    'password_halt' => '_$cwl_pwd', // 密码加密盐
    'aeskey' => 'qaz123abc3211234', // aes 密钥，服务的和客户端一致
    'apptypes' => ['IOS','WAFER'], // APP 类型
    'app_sign_time' => 100000, // SIGN 有效期
    'app_sign_cache_time' => 200000, // SIGN 缓存失效时间

];