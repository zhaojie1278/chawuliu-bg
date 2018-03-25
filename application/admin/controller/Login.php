<?php
namespace app\admin\controller;

use app\common\lib\IAuth;
use think\Controller;

class Login extends Base
{
    public function _initialize()
    {
        //return parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function index() {
        if ($this->isLogin()) {
            return $this->redirect('index/index');
        } else {
            return $this->fetch();
        }
    }

    /**
     * 登录
     */
    public function check() {
        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate('Login');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            try {
                $user = model('Admin')->get(['username' => $data['username']]);
            } catch(\Exception $e) {
                $this->error($e->getMessage());
            }
            // 用户验证
            if (!$user || $user->status != config('code.status_normal')) {
                $this->error('用户不存在');
            }

            // 密码验证
            if (IAuth::setPwd($data['password']) != $user['password']) {
                $this->error('密码不正确');
            }

            // 更新登录时间
            $udata = [
                'last_login_time' => date('Y-m-d H:i:s'),
                'ip' => request()->ip(),
            ];
            try {
                model('Admin')->save($udata, ['id' => $user->id]);
            } catch(\Exception $e) {
                $this->error($e->getMessage());
            }

            // session
            session(config('admin.session_user'),$user,config('admin.session_user_scope'));

            // 跳转
            $this->success('登录成功','index/index');


        } else {
            $this->error('请求不合法002');
        }
    }

    /**
     * 退出
     */
    public function logout() {
        // 清空session
        session(null,config('admin.session_user_scope'));

        // 跳至登录界面
        $this->redirect('login/index');
    }
}
