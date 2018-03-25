<?php
namespace app\api\controller;

use think\Controller;

/**
 * 图片上传
 * Class Admin
 * @package app\api\controller
 */
class Image extends Controller
{
    /**
     * 添加用户
     * @return mixed
     */
    public function upload() {
        $file = request()->file('file');
        /* if (empty($file)) {
            return show(config('code.error'), '抱歉，图片不存在', [], 400);
        } */
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif,bmp,jpeg'])->move('uploads/wafer'); // 2M限制
        if ($info) {
            $saveName = $info->getSaveName();
            $saveName = $saveName ? str_replace('\\','/',$saveName) : '';
            $data = [
                'imgurl'=>'/uploads/wafer/'.$saveName,
            ];
            return show(config('code.success'), 'ok', $data, 200);//输出openid            
        } else {
            return show(config('code.error'), '上传失败：'.$file->getError().' 请更换图片或重试', [], 400);
        }
    }
}
