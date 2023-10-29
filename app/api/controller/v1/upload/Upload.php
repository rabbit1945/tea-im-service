<?php
namespace app\api\controller\v1\upload;
use app\api\business\MessageBusiness;
use app\api\business\MessageSendBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use think\App;
use think\facade\Config;
use think\facade\Filesystem;
use think\facade\Request;
use think\Response;

class Upload extends BaseController
{

    private MessageBusiness $business;

    private int|float $size = 5*1024*1024;

    public function __construct(App $app,MessageBusiness $business)
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
        $dir = Config::get('filesystem.disks.public.root').'/'.'files/'.$user_id.'/';
        $uploadAudio = $this->business->uploadPic($base64,$fileName,$dir);
        $getSize = $uploadAudio['fileSize'];
        if ($getSize > $this->size) return ImJson::output('20015','',[],['name'=>3]);
        if (!$uploadAudio['isSuccess']) {
            if (empty($list))  return ImJson::output('20001');
        }

        return ImJson::output(10000,'成功',['fileName' => $fileName,'fileSize' => $getSize,'file' => "files/$user_id/".$uploadAudio['fileName']]);
    }

    /**
     * 上传大文件
     */
    public function uploadPut()
    {
        $user_id =static::$user_id;
        $files = Request::post('file');
        if (!$files) {
            return ImJson::output(20006);
        }
        $fileName    = Request::post('fileName');
        $newFileName = $user_id."_".$fileName;
        $dir ='files/'.$user_id."/";
        $path =$dir.$newFileName;

        if (!$this->business->uploadFile($files,$path)) return ImJson::outData(20001);

        return ImJson::output(10000,'成功',['newFileName' => $newFileName,'path' => $path]);

    }

    /**
     * 检测文件
     * @return Response
     */
    public function checkChunkExist(): Response
    {
        $user_id =static::$user_id;
        $md5 = Request::post('identifier');  // md5
        $filename = Request::post('filename'); // 文件名称
        $totalChunks = Request::post('totalChunks'); // 分片总数量
        if (!$md5 || !$filename || !$totalChunks) return ImJson::output('20003');
        $location = $this->app->make(MessageSendBusiness::class)->getSequence();
        // 查询是否有相同的文件
        $find = $this->business->find(
            [
                "md5"  => $md5,
                "upload_status" => 3
            ]
        );
        $newFileName = $location.'_'.$user_id.'_'.$filename;
        $dir = "files/$user_id/";
        $type = 0;
        if ($find) {
            $type = 1;
            $mergeFilePath = $find['file_path'];
        } else {

            // 创建合并文件
            $mergeFilePath ="$dir".$newFileName;

        }

        if(!Filesystem::has( $mergeFilePath)) {
            $type = 0;
            Filesystem::createDir($dir."$md5");
        }
        $data = [
            "mergePath"        => $mergeFilePath,
            "newFileName"      => $newFileName,
            "type"             => $type // 秒传
        ];

        return ImJson::output(10000,'成功',$data);

    }







}
