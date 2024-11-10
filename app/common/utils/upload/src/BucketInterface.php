<?php

namespace app\common\utils\upload\src;
/*
 * 定义存储桶的接口
 */
interface BucketInterface
{

    /**
     * 创建存储桶
     * @param array $args
     * @return mixed
     */
    public function createBucket(array $args = []): mixed;

    /**
     * 删除存储桶
     * @param array $args
     * @return mixed
     */
    public function deleteBucket(array $args = []): mixed;

    /**
     * 删除所有存储桶
     * @param array $args
     * @return mixed
     */
    public function deleteBuckets(array $args = []): mixed;

    /**
     * 查询存储桶列表
     * @return mixed
     */
    public function listBuckets(): mixed;

    /**
     * 检索存储桶
     * @param array $args
     * @return mixed
     */
    public function headBucket(array $args = []): mixed;

    /**
     * 检查存储桶是否存在
     * @param array $args
     * @return mixed
     */
    public function doesBucketExist(array $args = array()): mixed;
    

}