<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 图片上传
 * Class Admin
 * @package app\admin\controller
 */
class Image extends Controller
{
    /**
     * 添加用户
     * @return mixed
     */
    public function upload() {
        $file = request()->file('file');
        $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif,bmp,jpeg'])->move('uploads');
        if ($info) {
//            halt($info);
            $saveName = $info->getSaveName();
            $saveName = $saveName ? str_replace('\\','/',$saveName) : '';
            $data = [
                'status'=>1,
                'message'=>'ok',
                'data'=>'/uploads/'.$saveName,
            ];
        } else {
            $data = [
                'status'=>0,
                'message'=>'上传失败：'.$file->getError().' 请更换图片或重试',
                'data'=>'',
            ];
        }
        echo json_encode($data);
    }
}
