<?php
namespace app\admin\controller;

use think\Controller;

class News extends Base
{
    public function index() {
        $where = [];
        if (request()->isPost()) {
            $condition = input('post.');
            if (!empty($condition['title'])) {
                $where['title'] = ['LIKE','%'.$condition['title'].'%'];
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
        $news = model('common/news')->getAll($where);
        return $this->fetch('',['news'=>$news,'condition'=>$condition]);
    }

    /**
     * 添加
     * @return mixed|void
     */
    public function add() {
        if(request()->isPost()) {

            $data = input('post.');
            // 数据需要做检验
            $validate = validate('News');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }

            //入库操作
            try {
                $data['uid'] = $this->userGlobal->id;
                $id = model('common/News')->add($data);
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

            if($id) {
                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
        }else {
            // $contacts = model('common/Newscat')->getAllContact();
            // return $this->fetch('',['contacts'=>$contacts]);
            return $this->fetch('');
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
            // 数据需要做检验
            $validate = validate('News');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            //入库操作
            try {
                // 修改联系人信息
                $rs = model('common/News')->edit($data);
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
            $ent = model('common/News')->getById(input('get.id'));
            return $this->fetch('', ['ent'=>$ent]);
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
                $rs = model('common/news')->del($data);
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

    /**
     * 发布
     */
    public function topHandle() {
        if (!request()->isAjax()) {
            $rsdata = [
                'code'=>'1001',
                'msg'=>'错误的请求'
            ];
        } else {
            $data = input('post.');
            //入库操作
            try {
                $rs = model('common/News')->edit($data);
            }catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

            if($rs) {
                $rsdata = [
                    'code'=>'0',
                    'msg'=>'修改成功'
                ];
            } else {
                $rsdata = [
                    'code'=>'0',
                    'msg'=>'修改失败'
                ];
            }

        }
        echo json_encode($rsdata);
    }
}
