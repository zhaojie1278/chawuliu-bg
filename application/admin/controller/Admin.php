<?php
namespace app\admin\controller;

use think\Controller;
use app\common\lib\IAuth;

class Admin extends Controller
{
    /**
     * 添加用户
     * @return mixed
     */
    public function add() {
        if (request()->isPost()) {
            $data = input('post.');
            $validate = validate('Admin');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            if ($data['password2'] !== $data['password']) {
                $this->error('确认密码需与密码一致，请重新填写');
            }

            $data['password'] = IAuth::setPwd($data['password']);
            $data['status'] = config('code.status_normal');

            try {
                $id = model('common/Admin')->add($data);
            }catch(\Exception $e) {
                $this->error($e->getMessage());
            }
            if ($id) {
                $this->success('用户 '.$data['username'].' 增加成功！');
            } else {
                $this->error('系统错误001');
            }
        }
        return $this->fetch();
    }
}
