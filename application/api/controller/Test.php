<?php
namespace app\api\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\Aes;

class Test extends Common {

    /**
     * 测试路由
     */
    public function index() {
        dump(__FILE__);
        dump(__DIR__);
        dump(getenv('HTTP_CLIENT_IP'));
        dump(getenv('HTTP_X_FORWARDED_FOR'));
        dump(getenv('REMOTE_ADDR'));
        $arr = array("blue","red","green","yellow","red");
print_r(str_replace("red","pink",$arr,$i));
echo "替换数：$i";
        $a = array("Hello world!, world, world");
        print_r(str_replace("world","Shanghai",$a,$i));
        echo '$i::'.$i;

        $a[] = null;
        dump($a == null);
        dump(is_null($a));
        dump(max(1,2,3,222));
        dump(min(1,20.1,0.1,0,-1,3,-222));
        dump(max(array('b'=>1,2,'a'=>-2,20.1,0.1,0,-1,3,222)));
        dump(min(array('b'=>1,2,'a'=>-2,20.1,0.1,0,-1,3,222)));
        // echo phpinfo();

        $n = NULL;
        $s = 'a\" is b null'."aa $n";
        dump(addslashes($s));
        dump(stripslashes($s));
        dump(get_magic_quotes_gpc());
        $x=87;
        $y=($x%7)*16;
        $z=$x>$y?1:0;
        echo $z;
        // dump($_SERVER);
        dump(number_format(1234567,2,'-','.'));
        dump(str_pad('1235',5,'X.2',STR_PAD_LEFT));
        exit;
        halt($_SERVER);
        $a = 1234567890;
        dump(number_format($a));
        dump(number_format($a,3));
        dump(number_format($a,2,'"',''));




        halt(0);

        return [
            'sgsg',
            'abcd'
        ];
    }

    /**
     * put update
     */
    public function update($id = 0) {
        $ip = input('put.name');
        dump(input('name'));
        halt($ip);
        return $id;
    }

    public function delete($id=0) {
        dump('deleteid:'.$id);
        return 'del funciton';
    }

    /**
     * 测试资源路由
     */
    public function save($u=0) {

        $pdata = input('post.');
        // halt($pdata);

        if ($pdata['name']) {
            //throw new ApiException('您的数据不合法1', 401);
            // exception('您的数据不合法');
        }
        // try {
        //     model('a');
        // } catch (\Exception $e) {
        //     return show(0, $e->getMessage(), 400);
        // }
        return show(1, 'ok', (new Aes())->encrypt(json_encode(input('post.'))), 200);
    }

    /**
     * tp5资源路由读取数据默认方法
     */
    public function read() {
        dump(input());
        return '123';
    }
}