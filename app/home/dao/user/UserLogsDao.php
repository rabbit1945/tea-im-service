<?php


namespace app\home\dao\user;
use app\model\UserLogsModel;
use app\home\dao\BaseDao;

class UserLogsDao extends BaseDao
{

    public function setModel(): string
    {
        // TODO: Implement setModel() method.
        return UserLogsModel::class;

    }

    /**
     * 添加用户日志
     * @param $data
     * @return mixed
     */
    public function addUserLogs($data)
    {
        return $this->getModel()->save($data);
    }



}
