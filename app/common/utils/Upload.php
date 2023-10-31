<?php


namespace app\common\utils;
use  think\facade\Filesystem;
use think\exception\ValidateException;
/**
 * 上传工具
 * Class Upload
 * @package app\common\utils
 */

class Upload
{
    private int|float $maxsize = 300*1024*1024;

    const UPLOADING = 0; // 上传中

    const UPLOAD_SUCCESS = 1; // 上传成功

    const SENDING = 2; // 发送中

    const SEND_SUCCESS = 3; // 发送成功



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

    public function fileUpload($dir,$file,$name)
    {
        try {
            if (empty($file) || empty($dir) || empty($name)) return false;

            return Filesystem::putFileAs( $dir, $file, $name);
        } catch ( ValidateException $e) {

            return $e->getMessage();

        }


    }

}
