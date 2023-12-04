<?php

namespace app\common\utils\upload\src;
use think\contract\Arrayable;

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
     * @param string $key
     * @return array|bool
     */
    public function doesObjectExist(string $key): array|bool;

    /**
     * 查询对象信息
     * @param string $key
     * @return array|bool|object
     */
    public function headObject(string $key): array|bool|object;

    /**
     * 创建缩略图
     * @param string $path
     * @param string $thumbPath
     * @param int $width
     * @param int $height
     * @return string|array|bool
     */
    public function createThumb(string $path, string $thumbPath, int $width = 200, int $height = 200): string|bool;
}