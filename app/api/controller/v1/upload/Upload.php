<?php
namespace app\api\controller\v1\upload;
use app\api\business\MessageReceiveBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use think\App;
use think\facade\Config;
use think\facade\Request;

class Upload extends BaseController
{

    private  $business;

    private $size = 3*1024*1024;

    public function __construct(App $app,MessageReceiveBusiness $business)
    {
        parent::__construct($app);
        $this->business = $business;

    }


    /**
     * 上传音频
     */
    public function uploadAudio()
    {
        $user_id =static::$user_id;
        $file = Request::file('file');
        if (!$file) {
            return ImJson::output(20006);
        }
        $getSize = $file->getSize();
        if ($getSize > $this->size) return ImJson::output('20015','',[],['name'=>3]);
        $dir = 'audio';
        $time = time();
        $name = "audio"."_$user_id"."_$time"."_".".mp3";
        $uploadAudio = $this->business->upload($dir,$file,$name);
        if (!$uploadAudio) {
            if (empty($list)) return ImJson::output('20001');

        }
        return ImJson::output(10000,'成功',['file' => $uploadAudio]);
    }

    /**
     * 上传文件
     * @return \think\Response
     */
    public function uploadFiles(): \think\Response
    {
        $user_id =static::$user_id;
        $files = Request::file('file');
        if (!$files) {
            return ImJson::output(20006);
        }
        $fileName = $files->getOriginalName();
        $getSize = $files->getSize();
        if ($getSize > $this->size) return ImJson::output('20015','',[],['name'=>3]);
        $dir = 'files';
        $time = time();
        $name = "files"."_$user_id"."_$time"."_".$fileName;
        $uploadAudio = $this->business->upload($dir,$files,$name);
        if (!$uploadAudio) {
            if (empty($list))  return ImJson::output('20001');
        }
        return ImJson::output(10000,'成功',['fileName' => $fileName,'fileSize' => $getSize,'file' => $uploadAudio]);
    }


    /**
     * 上传base64
     * @return \think\Response
     */
    public function uploadBase64(): \think\Response
    {

        $base64 = Request::post('base64');
        if (!$base64) {
            return ImJson::output(20006);
        }
        $user_id =static::$user_id;
        $time = time();
        $fileName = "files"."_$user_id"."_$time";
        $dir = Config::get('filesystem.disks.public.root').'/'.'files/';
        $uploadAudio = $this->business->uploadBase64($base64,$fileName,$dir);
        $getSize = $uploadAudio['fileSize'];
        if ($getSize > $this->size) return ImJson::output('20015','',[],['name'=>3]);
        if (!$uploadAudio['isSuccess']) {
            if (empty($list))  return ImJson::output('20001');
        }

        return ImJson::output(10000,'成功',['fileName' => $fileName,'fileSize' => $getSize,'file' => "files/".$uploadAudio['fileName']]);
    }




}
