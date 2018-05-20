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
Route::post('api/:ver/zhuanxian/tui','api/:ver.zhuanxian/indextui'); // 首页推荐专线
Route::post('api/:ver/zhuanxian/search','api/:ver.zhuanxian/search');
Route::post('api/:ver/zhuanxian/add','api/:ver.zhuanxian/add');
Route::post('api/:ver/zhuanxian/toupdate','api/:ver.zhuanxian/detail');

// 超级买卖：车辆买卖
Route::post('api/:ver/sellmsg/add','api/:ver.sellmsg/add');
Route::post('api/:ver/sellmsg/toupdate','api/:ver.sellmsg/detail');
Route::post('api/:ver/sellmsg/search','api/:ver.sellmsg/search');
Route::post('api/:ver/sellmsg/detail','api/:ver.sellmsg/detailById');

// 司机招聘
Route::post('api/:ver/zhaopin/add','api/:ver.zhaopin/add');
Route::post('api/:ver/zhaopin/toupdate','api/:ver.zhaopin/detail');
Route::post('api/:ver/zhaopin/search','api/:ver.zhaopin/search');
Route::post('api/:ver/zhaopin/detail','api/:ver.zhaopin/detailById');

// 用户
// Route::get('api/:ver/contact','api/:ver.contact/detail'); // 详情
Route::post('api/:ver/contact/favcompany','api/:ver.contact/favcompany'); // 收藏公司信息
Route::post('api/:ver/contact/isregcompany','api/:ver.contact/detailbyopenid'); // 详情
Route::post('api/:ver/contact/companyinfo','api/:ver.contact/detailbyopenid'); // 详情
Route::post('api/:ver/contact/contactinfo','api/:ver.contact/detailByOpenid2'); // 详情，带有专线
Route::post('api/:ver/contact/addcompany','api/:ver.contact/addcompany'); // 添加公司信息
Route::post('api/:ver/contact/updatecompany','api/:ver.contact/updatecompany'); // 修改公司信息
Route::post('api/:ver/contact/getallzx','api/:ver.zhuanxian/getallbyopenid'); // 根据openid获取用户所有专线
Route::post('api/:ver/contact/getallsellmsg','api/:ver.sellmsg/getallbyopenid'); // 根据openid获取用户所有超级买卖信息
Route::post('api/:ver/contact/getallzhaopin','api/:ver.zhaopin/getallbyopenid'); // 根据openid获取用户所有招聘信息
Route::post('api/:ver/contact/delzx','api/:ver.zhuanxian/delByIdCid'); // 根据用户id和专线id删除专线
Route::post('api/:ver/contact/delsellmsg','api/:ver.sellmsg/delByIdCid'); // 根据用户id和专线id删除超级买卖信息
Route::post('api/:ver/contact/delzhaopin','api/:ver.zhaopin/delByIdCid'); // 根据用户id和专线id删除招聘信息
Route::post('api/:ver/contact','api/:ver.contact/detail'); // 详情

// 收藏
Route::post('api/:ver/fav/getfavors','api/:ver.fav/getFavorsByCid'); // 获取收藏

// OPENID 获取
Route::post('api/getopenid','api/waferauth/getopenid');

// 上传
Route::post('api/upload/image','api/image/upload');
Route::post('api/upload/record','api/record/upload');
// Route::get('api/upload/recognize','api/record/recognize');

// 地区
Route::get('api/area','api/area/getarea');
Route::get('api/area/getprovin','api/area/getProvins');
Route::get('api/area/getcity','api/area/getCitys');
Route::get('api/area/getarea','api/area/getAreas');
Route::get('api/area/getcode','api/area/getCode');
Route::get('api/area/getforeignfirst','api/foreign/getFirst');
Route::get('api/area/getforeignsecond','api/foreign/getSecond');

// 新闻
Route::post('api/:ver/news/tui','api/:ver.news/indextui'); // 首页推荐专线
Route::post('api/:ver/news','api/:ver.news/detail');
