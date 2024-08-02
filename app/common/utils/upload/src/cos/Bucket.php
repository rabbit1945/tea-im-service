<?php

namespace app\common\utils\upload\src\cos;

use app\common\utils\upload\src\BucketInterface;
use think\App;

 class Bucket extends Cos implements BucketInterface
{


    /**
     * 创建存储库
     * @param array $args
     * @return string
     */
    public function createBucket(array $args = []): string
    {
        try {
            return $this->cosClient->createBucket(
                 $args
             );
        } catch (\Exception $e) {
            return $e->getMessage();
        }


    }

    /**
     * @param array $args
     * @return string
     */
    public function deleteBucket(array $args = []): string
    {

        try {
            return $this->cosClient->deleteBucket(
                $args
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * 检查存储桶是否存在，是否有权限访问
     * @param array $args
     * @return string
     */
    public function headBucket(array $args = []): string
    {

        try {
            return $this->cosClient->headBucket(
                $args
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


     public function deleteBuckets(array $args = []): mixed
     {
         // TODO: Implement deleteBuckets() method.
     }

     public function listBuckets(): mixed
     {
         // TODO: Implement listBuckets() method.
     }

     public function doesBucketExist(array $args = array()): mixed
     {
         // TODO: Implement doesBucketExist() method.
     }
 }