<?php


namespace app\api\business;
use app\api\dao\message\MessageDao;
use app\common\utils\Upload;
use app\service\RedisService;
use think\App;
use think\facade\Filesystem;

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
     * @param $data
     * @param string $field
     * @param null $order
     * @return bool|array
     */
    public function find($data,string $field = "*",$order = null): bool|array
    {
        if (!$data) return false;
        return $this->dao->find($data,$field,$order);
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
        if (!$data || !$id) return false;
        return $this->dao->update($id,$data,$key);

    }


    /**
     * 修改消息
     * @param $seq
     * @param $uploadStatus
     * @return bool|void
     */
    public function updateMsgStatus($seq,$uploadStatus)
    {

        $where = [
            "seq" => $seq
        ];
        $data = [
            "upload_status" => $uploadStatus
        ];
        return $this->save($where,$data);


    }

    public function count($where)
    {
        if (!$where) return false;
        return $this->dao->count($where);

    }










}
