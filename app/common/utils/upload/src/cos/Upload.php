<?php

namespace app\common\utils\upload\src\cos;

use app\common\utils\upload\src\UploadInterface;
use think\App;
use app\common\utils\upload\src\cos\Cos as cosUpload;

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

                 return ['path' => "https://" .$putObject['Location'] ?? "","url" =>  $this->getObjectUrl($key)];
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

     public function download(): mixed
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

     public function doesObjectExist(): mixed
     {
         // TODO: Implement doesObjectExist() method.
     }

     public function headObject(): mixed
     {
         // TODO: Implement headObject() method.
     }

     /**
      * 获取访问URL
      * @param $key
      * @return mixed
      */
     public function getObjectUrl($key):mixed
     {
         try {
             $bucket = $this->cos->bucket; //存储桶，格式：BucketName-APPID

             // 请求成功 有效期30天
             $url =  $this->cos->getObjectUrl($bucket, $key,'+21600 minutes');
             if (!$url) return  false;
             return $url;
         } catch (\Exception $e) {
             // 请求失败
             return false;
         }

     }
 }