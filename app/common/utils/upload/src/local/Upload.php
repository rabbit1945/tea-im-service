<?php

namespace app\common\utils\upload\src\local;

use app\common\utils\upload\src\ImagesInterface;
use League\Flysystem\FileExistsException;
use think\App;
use think\Exception;
use think\facade\Config;
use think\facade\Filesystem;
use think\Image;

/**
 * 上传下载接口
 */
 class Upload  implements ImagesInterface
{


     protected App $app;
     public function __construct(App $app)
     {
        $this->app = $app;

     }


     /**
      * @param string $path
      * @param string $thumbPath
      * @param string $fileType
      * @param int $width
      * @param int $height
      * @return false|string
      */
     public function createThumb(string $path, string $thumbPath, string $fileType,int $width = 200, int $height = 200)
     {
         try {
             //创建一个真彩色的图像，支持的颜色数较多
             $dst = imagecreatetruecolor($width, $height); //目标图宽高
             switch ($fileType) {
                 case 'gif':
                     $src = imagecreatefromgif($path); //源图
                     break;
                 case 'png':
                     $src = imagecreatefrompng($path); //源图
                     break;
                 case 'jpg':
                 case 'jpeg':
                     $src = imagecreatefromjpeg($path); //源图
                     break;

                 case 'wbmp':
                     $src = imagecreatefromwbmp($path); //源图
                     break;
                 default:
                     return false;
             }
             $width = imagesx($src);    //源图的宽度
             $height = imagesy($src);    //源图的高度
             if (!imagecopyresampled($dst,$src,0,0,0,0,200,200,$width,$height)) return false;
             // 判断文件是否存在
             if (!is_dir($thumbPath)) {
                 //创建空文件
                 Filesystem::write( $thumbPath,"");
             }
             $thumbPath =Config::get("filesystem.disks.public.root").$thumbPath;
             imagegif($dst,$thumbPath);

             //销毁图片资源
             imagedestroy($dst);
             imagedestroy($src);

             return $thumbPath;

         } catch (FileExistsException $e) {

             return false;

         }


     }
 }