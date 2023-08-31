<?php


namespace app\home\dao\message;
use app\home\dao\BaseDao;
use app\model\UserReceiveModel;

class MessageReceiveDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return UserReceiveModel::class;

    }

    /**
     * @param array $where
     * @param $user_id
     * @param int $limit
     * @return bool|array
     */
    public function receiveList(array $where, $user_id)
    {


        if (empty($where) || empty($user_id)) return false;
        return $this->getModel()::where($where)
            ->field('msg_id,room_id,msg_form,msg_to,msg_content,send_time,create_time,msg_type,delivered')
            ->select()->toArray();

    }

    /**
     * 历史消息
     * @param array $where
     * @param $user_id
     * @param  $page
     * @param  $limit
     */
    public function historyMessageList(array $where, $user_id,  $page = 1,  $limit =  20)
    {


        if (empty($where) || empty($user_id)) return false;
        return $this->getModel()::where($where)
            ->when($page && $limit ,function ($query) use ($page,$limit) {
                $query->page($page,$limit);
            })
            ->limit($limit)
            ->order('seq desc')
            ->field('msg_id,seq,room_id,msg_form,msg_to,msg_content,send_time,create_time,msg_type,delivered')
            ->select()->toArray();

    }


}
