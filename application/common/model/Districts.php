<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Districts extends Base {

    /**
     * 清空旧数据
     * @return void
     */
    public function cleanData() {
        $cleanPro = "TRUNCATE table c_province";
        $cleanPro = "DELETE FROM c_province WHERE id<5";
        $cleanCity = "TRUNCATE TABLE c_city";
        $cleanArea = "TRUNCATE TABLE c_area";
        
        try {
            $rs = Db::table('c_province')->where('id',1)->find();
        } catch(\Exception $e) {
            halt($e->getMessage());
        }
        halt($rs);
    }
}