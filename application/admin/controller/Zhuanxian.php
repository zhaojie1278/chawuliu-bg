<?php
namespace app\admin\controller;

use think\Controller;

class Zhuanxian extends Base
{
    public function index() {
        $where = [];
        if (request()->isPost()) {
            $condition = input('post.');
            if (!empty($condition['start_prov'])) {
                $where['start_prov'] = ['EQ',$condition['start_prov']];
            }
            if (!empty($condition['start'])) {
                $where['start'] = ['EQ',$condition['start']];
            }
            if (!empty($condition['point_prov'])) {
                $where['point_prov'] = ['EQ',$condition['point_prov']];
            }
            if (!empty($condition['point'])) {
                $where['point'] = ['EQ',$condition['point']];
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
        $zhuanxians = model('common/zhuanxian')->getAll($where);
        if ($zhuanxians) {
            foreach ($zhuanxians as $zx) {
                $cids[] = $zx['cid'];
            }
            $contacts = model('common/Contact')->getAllContactInIds($cids);
        } else {
            $contacts = array();
        }
//        dump( model('common/Contact')->getLastSql());
        $contactsArr = array();
        if ($contacts) {
            foreach ($contacts as $c) {
                $contactsArr[$c->id] = $c->toArray();
            }
        }
        return $this->fetch('',['zhuanxians'=>$zhuanxians,'condition'=>$condition,'contacts'=>$contactsArr]);
    }

    /**
     * 添加
     * @return mixed|void
     */
    public function add()
    {
        if(request()->isPost()) {

            $data = input('post.');
            // 数据需要做检验 validate机制小伙伴自行完成
            $validate = validate('Zhuanxian');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            //入库操作
            try {
                $id = model('common/Zhuanxian')->add($data);
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

            if($id) {
                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
        }else {
            $contacts = model('common/Contact')->getAllContact();
            return $this->fetch('', ['contacts' => $contacts]);
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
            $validate = validate('Zhuanxian');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            //入库操作
            try {
                $rs = model('common/Zhuanxian')->edit($data);
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
            $ent = model('common/zhuanxian')->getById(input('get.id'));
//            halt(model('common/zhuanxian')->getLastSql());
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
                $rs = model('common/Zhuanxian')->del($data);
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
