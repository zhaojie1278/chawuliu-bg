<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 空运、海运
 */
class Foreign extends Base
{
    public function index() {
        $where = [];
        if (request()->isPost()) {
            $condition = input('post.');
            if (!empty($condition['areaname'])) {
                $where['areaname'] = ['LIKE','%'.$condition['areaname'].'%'];
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
        $foreigns = model('common/foreign')->getAll($where);
        return $this->fetch('',['foreigns'=>$foreigns,'condition'=>$condition]);
    }

    /**
     * 添加
     * @return mixed|void
     */
    public function add() {
        if(request()->isPost()) {

            $data = input('post.');
            // 数据需要做检验
            $validate = validate('Foreign');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }

            //入库操作
            try {
                $data['uid'] = $this->userGlobal->id;
                $maxCode = model('common/Foreign')->getMaxCode();
                if (empty($maxCode)) {
                    $maxCode = 10000;
                } else {
                    $maxCode = $maxCode + 1;
                }
                $data['code'] = $maxCode;

                if (!empty($data['firstcode'])) {
                    // 父节点
                    $firstInfo = model('common/Foreign')->getByCode($data['firstcode']);
                    if (empty($firstInfo)) {
                        exception('此上一级已不存在，请刷新界面后重新添加');
                    } else {
                        $data['firstname'] = $firstInfo['areaname'];
                    }
                }

                $id = model('common/Foreign')->add($data);
            }catch (\Exception $e) {
                $this->error('添加失败->'.$e->getMessage());
            }

            if($id) {
                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
        }else {
            $isFirst = true;
            $firsts = model('common/Foreign')->getAllForeign($isFirst);
            return $this->fetch('',['firsts'=>$firsts]);
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
            $validate = validate('Foreign');
            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }
            //入库操作
            try {
                if (!empty($data['firstcode'])) {
                    // 父节点
                    $firstInfo = model('common/Foreign')->getByCode($data['firstcode']);
                    if (empty($firstInfo)) {
                        exception('此上一级已不存在，请刷新界面后重新添加');
                    } else {
                        $data['firstname'] = $firstInfo['areaname'];
                    }
                }
                // 修改联系人信息
                $rs = model('common/Foreign')->edit($data);
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
            $ent = model('common/Foreign')->getById(input('get.id'));
            $isFirst = true;
            $firsts = model('common/Foreign')->getAllForeign($isFirst);
            return $this->fetch('', ['ent'=>$ent,'firsts'=>$firsts]);
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
                $rs = model('common/Foreign')->del($data);
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
                $rs = model('common/Foreign')->edit($data);
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
