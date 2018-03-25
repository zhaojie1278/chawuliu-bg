<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
Route::get('test','api/test/index');
Route::put('test/:id','api/test/update');
Route::delete('test/:id','api/test/delete');

Route::resource('test','api/test');

// API接口数据获取及处理
Route::get('api/:ver/lanmu', 'api/:ver.lanmu/read');
Route::get('api/:ver/index','api/:ver.index/read');

// 专线
Route::get('api/:ver/zhuanxian/tui','api/:ver.zhuanxian/indextui'); // 首页推荐专线
Route::post('api/:ver/zhuanxian/search','api/:ver.zhuanxian/search');
Route::post('api/:ver/zhuanxian/add','api/:ver.zhuanxian/add');
Route::get('api/:ver/zhuanxian/:id','api/:ver.zhuanxian/detail');

// 用户
Route::get('api/:ver/contact/:id','api/:ver.contact/detail'); // 详情
Route::post('api/:ver/contact/isregcompany','api/:ver.contact/detailbyopenid'); // 详情
Route::post('api/:ver/contact/companyinfo','api/:ver.contact/detailbyopenid'); // 详情
Route::post('api/:ver/contact/addcompany','api/:ver.contact/addcompany'); // 添加公司信息
Route::post('api/:ver/contact/updatecompany','api/:ver.contact/updatecompany'); // 添加公司信息

// OPENID 获取
Route::post('api/getopenid','api/waferauth/getopenid');

// 上传
Route::post('api/upload','api/image/upload');
