<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 邀请
 * Class Yaoqing
 * @package app\admin\controller
 */
class Yaoqing extends Base
{
    public function index() {
        $where = [];
        if (request()->isPost()) {
            $condition = input('post.');
            if (!empty($condition['nickname'])) {
                $where['nickname'] = ['LIKE','%'.$condition['nickname'].'%'];
            }
            if (!empty($condition['iv_nickname'])) {
                $where['iv_nickname'] = ['LIKE','%'.$condition['iv_nickname'].'%'];
            }
            if (!empty($condition['starttime'])) {
                $where['create_time'] = ['>=',$condition['starttime']];
            }
            if (!empty($condition['endtime'])) {
                if (!empty($condition['starttime'])) {
                    $where['create_time'] = ['between', [$condition['starttime'], $condition['endtime']]];
                } else {
                    $where['create_time'] = ['<=', $condition['endtime']];
                }
            }
        } else {
            $condition = [];
        }
        $yaoqings = model('common/Yaoqing')->getAll($where);
        return $this->fetch('',['yaoqings'=>$yaoqings,'condition'=>$condition]);
    }

    /**
     * 修改
     * @return mixed|void
     */
    public function edit()
    {
        if(request()->isPost()) {

            $data = input('post.');
            // 数据需要做检验
            $validate = validate('Yaoqing');
            if (!$validate->scene('edit')->check($data)) {
                return $this->error($validate->getError());
            }

            //入库操作
            try {
                if ($data['iv_uid'] === '0') {
                    // 删除关系
                    $rs = model('common/Yaoqing')->del($data);
                } else {
                    $rs = model('common/Yaoqing')->edit($data);
                }
                // 修改联系人表数据
                if ($rs) {
                    $contactData['iv_uid'] = $data['iv_uid'];
                    $contactData['id'] = $data['uid'];

                    $rsContactEdit = model('common/contact')->edit($contactData);
                    if (!$rsContactEdit) {
                        exception('修改失败，联系人数据异常！');
                    }
                } else {
                    exception('修改失败，邀请数据异常！');
                }
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

            if($rs) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }else {
            $contacts = model('common/Contact')->getAllContact();
            if (!input('get.id')) {
                $this->error('打开修改界面失败');
            }
            $ent = model('common/yaoqing')->getById(input('get.id'));
            return $this->fetch('', ['contacts' => $contacts, 'ent'=>$ent]);
        }
    }

    /**
     * 删除
     */
    public function del() {
        if (!request()->isAjax()) {
            $rsdata = [
                'code'=>'1001',
                'msg'=>'错误的请求'
            ];
        } else {
            $data = input('post.');
            $msg = '';
            try {
                $yqData = model('common/Yaoqing')->getById($data['id']);
                $contactData['iv_uid'] = 0;
                $contactData['id'] = $yqData['uid'];
                $rsContactEdit = model('common/contact')->edit($contactData);
                if (!$rsContactEdit) {
                    exception('联系人数据异常！');
                }

                $rs = model('common/Yaoqing')->del($data);
                // 修改联系人表数据
                if (!$rs) {
                    exception('邀请数据异常！');
                }
            }catch (\Exception $e) {
                $msg = $e->getMessage();
                $rs = false;
            }
            if ($rs) {
                $rsdata = [
                    'code'=>'0',
                    'msg'=>'删除成功'
                ];
            } else {
                $rsdata = [
                    'code'=>'1002',
                    'msg'=>$msg ? $msg : '系统异常'
                ];
            }

        }
        echo json_encode($rsdata);
    }
}
