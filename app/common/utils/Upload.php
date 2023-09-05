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
