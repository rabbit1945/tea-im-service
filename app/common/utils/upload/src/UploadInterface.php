<?php

namespace app\common\utils\upload\src;
use think\contract\Arrayable;
use think\File;

/**
 * 定义上次接口
 */
interface UploadInterface
{

    /**
     * 简单上传
     */
    public function putUpload(string|array $key, string|File $body, bool $is_file = true):mixed;

    /**
     * 获取可以访问的URL
     * @param string $key
     * @returnstring|bool
     */
    public function getObjectUrl(string $key): string|bool;


    /**
     * 列出列表
     * @return mixed
     */
    public function listObjects(): mixed;

    /**
     * 下载
     * @param string $key
     */
    public function download(string $key):object|array|bool;


    /**
     * 删除对象
     * @return mixed
     */
    public function deleteObject(): mixed;


    /**
     * 删除对象
     * @return mixed
     */
    public function deleteObjects(): mixed;


    /**
     * 检查存储桶中是否存在某个对象。
     * @param string $key // 路径
     * @return bool
     */
    public function doesObjectExist(string $key): bool;

    /**
     * 查询对象信息
     * @param string $key
     * @return array|bool|object
     */
    public function headObject(string $key): array|bool|object;

    /**
     * 初始化分块上传
     * @param array $args
     * @return mixed
     */
    public function  createMultipartUpload(array $args = []): mixed;


    /**
     * 分块上传
     * @param array $args
     * @return mixed
     */
   public function  uploadPart(array $args =[]): mixed;


    /**
     * 完成分块上传
     * @param array $args
     * @return mixed
     */
   public function completeMultipartUpload(array $args = array()): mixed;


    /**
     * 终止分块上传
     * @param array $args
     * @return mixed
     */

    public function abortMultipartUpload(array $args = array()): mixed;


}