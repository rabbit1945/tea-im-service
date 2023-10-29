<?php


namespace app\api\dao;


abstract class BaseDao
{

    /**
     * 设置模型
     * @return string
     */
    abstract public function setModel():string;

    /**
     * 获取模型
     * @return mixed|object
     */
    public function getModel() {
        return app()->make($this->setModel());
    }

    /**
     * 获取主键
     * @return array|string
     */
    protected function getPk()
    {
        return $this->getModel()->getPk();
    }

    /**
     * @param array $where
     * @param string $field
     * @return array
     */
    public function find(array $where,string $field = "*"): array
    {
        if (empty($where)) return [];
        $find = $this->getModel()::where($where)->field($field)->find();
        if ($find) {
            return  $find->toArray();
        }
        return [];

    }

    public function save($where,$data)
    {
        $find = $this->getModel()::where($where)->find();
        if (!$find) return false;
        return  $find->save($data);
    }

    /**
     * 获取列表
     * @param array $where
     *
     */
    public function list(array $where)
    {
        if (empty($where)) return [];
        $list = $this->getModel()::where($where)->select();
        if ($list) {
            return $list->toArray();
        }
        return $list;

    }

    /**
     * 添加数据
     * @param array $data
     * @return mixed
     */
    public function create(array $data){
        if (empty($data)) return false;
        $add = $this->getModel()::create($data);
        return  $add;
    }

    /**
     * 批量更新
     * @param array $data
     * @return mixed
     */
    public function saveAll(array $data)
    {
        if (empty($data)) return false;
        $save = $this->getModel()->saveAll($data);
        // echo $this->getModel()->getLastSql();
        return $save;
    }

    /**
     * 更新数据
     * @param $id
     * @param array $data
     * @param string|null $key
     * @return mixed
     */
    public function update($id, array $data, ?string $key = null)
    {

        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }

        return $this->getModel()::update($data, $where)->toArray();


    }

    public function count(array $where)
    {

        return $this->getModel()::where($where)->count();

    }


}
