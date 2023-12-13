<?php
namespace app\api\business;
use app\api\dao\message\MessageDao;
use think\App;
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

    public function update($id,$data,$key = null)
    {
        if (!$data || !$id) return false;
        return $this->dao->update($id,$data,$key);
    }

    public function count($where)
    {
        if (!$where) return false;
        return $this->dao->count($where);

    }










}
