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











}
