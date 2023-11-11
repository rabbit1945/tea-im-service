<?php
namespace app\api\controller\v1\upload;
use app\api\business\MessageBusiness;
use app\api\business\MessageSendBusiness;
use app\api\business\UploadBusiness;
use app\BaseController;
use app\common\utils\ImJson;
use app\common\utils\Upload as utilsUpload;
use think\App;
use think\facade\Config;
use think\facade\Filesystem;
use think\facade\Request;
use think\Response;

class Upload extends BaseController
{

    private MessageBusiness $business;

    private int|float $size = 0;
    private UploadBusiness $uploadBusiness;

    public function __construct(App $app,MessageBusiness $business,UploadBusiness $uploadBusiness)
    {
        parent::__construct($app);
        $this->business = $business;

        $this->uploadBusiness = $uploadBusiness;

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
//        $getSize = $file->getSize();
//        if ($getSize > $this->size) return ImJson::output('20015','',[],['name'=>3]);
        $dir = 'audio';
        $time = time();
        $name = "audio"."_$user_id"."_$time"."_".".mp3";
        $uploadAudio =$this->uploadBusiness->upload($dir,$file,$name);
        if (!$uploadAudio) {
            if (empty($list)) return ImJson::output('20001');

        }
        return ImJson::output(10000,'成功',['file' => $uploadAudio]);
    }

    /**
     * 上传文件
     * @return Response
     */
    public function uploadFiles(): Response
    {
        $user_id = static::$user_id;
        $files = Request::file('file');
        if (!$files) {
            return ImJson::output(20006);
        }
        $fileName = $files->getOriginalName();
        $getSize = $files->getSize();
        $dir = 'files';
        $time = time();
        $name = "files"."_$user_id"."_$time"."_".$fileName;
        $uploadAudio = $this->uploadBusiness->upload($dir,$files,$name);
        if (!$uploadAudio) {
            if (empty($list))  return ImJson::output('20001');
        }
        return ImJson::output(10000,'成功',['fileName' => $fileName,'fileSize' => $getSize,'file' => $uploadAudio]);
    }


    /**
     * 上传base64
     * @return
     */
    public function uploadBase64()
    {

        $base64 = Request::post('base64');
        if (!$base64) {
            return ImJson::output(20006);
        }
        $user_id =static::$user_id;
        $time = time();
        $fileName = "files"."_$user_id"."_$time";
        $dir = Config::get('filesystem.disks.public.root').'/'.'files/';
        $uploadAudio =$this->uploadBusiness->uploadPic($base64,$fileName,$dir);
        $totalSize = $uploadAudio['fileSize'];
        $getUploadMaxSize = $this->uploadBusiness->getUploadMaxSize();
        if ($totalSize > $getUploadMaxSize) return ImJson::output(20015,'',[],['name'=>300]);
        if (!$uploadAudio['isSuccess']) {
            if (empty($list))  return ImJson::output(20001);
        }

        return ImJson::output(10000,'成功',['fileName' => $fileName,'fileSize' => $totalSize,'file' => "files/".$uploadAudio['fileName']]);
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
        $dir ='files';
        $path =$dir.$newFileName;

        if (!$this->uploadBusiness->uploadFile($files,$path)) return ImJson::outData(20001);

        return ImJson::output(10000,'成功',['newFileName' => $newFileName,'path' => $path]);

    }

    /**
     * 检测文件
     * @return Response
     */
    public function checkChunkExist(): Response
    {
        $user_id =static::$user_id;
        $file = Request::post('File');
        $md5 = Request::post('identifier');  // md5
        $filename = Request::post('filename'); // 文件名称
        $totalChunks = Request::post('totalChunks'); // 分片总数量
        $totalSize = Request::post('totalChunks'); // 分片大小
        if (!$md5 || !$filename || !$totalChunks) return ImJson::output('20003');
        $getSequence = $this->app->make(MessageSendBusiness::class)->getSequence();  // 给文件添加唯一标识
        $getUploadMaxSize = $this->uploadBusiness->getUploadMaxSize();
        if ($totalSize > $getUploadMaxSize) return ImJson::output('20015','',[],['name'=>300]);
        $uploadStatus = $this->app->make(utilsUpload::class);
        // 查询是否有相同的文件
        $find = $this->business->find(
            [
                "md5"  => $md5,
                "upload_status" =>$uploadStatus::SEND_SUCCESS
            ],
            'file_path',"id desc"
        );
        $newFileName = $getSequence.'_'.$user_id.'_'.$filename;
        $dir = "/files/$user_id/";
        $type = 0;
        if ($find) {
            $type = 1;
            $mergeFilePath = $find['file_path'];
        } else {
            // 创建合并文件
            $mergeFilePath ="$dir".$newFileName;
        }

        if(!Filesystem::has($mergeFilePath)) {
            $mergeFilePath ="$dir".$newFileName;
        }


        $data = [
            "mergePath"        => $mergeFilePath,
            "newFileName"      => $newFileName,
            "type"             => $type // 0 普通上传 1 秒传
        ];

        return ImJson::output(10000,'成功',$data);

    }







}
