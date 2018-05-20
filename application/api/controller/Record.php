<?php
namespace app\api\controller;

use think\Controller;
use AipSpeech160;

/**
 * 录音上传
 * Class Admin
 * @package app\api\controller
 */
class Record extends Controller
{
    /**
     * 录音上传
     * @return mixed
     */
    public function upload() {
        $file = request()->file('file');
        if (empty($file)) {
            return show(config('code.error'), '抱歉，上传文件不存在', [], 400);
        }
        $data = input('post.');
        if (empty($data['from']) || !in_array($data['from'], config('image.record_from'))) {
            return show(config('code.error'), '抱歉，上传参数传递错误', [], 400);
        }
        $info = $file->rule('setFilePathRandTimeStr')->move('uploads/wafer/record/'.$data['from']); // 2M限制
        if ($info) {
            $saveName = $info->getSaveName();
            $filepath = ROOT_PATH.'public'.DS.'uploads'.DS.'wafer'.DS.'record'.DS.$data['from'].DS;
            $filename = $saveName;

            // 调用百度语音识别接口
            // $recogData = $this->recognize($filepath);

            $saveName = $saveName ? str_replace('\\','/',$saveName) : '';
            /*$rsData = [
                'recordurl'=>'/uploads/wafer/record/'.$rsData['from'].'/'.$saveName,
                // 'filepath' => $filepath,
                // 'recogData' => $recogData
            ];*/

            // 转码
            $rsZhuanma = $this->zhuanma($filepath, $filename);
            // $rsData['zm'] = $rsZhuanma;
            if ($rsZhuanma) {
                // 识别
                $rsRecog = $this->recognize($rsZhuanma);
                if ($rsRecog['err_no'] != 0) {
                    $rsRecogArr = 0;
                } else {
                    $rsRecogStr = $rsRecog['result'][0];
                    // if (stripos($rsRecog, '到'))
                    $strArr = explode('到', $rsRecogStr);
                    if (count($strArr) > 1) {
                        $rsRecogArr['start'] = str_ireplace('从','',$strArr[0]);
                        $rsRecogArr['end'] = $strArr[1];
                    } else {
                        $rsRecogArr = 0;
                    }
                }
            } else {
                $rsRecogArr = 0;
            }
            // dump($rsRecog);
            $rsData['recog'] = $rsRecogArr;
            return show(config('code.success'), 'ok', $rsData, 200);//输出openid            
        } else {
            // return show(config('code.error'), '识别失败：'.$file->getError().' 请重试', [], 400);
            return show(config('code.error'), 'fail, please try again later.', [], 400);
        }
    }

    /**
     * 转码 mp3 -- pcm
     * @param  string $filepath2name
     * @return 转码后的文件路径
     */
    public function zhuanma($path, $filename) {
        try {
            // ./ffmpeg -y -i ../silk-v3-decoder-master/test/af04fac658b32afda68037cd1b4a782e.mp3  -acodec pcm_s16le -f s16le -ac 1 -ar 16000 testmp3/silk.pcm
            $zmName = explode(".", $filename)[0] . ".pcm";  
            // dump($zmName);
            $ffmpegPath = "/www/web/hycwl_cn/public_html/record/ffmpeg-3.4.2/";  
            $cmd = $ffmpegPath."ffmpeg -y -i ".$path.$filename." -acodec pcm_s16le -f s16le -ac 1 -ar 16000 ".$path.$zmName;
            // dump($cmd);
            $rs = shell_exec($cmd);
            /*if (!$rs) {
                return '11';
            }*/
            // dump($rs);
        } catch (\Exception $e) {
            // dump($e->getMessage());
            return '';
        }
        return $path.$zmName;
    }

    /**
     * 识别
     * @return [type] [description]
     */
    public function recognize($filePath = '') {
        // $filePath = dirname(__FILE__)."/16k.pcm";
        // dump($filePath);
        // dump($filePath);
        try {
            // 识别本地文件
            $client = new \AipSpeech160\AipSpeech (config('baiduapi.appId'), config('baiduapi.apiKey'), config('baiduapi.secretKey'));
            $rs = $client->asr(file_get_contents($filePath), 'pcm', 16000, array(
                'dev_pid' => '1536',
            ));
        } catch(\Exception $e) {
            // dump($e->getMessage());
            return 0;
        }
        return $rs;
    }
}
