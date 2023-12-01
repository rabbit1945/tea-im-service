<?php


namespace app\api\business;
use app\api\dao\message\MessageDao;
use app\common\utils\Upload;
use League\Flysystem\FileNotFoundException;
use think\App;
use think\facade\Filesystem;
use app\common\utils\upload\src\cos\Upload as cosUpload;

class UploadBusiness
{
    /**
     * @var App
     */
    protected App $app;

    /**
     * @var MessageDao
     */
    protected MessageDao $dao;
    /**
     * @var Upload
     */
    private Upload $upload;


    public function __construct(App $app,MessageDao $dao, Upload $upload ) {
        $this->dao = $dao;
        $this->app = $app;
        $this->upload = $upload;

    }


    /**
     * 上传
     */
    public function upload($dir,$file,$name): bool|string
    {
        $path = $this->upload->fileUpload($dir,$file,$name);
        if (!$path) return false;
        return $path;

    }

    /**
     * 上传base64
     * @param $file
     * @param $path
     * @return bool
     */
    public function uploadFile($file,$path): bool
    {
        $match_result =  explode(',', $file);
        if (!Filesystem::has( $path)) {
            return Filesystem::put($path,base64_decode($match_result[1]));
        }
        return true;
    }


    /**
     * 上传base64
     * @param $base64
     * @param $fileName
     * @param $dir
     * @return array|false
     */
    public function uploadPic($base64,$fileName,$dir): bool|array
    {
        $reg = '/data:image\/(\w+?);base64,(.+)$/si';
        preg_match($reg,$base64,$match_result);
        if (!$match_result)  return false;
        $baseImg=str_replace($match_result[1], '', $base64);
        $baseImg=str_replace('=','',$baseImg);
        $imgLen = strlen($baseImg);
        $fileSize = intval($imgLen - ($imgLen/8)*2);

        $fileName = $fileName.'.'.$match_result[1];
        $path = $dir.$fileName;
        $upload = file_put_contents($path,base64_decode($match_result[2]));

        return [
            "isSuccess" => $upload,
            "fileSize" => $fileSize,
            "fileName"  => $fileName,
        ];
    }



    /**
     *
     * @return int|float
     *
     */
    public function getUploadMaxSize(): int|float
    {

        return $this->upload->getMaxSize();

    }

    /**
     * @param $dirPath
     * @param $newFileName
     * @param $user_id
     * @param bool $is_file
     * @return array|bool
     */
    public function cosPutUpload($dirPath, $newFileName, $user_id, bool $is_file = true): bool|array
    {
        if ($is_file) {
            $dirPath = $this->app->getRootPath() . 'public/storage/' . $dirPath;
        }
        $cosUpload = $this->app->make(Upload::class);

        $cosUpload->setModel('app\common\utils\upload\src\cos\Upload');

        $info = $cosUpload->putUpload($user_id .'/'.$newFileName, $dirPath);
        if($info) {
            // 删除本地图片
//            Filesystem::delete($dirPath);
            return $info;
        }
        return false;
    }


}
