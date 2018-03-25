<?php
namespace app\admin\controller;

use think\Controller;

class Contact extends Base
{
    public function index() {
        $where = [];
        if (request()->isPost()) {
            $condition = input('post.');
            if (!empty($condition['nickname'])) {
                $where['nickname'] = ['LIKE','%'.$condition['nickname'].'%'];
            }
            if (!empty($condition['company'])) {
                $where['company'] = ['LIKE','%'.$condition['company'].'%'];
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
        $contacts = model('common/contact')->getAll($where);
        return $this->fetch('',['contacts'=>$contacts,'condition'=>$condition]);
    }

    /**
     * 添加
     * @return mixed|void
     */
    public function add() {

        if(request()->isPost()) {

            $data = input('post.');
            // 数据需要做检验 validate机制小伙伴自行完成
            $validate = validate('Contact');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }

            // 邀请人处理
            if (!empty($data['iv_uid'])) {
                $ivContact = model('contact')->getById($data['iv_uid']);
                if (!$ivContact) {
                    return $this->error('邀请人不存在或已删除，请核实后添加');
                }
            }

            //入库操作
            try {
                $id = model('common/Contact')->add($data);
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

            if($id) {
                // 邀请人处理
                if (!empty($data['iv_uid'])) {
                    $ivData['uid'] = $id;
                    $ivData['nickname'] = $data['nickname'];
                    $ivData['iv_uid'] = $data['iv_uid'];
                    $ivData['iv_nickname'] = $ivContact->nickname;
                    try {
                        $idYq = model('common/Yaoqing')->add($ivData);
                    }catch (\Exception $e) {
                        return $this->error($e->getMessage());
                    }
                }

                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
        }else {
            $contacts = model('common/Contact')->getAllContact();
            return $this->fetch('',['contacts'=>$contacts]);
        }
    }

    /**
     * 修改
     * @return mixed|void
     */
    public function edit()
    {
        if(request()->isPost()) {

            $data = input('post.');
            // 数据需要做检验 validate机制小伙伴自行完成
            $validate = validate('Contact');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            //入库操作
            try {
                // 邀请人处理
                // 获取旧的邀请人
                $contactIvUid = model('common/contact')->getIvUid($data['id']);
                // 新的与旧的不同
                if ($contactIvUid!=$data['iv_uid']) {
                    $yaoqing = model('common/yaoqing')->getByUidIvUid($data['id'], $contactIvUid);
                    if (!$data['iv_uid']) {
                        if ($yaoqing) {
                            try {
                                $yaoqing->delete();
                            }catch (\Exception $e) {
                                return $this->error($e->getMessage());
                            }
                        }
                    } else {
                        // 有则更改
                        $ivContact = model('common/contact')->getById($data['iv_uid']);
                        if (!$ivContact) {
                            return $this->error('邀请人不存在或已删除，请核实后添加');
                        }
                        if ($yaoqing) {
                            $yqEditData['iv_uid'] =  $data['iv_uid'];
                            $yqEditData['iv_nickname'] = $ivContact->nickname;
                            try {
                                $yaoqing->edit($yqEditData);
                            }catch (\Exception $e) {
                                return $this->error($e->getMessage());
                            }
                        } else {
                            // 无则添加
                            $ivData['uid'] = $data['id'];
                            $ivData['nickname'] = $data['nickname'];
                            $ivData['iv_uid'] = $data['iv_uid'];
                            $ivData['iv_nickname'] = $ivContact->nickname;
                            try {
                                $idYq = model('common/Yaoqing')->add($ivData);
                            }catch (\Exception $e) {
                                return $this->error($e->getMessage());
                            }
                        }
                    }
                }

                // 修改联系人信息
                $rs = model('common/contact')->edit($data);
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

            if($rs) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }else {
            if (!input('get.id')) {
                $this->error('打开修改界面失败');
            }
            $contacts = model('common/Contact')->getAllContact();
            $ent = model('common/contact')->getById(input('get.id'));
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
                $rs = model('common/contact')->del($data);
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
