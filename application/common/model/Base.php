<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3
 * Time: 2:27
 */

namespace app\common\model;

use think\Model;

class Base extends Model {

    protected $autoWriteTimestamp = 'datetime';

    /**
     * 保存多条数据
     * @param array $list
     * @return 
     */
    public function addAll($list) {
        return $this->allowField(true)->saveAll($list);
    }
    /**
     * 新增
     * @param $data
     * @param null $dupfield
     * @param null $dupmsg
     * @return mixed
     */
    public function add($data, $dupfield = null, $dupmsg = null) {
        if (!$data) {
            exception('传输数据不合法');
        }
        // 重名判断
        if ($dupfield) {
            $getRs = $this->get([$dupfield => $data[$dupfield], 'status' => config('code.status_normal')]);
            if ($getRs) {
                exception($dupmsg.'，添加失败！');
            }
        }
        $this->allowField(true)->save($data);
        return $this->id;
    }
    /**
     * 修改
     * @param $data
     * @return mixed
     */
    public function edit($data, $dupfield = null, $dupmsg = null) {
        if (!$data || empty($data['id'])) {
            exception('传输数据不合法');
        }
        // 重名判断
        if ($dupfield) {
            $getRs = $this->get([$dupfield => $data[$dupfield], 'status' => config('code.status_normal'),'id'=>['NEQ',$data['id']]]);
            if ($getRs) {
                exception($dupmsg.'，修改失败！');
            }
        }
        $rs = $this->allowField(true)->save($data,['id'=>$data['id']]);
        return $rs;
    }

    /**
     * 删除
     * @param $data
     * @return false|int
     */
    public function del($data, $isPhysical = false) {
        if (!$data || (empty($data['id']) && empty($data['ids']))) {
            exception('传输数据不合法');
        }
        $rs = false;
        if ($isPhysical) {
            if (!empty($data['id'])) {
                $rs = $this->destroy($data['id']);
            }
        } else {
            if (!empty($data['id'])) {
                $updata = ['status' => config('code.status_delete')];
                $rs = $this->allowField(true)->save($updata, ['id' => $data['id']]);
            } else if (!empty($data['ids'])) {
                $updata['status'] = config('code.status_delete');
                $updata['update_time'] = date('Y-m-d H:i:s');
                $rs = $this->allowField(true)->where('id', 'IN', $data['ids'])->update($updata);
            }
        }
        return $rs;
    }

    /**
     * 获取总数
     */
    public function getCount($statusField = true) {
        if ($statusField) {
            $where = ['status'=>config('code.status_normal')];
        } else {
            $where = [];
        }
        $count = 0;
        if ($where) {
            $count = $this->where($where)->count('id');
        } else {
            $count = $this->where()->count('id');
        }
        return $count;
    }
}