<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Base
{
    public function index()
    {
//        halt(session(config('admin.session_user'),'',config('admin.session_user_scope')));
        return $this->fetch('index');
    }

    /**
     * 初始进入界面
     * @return mixed
     */
    public function welcome() {
        $zhuanxianCount = model('common/zhuanxian')->getCount();
        $contactCount = model('common/contact')->getCount();
        $now = date('Y-m-d H:i:s');
        return $this->fetch('',['zhuanxianCount'=>$zhuanxianCount,'contactCount'=>$contactCount,'time'=>$now]);
    }
}
