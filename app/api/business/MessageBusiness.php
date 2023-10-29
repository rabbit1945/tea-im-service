<?php


namespace app\api\business;
use app\api\dao\message\MessageDao;
use app\common\utils\ImJson;
use app\common\utils\Upload;
use app\service\RedisService;
use League\Flysystem\FileNotFoundException;
use think\App;
use think\facade\Filesystem;
use think\facade\Log;

class MessageBusiness
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var MessageDao
     */
    protected $dao;

    /**
     * @var
     */
    protected static $redis;


    public function __construct(App $app,MessageDao $dao) {
        $this->dao = $dao;
        $this->app = $app;

    }

    /**
     * 添加消息
     *
     */
    public function addMessage($data)
    {
        if (empty($data)) return false;
        return $this->dao->create($data);
    }


    /**
     * 上传
     */
    public function upload($dir,$file,$name)
    {
        $upload = $this->app->make(Upload::class);
        $path = $upload->fileUpload($dir,$file,$name);
        if (!$path) return false;
        return $path;

    }

    /**
     * 上传base64
     * @param $base64
     * @param $fileName
     * @param $dir
     * @return array|false
     */
    public function uploadBase64($base64,$fileName,$dir)
    {
        $reg = '/data:image\/(\w+?);base64,(.+)$/si';
        preg_match($reg,$base64,$match_result);
        if (!$match_result)  return false;
        $baseImg=str_replace($match_result[1], '', $base64);
        $baseImg=str_replace('=','',$baseImg);
        $imgLen = strlen($baseImg);
        $fileSize = intval($imgLen - ($imgLen/8)*2);

        $fileName = $fileName.'_'.$match_result[1];
        $path = $dir.$fileName;
        $upload = file_put_contents($path,base64_decode($match_result[2]));
        return [
            "isSuccess" => $upload,
            "fileSize" => $fileSize,
            "fileName"  => $fileName,
        ];
    }

    /**
     * @param $data
     * @return bool|array
     */
    public function find($data): bool|array
    {
        if (!$data) return false;
        return $this->dao->find($data);
    }

    public function save($where,$data)
    {
        if (!$data || !$where) return false;
        return $this->dao->save($where,$data);
    }

    public function mergeList($key, $dir, $path,$totalChunks,$filename,$user_id)
    {
        $redisService = $this->app->make(RedisService::class);

        $dirPath = $dir.'/'.$path;
        if (!$mergeFile = fopen($dirPath, "wb")) return false;
        //flock($hander,LOCK_EX)文件锁
        if ( flock($mergeFile, LOCK_EX) ) {
            for( $index = 0; $index < $totalChunks; $index++ ) {
                $filePath = $dir.'/files/'.$user_id;
                $num = $index+1;
                chmod("{$filePath}/{$num}_{$user_id}_{$filename}",0777);
                if (!$in = fopen("{$filePath}/{$num}_{$user_id}_{$filename}", "rb")) {
                    break;
                }
                while ($buff = fread($in, 4096)) {
                    fwrite($mergeFile, $buff);
                }
                fclose($in);
                unlink("{$filePath}/{$num}_{$user_id}_{$filename}");
                $redisService->ZREM($key,"file/"."{$num}_{$user_id}_{$filename}");
            }
            flock($mergeFile, LOCK_UN);
        }
        fclose($mergeFile);
        return true;

    }


    public function update($id,$data,$key = null)
    {
       return $this->dao->update($id,$data,$key);

    }


    /**
     * 修改消息
     * @param $file_name
     * @param $uploadStatus
     * @return bool|void
     */
    public function updateMsgStatus($file_name,$uploadStatus)
    {

        $where = [
            "file_name" => $file_name
        ];
        $data = [
            "upload_status" => $uploadStatus
        ];
        return $this->save($where,$data);


    }










}
