<?php

namespace app\common\utils\upload\src\local;

use app\api\business\UploadBusiness;
use app\common\utils\IdRedisGenerator;
use app\common\utils\upload\src\ImagesInterface;
use app\common\utils\upload\src\UploadInterface;
use League\Flysystem\FileExistsException;
use think\App;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Filesystem;
use think\facade\Log;
use think\File;

/**
 * 上传下载接口
 */
 class Upload  implements ImagesInterface,UploadInterface
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
     public function createThumb(string $path, string $thumbPath, string $fileType,int $width = 200, int $height = 200): bool|string
     {
         try {
             if (!file_exists($path)) return false;
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

     /**
      * 简单上传
      * @param string|array $key 文件唯一值
      * @param File|string $body 文件
      * @param bool $is_file
      * @return string|bool
      */
     public function putUpload(string|array $key, File|string $body, bool $is_file = true): string|bool
     {

         try {
             if ($is_file) {
                 return Filesystem::putFileAs( $key['dir'], $body,$key['name']);
             } else {
                 return Filesystem::put($key,$body);
             }

         } catch ( ValidateException $e) {
             return false;
         }


     }

     public function getObjectUrl(string $key): string|bool
     {
         // TODO: Implement getObjectUrl() method.
     }

     public function listObjects(): mixed
     {
         // TODO: Implement listObjects() method.
     }

     public function download(string $key): object|array|bool
     {
         // TODO: Implement download() method.
     }

     public function deleteObject(): mixed
     {
         // TODO: Implement deleteObject() method.
     }

     public function deleteObjects(): mixed
     {
         // TODO: Implement deleteObjects() method.
     }

     /**
      * @param string $key // 路径
      * @return bool
      */
     public function doesObjectExist(string $key): bool
     {

         if (Filesystem::has( $key)) return true;
         return false;
     }

     public function headObject(string $key): array|bool|object
     {
         // TODO: Implement headObject() method.
     }

     /**
      * 初始化分块
      * @param array $args
      * @return array|bool
      */
     public function createMultipartUpload(array $args = []): array|bool
     {
         try {
             // 生成对象id
             $idGenerator = app()->make(IdRedisGenerator::class);
             $idGenerator->generator('1', strtotime('2023-08-25') * 1000);
             return [
                 "dir" => $args['dir'], // 目录
                 "key" => $args['key'], // key 唯一值名称
                 "md5" => $args['md5'], // 文件校验
                 "uploadId" => $idGenerator->getSequence(), // 上传的唯一标识
             ];
         } catch ( ValidateException $e) {
             return false;
         }

     }

     /**
      * 上传分块
      * @param array $args
      * @return array|bool
      */
     public function uploadPart(array $args = []): array|bool
     {
         try {
             $dir  = $args['dir']; // 目录
             $key  = $args['key']; //  key 唯一值名称
             $path = $dir.$key;  // 分块地址
             if ($this->doesObjectExist($path)) return true;
             $uploadId = $args['uploadId']; // 上传id
             $partId = $args['partId']; // 分块id

             $file = $args['file']; // 数据

             $uploadBusiness = app()->make(UploadBusiness::class);


             if (!$uploadBusiness->uploadFile($file,$path)) return false;

             return [
                 "uploadId" => $uploadId,
                 "dir"      => $args['dir'], // 目录
                 "key"      => $args['key'], // key 唯一值名称
                 "partId"   => $partId,// 分块ID
                 "path"     => $path //分块地址
             ];
         } catch ( ValidateException $e) {
             return false;
         }
     }

     /**
      * 完成分块，进行合并
      * @param array $args
      * @return bool|array
      */
     public function completeMultipartUpload(array $args = array()): bool|array
     {
         try {
             $uploadId = $args['uploadId']; // 上传id
             $totalChunks = $args['totalChunks']; // 总的分块数
             $dir   = $args['dir']; // 分块的目录
             $key   = $args['key']; //  key 唯一值合并的名称
             $parts = $args['parts']; // 分块列表
             $path  = $dir.$key; // 地址
             $count = 0;
             foreach ($parts as $val) {
                 $file = $val['file'];
                 if (!$this->putUpload($path,$file,false)) return false;
                 $count ++;
             }

             if ($totalChunks != $count) return false;
             return [
                 "uploadId" => $uploadId,
                 "path" => $path, // 返回合并的地址
             ];
         } catch ( ValidateException $e) {
             return false;
         }
     }

     public function abortMultipartUpload(array $args = array()): mixed
     {
         // TODO: Implement abortMultipartUpload() method.
     }
 }