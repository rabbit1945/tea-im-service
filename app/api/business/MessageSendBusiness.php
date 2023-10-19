<?php


namespace app\api\business;
use app\common\utils\IdRedisGenerator;
use app\common\utils\SensitiveWord;
use app\api\dao\message\MessageSendDao;
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
        $data =   [
            'room_id'   => $room,
            'seq'       => $seq,
            'user_id'   => $user_id, // user
            'nick_name' => $msgData['nick_name'], // 名称
            'userLogo'  => $msgData['userLogo'], // img
            'file_name' => $msgData['file_name']?? "", // 文件名称
            'file_size' => $msgData['file_size']??"", // 文件大小
            'sender'    => $sender, //客户端
            'msg'       => $msg, // 消息
            'msg_type'  => $msg_type,
            'content_type'  => $msgData['content_type'],
            'send_timestamp'=> $time,
            'send_time'     => date("Y-m-d H:i:s",time()),   // 发送时间
            'contactList'   => implode(",",$getContactUsers)
        ];

       // 屏蔽敏感词
        $sensitiveWord = app()->make(SensitiveWord::class);
        $sensitiveWord->addWords(false);
        $data['sensitiveMsg'] = $sensitiveWord->filter($data['msg'],'*',2);

        return $data;

    }









}
