<?php
namespace app\admin\controller;

use think\Controller;

class Appinfo extends Base
{
    public function index() {
        if (request()->isPost()) {
            $data = input('post.');
            try {
                if (empty($data['id'])) {
                    $rs = model('common/appinfo')->save($data);
                } else {
                    $appinfo = model('common/appinfo')->get($data['id']);
                    $appinfo->name = $data['name'];
                    $appinfo->officialphone = $data['officialphone'];
                    $rs = $appinfo->save();
                }
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
            if ($rs) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败，未更新字段信息或系统异常');
            }
        } else {
            $appinfo = model('common/appinfo')->all();
            if ($appinfo) {
                $appinfo = $appinfo[0];
            } else {
                $appinfo = ['id'=>'','name'=>'','officialphone'=>''];
            }
            return $this->fetch('', ['appinfo' => $appinfo]);
        }
    }
}
