<?php

namespace app\common\utils\upload\src\cos;

use app\common\utils\upload\src\UploadInterface;
use think\App;
use app\common\utils\upload\src\cos\Cos as cosUpload;
use think\facade\Log;

/**
 * 上传下载接口
 */
 class Upload  implements UploadInterface
{

     protected Cos $cos;
     protected App $app;

     public function __construct(App $app, cosUpload $cos)
    {
        $this->app = $app;
        $this->cos = $cos;
    }


     /**
      * 简单上传文件
      * @param string $key    文件名称目录
      * @param string $body   文件或二进制流
      * @param bool $is_file 是否是文件
      * @return array|false
      */
     public function putUpload(string $key, string $body, bool $is_file = true): array|bool
     {

         try {
             if ($is_file) {
                $body =   fopen($body, 'rb');
             }
             $data = [
                 'Bucket' => $this->cos->bucket , //存储桶，格式：BucketName-APPID
                 'Key' => $key, //对象在存储桶中的位置，即对象键
                 'Body' =>$body, //可为空或任意字符串
                 'ContentMD5' => true,
             ];

             $putObject = $this->cos->putObject($data);

             if ($putObject) {

                 return ['path' => $this->cos->getScheme()."://" .$putObject['Location'] ?? ""];
             }

         } catch (\Exception $e) {
             // 请求失败
             return ["error" =>$e->getMessage()];
         }

         return false;

     }

     public function listObjects(): mixed
     {
         // TODO: Implement listObjects() method.
     }

     /**
      * 下载到本地文件
      * @param string $key
      * @return object|array|bool
      */
     public function download(string $key): object|array|bool
     {

         try {
             $local_path = "./storage/files/$key";
             return $this->cos->download(
                 $this->cos->bucket, //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
                  $key,
                  $local_path,
                  array(
//                     'Progress' => $printbar, //指定进度条
                     'PartSize' => 10 * 1024 * 1024, //分块大小
                     'Concurrency' => 5, //并发数
                     'ResumableDownload' => true, //是否开启断点续传，默认为false
//                     'ResumableTaskFile' => 'tmp.cosresumabletask' //断点文件信息路径，默认为<localpath>.cosresumabletask
                 )
             );

         } catch (\Exception $e) {
             // 请求失败
             return ["error" =>$e->getMessage()];
         }


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
      * 判断对象是否存在
      * @param string $key
      * @return bool|array
      */
     public function doesObjectExist(string $key): bool|array
     {
         try {
             $bucket = $this->cos->bucket;
             return $this->cos->doesObjectExist(
                 $bucket,
                 $key
             );
         } catch (\Exception $e) {
             // 请求失败
             return ['error' => $e->getMessage()];
         }

     }

     /**
      * 判断是否存在和有权限
      * @param string $key
      * @return array|bool|object
      */
     public function headObject(string $key): array|bool|object
     {
         try {
             $bucket = $this->cos->bucket;
             $result =  $this->cos->headObject(
                [
                 'Bucket' => $bucket,
                 'Key'    => $key,
                ]
             );
             if (!isset($result['error'])) return false;
             return $result['structure']['data'];
         } catch (\Exception $e) {
             // 请求失败
             return ['error' => $e->getMessage()];
         }

     }

     /**
      * 获取访问URL
      * @param $key
      * @return string|bool
      */
     public function getObjectUrl($key): string|bool
     {
         try {
             $bucket = $this->cos->bucket; //存储桶，格式：BucketName-APPID

             // 请求成功 有效期1小时
             $url =  $this->cos->getObjectUrl($bucket, $key,'+60 minutes');
             if (!$url) return false;
             return $url;
         } catch (\Exception $e) {
             // 请求失败
             return false;
         }

     }

     /**
      * @param string $path
      * @param string $thumbPath
      * @param int $width
      * @param int $height
      * @return string|bool
      */
     public function createThumb(string $path, string $thumbPath, int $width = 200, int $height = 200): string|bool
     {
         // TODO: Implement createThumb() method.
     }
 }