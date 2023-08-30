<?php


namespace app\home\business;
use app\home\dao\message\MessageSendDao;
use think\App;

class MessageSendBusiness
{
    protected $app;
    protected $dao;

    public function __construct(App $app,MessageSendDao $dao) {
        $this->dao = $dao;
        $this->app = $app;
    }

    /***
     * 保存某个用户发送的那些消息
     * @param array $data
     * @return false|mixed
     */
    public function addSend(array &$data){
        if (empty($data)) return false;
        return $this->dao->create($data);
    }









}
