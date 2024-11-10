<?php


namespace app\api\business;
use app\api\dao\user\RoomUserDao;
use app\api\dao\room\RoomDao;
use think\App;
class RoomBusiness
{
    protected $app;
    protected $dao;

    public function __construct(App $app,RoomDao $dao) {
        $this->dao = $dao;
        $this->app = $app;
    }

    public function index($user_id)
    {
        return $this->dao->list($user_id);
    }



}
