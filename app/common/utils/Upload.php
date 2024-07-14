<?php


namespace app\common\utils;
use think\App;
use  think\facade\Filesystem;
use think\exception\ValidateException;
use  Qcloud\Cos\Client;
use  app\common\utils\upload\src\cos\Upload as cosUpload;
use think\facade\Log;
use think\File;
use think\swoole\Sandbox;

/**
 * 上传工具
 * Class Upload
 * @package app\common\utils
 */

class Upload
{
    private int|float $maxsize = 100*1024*1024;

    const UPLOADING = 0; // 上传中

    const UPLOAD_SUCCESS = 1; // 上传成功

    const SENDING = 2; // 发送中

    const SEND_SUCCESS = 3; // 发送成功

    private App $app;
    public mixed $model;

    public function __construct(App $app)
    {
        $this->app = $app;

    }


    /**
     * @return float|int
     */
    public function getMaxSize(): float|int
    {
        return $this->maxsize;
    }

    /**
     * @param float|int $maxsize
     */
    public function setMaxSize(float|int $maxsize): void
    {
        $this->maxsize = $maxsize;
    }

    /**
     * 设置模型
     * @param mixed $model

     */
    public function setModel( string $model)
    {
        return $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getModel(): mixed
    {
        return $this->model;
    }

    /**
     * 简单上传
     * @param string|array $key
     * @param File|string $body
     * @param bool $is_file
     * @return bool|string
     */
    public function putUpload(string|array $key, File|string $body, bool $is_file): bool|string
    {
        return $this->app->make($this->getModel())->putUpload($key,$body,$is_file);
    }

    /**
     * 获取URL访问权限
     * @param string $key
     * @return string|bool
     */
    public function getObjectUrl(string $key): string|bool
    {
        return $this->app->make($this->getModel())->getObjectUrl($key);
    }

    /**
     * 判断是否存在和有权限
     * @param string $key
     * @return bool
     */
    public function doesObjectExist(string $key): bool
    {
        if ($this->app->make($this->getModel())->doesObjectExist($key)) return true;
        return false;
    }

    /**
     * 判断是否存在和有权限
     * @param string $key
     * @return mixed
     */
    public function headObject(string $key): mixed
    {
        return $this->app->make($this->getModel())->headObject($key);
    }


    /**
     * 创建缩略图
     * @param string $path
     * @param string $thumbPath
     * @param string $fileType
     * @param int $width
     * @param int $height
     * @return string|bool
     */
    public function createThumb(string $path, string $thumbPath,string $fileType, int $width = 200, int $height = 200): bool|string
    {

        return $this->app->make($this->getModel())->createThumb($path,$thumbPath,$fileType,$width,$height);
    }

    /**
     * 上传分块
     * @param array $args
     * @return mixed
     */
    public function uploadPart(array $args = []): mixed
    {
        $uploadPart =  $this->app->make($this->getModel())->uploadPart($args);
        if (!$uploadPart) return [];
        $totalChunks = $args['totalChunks'];
        $chunkNumber = $uploadPart['partId'];
        $uploadStatus = upload::UPLOADING;
        if ($totalChunks == $chunkNumber ) {
            $uploadStatus =  upload::UPLOAD_SUCCESS; // 1
        }
        $uploadPart['uploadStatus'] = $uploadStatus;
        return $uploadPart;
    }



}
