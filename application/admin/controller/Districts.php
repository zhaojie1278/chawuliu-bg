<?php
namespace app\admin\controller;

use think\Controller;
use think\File;
use think\Db;
/**
 * 区域数据矫正
 */
class Districts extends Base
{
    public function index() {
        halt('a');
    }

    /**
     * 清空旧数据
     * @return void
     */
    public function cleanData() {
        $cleanPro = "TRUNCATE TABLE c_province";
        // $cleanPro = "DELETE FROM c_city WHERE id<5";
        $cleanCity = "TRUNCATE TABLE c_city";
        $cleanArea = "TRUNCATE TABLE c_area";
        
        try {
            $rs = Db::execute($cleanPro);
            $rs = Db::execute($cleanCity);
            $rs = Db::execute($cleanArea);
        } catch(\Exception $e) {
            halt('error:'.$e->getMessage());
        }
        // halt($rs);
    }

    /**
     * 数据填充
     * @return [type] [description]
     */
    public function parseData() {
        // model('Districts')->cleanData();
        
        $this->cleanData(); // 清空数据

        echo 'begin fulldata -- openfile'."<br/>";
        $file = new File(ROOT_PATH . 'public' . DS .'districts.json');
        $cntStr = '';
        while (!$file->eof()) {
          $cntStr .= $file->current();
          $file->next();
        }
        // $cnt = $file->getPathname();
        // $filename = $file->getFilename();
        // dump($filename);
        // dump($file->fgets());
        // dump($file->sha1());
        // dump($file->isReadable());
        $cntJson = json_decode($cntStr, true);
        $arrayProvince = array();
        $arrayCity = array();
        $arrayArea = array();
        foreach($cntJson as $pk=>$pv) {
            $arrayProvince[$pk]['code'] = $pk; // 省份 code areaname
            $arrayProvince[$pk]['areaname'] = $pv['name']; // 省份 code areaname
            // 市区处理  code areaname provincecode
            if (!empty($pv['child']) && is_array($pv['child']) && count($pv['child'])>0) {
                $citys = $pv['child'];
                foreach($citys as $ck=>$cv) {
                    $arrayCity[$ck]['code'] = $ck;
                    $arrayCity[$ck]['areaname'] = is_array($cv) ? $cv['name'] : $cv;
                    $arrayCity[$ck]['provincecode'] = $pk;

                    // 县处理 code areaname citycode
                    if (!empty($cv['child']) && is_array($cv['child']) && count($cv['child'])>0) {
                        foreach($cv['child'] as $ak=>$av) {
                            $arrayArea[$ak]['code'] = $ak;
                            $arrayArea[$ak]['areaname'] = $av;
                            $arrayArea[$ak]['citycode'] = $ck;
                        }
                    }
                }
            }
            
        }

        // dump($arrayProvince);
        // dump($arrayCity);
        // dump($arrayArea);

        $rs = $this->paddingProvData($arrayProvince);
        $rs = $this->paddingCityData($arrayCity);
        $rs = $this->paddingAreaData($arrayArea);
        halt($rs);
        exit;
    }

    public function paddingProvData($data) {
        $sql = 'INSERT INTO c_province(code, areaname) VALUES';
        $sqlData = '';
        /*array_map(function($v) use (&$sqlData) {
            // dump($v);
            $sqlData .= '('.implode(',', $v).')';
        }, array_slice($data, 0, 5, true));*/
        
        // $data = array_slice($data, 0, 5);
        // dump($data);

        array_walk_recursive($data, function(&$v, $k){
            $v = "'$v'";
        });

        // dump($data);
        // halt('0000');

        array_walk($data, function(&$v,$k) {
            $v = "(".implode(',', $v).")";
        });

        $sqlData = implode(',', $data);

        $sql = $sql . $sqlData;

        /*array_reduce(array_keys(array_slice($data,0,5)), function($v1, $v2){
            // dump(implode(',',$v1));
            // dump($v1);
            // echo $v1.'==========='.$v2;
            // dump(implode(',',$v2));
            // dump($v2);
        });*/
        // dump(implode(',', $data));
        // halt($sqlData);
        
        $rs = Db::execute($sql);
        return $rs;
    }

    public function paddingCityData($data) {
        $sql = 'INSERT INTO c_city(code, areaname, provincecode) VALUES';
        $sqlData = '';
          array_walk_recursive($data, function(&$v, $k){
            $v = "'$v'";
        });
        array_walk($data, function(&$v,$k) {
            $v = "(".implode(',', $v).")";
        });

        $sqlData = implode(',', $data);

        $sql = $sql . $sqlData;
        $rs = Db::execute($sql);
        return $rs;
    }

    public function paddingAreaData($data) {
        $sql = 'INSERT INTO c_area(code, areaname, citycode) VALUES';
        $sqlData = '';
          array_walk_recursive($data, function(&$v, $k){
            $v = "'$v'";
        });
        array_walk($data, function(&$v,$k) {
            $v = "(".implode(',', $v).")";
        });

        $sqlData = implode(',', $data);

        $sql = $sql . $sqlData;
        $rs = Db::execute($sql);
        return $rs;
    }

}
