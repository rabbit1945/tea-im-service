<?php

namespace app\common\utils\upload\src;
/**
 * 定义上次接口
 */
interface UploadInterface
{

    /**
     * 简单上传
     */
    public function putUpload(string $key, string $body, bool $is_file = true): array|bool;

    /**
     * 获取可以访问的URL
     * @param $key
     * @return mixed
     */
    public function getObjectUrl($key): mixed;


    /**
     * 列出列表
     * @return mixed
     */
    public function listObjects(): mixed;

    /**
     * 下载
     * @return mixed
     */
    public function download(): mixed;


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
     * @return mixed
     */
    public function doesObjectExist(): mixed;

    /**
     * 查询对象信息
     * @return mixed
     */
    public function headObject(): mixed;





















}