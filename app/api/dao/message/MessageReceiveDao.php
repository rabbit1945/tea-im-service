<?php


namespace app\api\dao\message;
use app\api\dao\BaseDao;
use app\model\UserReceiveModel;

class MessageReceiveDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return UserReceiveModel::class;

    }

    /**
     * 历史消息
     * @param array $where
     * @param $user_id
     * @param int $page
     * @param int $limit
     * @return array|false
     */
    public function getMessageList(array $where, $user_id, int $page = 1, int $limit =  20): array|false
    {
        if (empty($where) || empty($user_id)) return false;
        return $this->getModel()::where($where)
            ->field(
                'UserReceiveModel.id,UserReceiveModel.seq,UserReceiveModel.room_id,UserReceiveModel.msg_form,
                UserReceiveModel.msg_to,MessageListModel.send_time,UserReceiveModel.delivered,
                UserReceiveModel.create_time,MessageListModel.msg_type,MessageListModel.msg_content,
                MessageListModel.content_type,MessageListModel.file_name,MessageListModel.file_size,
                MessageListModel.original_file_name,MessageListModel.md5,MessageListModel.total_chunks,
                MessageListModel.upload_status,MessageListModel.chunk_number,MessageListModel.merge_number,
                MessageListModel.is_revoke,MessageListModel.file_path,MessageListModel.thumb_path'
            )
            ->hasWhere('userReceive')
            ->when($page && $limit ,function ($query) use ($page,$limit) {
                $query->page($page,$limit);
            })
            ->limit($limit)
            ->order('UserReceiveModel.seq desc')

            ->select()->toArray();

    }


}
