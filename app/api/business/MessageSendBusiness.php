<?php


namespace app\api\business;
use app\common\utils\IdRedisGenerator;
use app\common\utils\SensitiveWord;
use app\api\dao\message\MessageSendDao;
use think\App;
use think\facade\Log;

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

    /**
     * 获取消息序列号
     * @return mixed
     */
    public function getSequence()
    {
        $idGenerator =  app()->make(IdRedisGenerator::class);
        $idGenerator->generator('1',strtotime('2023-08-25')*1000);
        return $idGenerator->getSequence();

    }

    /**
     * 获取@联系人列表
     * @param array $contact
     * @return array
     */
    public function getContactList(array $contact): array
    {
        $user_ids = [];
        foreach ($contact as $val) {
            $user_id = $val['user_id'];
            $user_ids[] = $user_id;

        }

        return $user_ids;
    }

    /**
     * 获取消息内容
     * @param $msgData
     * @param int $sender
     * @return array
     */
    public function getContext($msgData, int $sender = 0): array
    {

        $time = floor(microtime(true) * 1000)| 0;
        $getContactList = $msgData['contactList'];
        $getContactUsers = $this->getContactList($getContactList);
        $user_id = $msgData['user_id'];
        $msg = trim($msgData['msg']);
        $seq = $this->getSequence();
        $room = $msgData['room_id'];
        $msg_type = 0;
        if (!empty($room)) {
            $msg_type = 2;

        }

        $msgData['seq'] = $seq;
        $msgData['sender'] = $sender;
        $msgData['msg'] = $msg;
        $msgData['msg_type'] = $msg_type;
        $msgData['send_timestamp'] = $time;
        $msgData['send_time'] = date("Y-m-d H:i:s",time());
        $msgData['contactList'] =implode(",",$getContactUsers);
        // 屏蔽敏感词
        $sensitiveWord = app()->make(SensitiveWord::class);
        $sensitiveWord->addWords(false);
        $msgData['sensitiveMsg'] = $sensitiveWord->filter($msgData['msg'],'*',2);

        return $msgData;

    }









}
