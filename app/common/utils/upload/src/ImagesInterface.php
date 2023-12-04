<?php

namespace app\common\utils\upload\src;
use think\contract\Arrayable;

/**
 * 图片处理
 */
interface ImagesInterface
{

    /**
     * 创建缩略图
     * @param string $path
     * @param string $thumbPath
     * @param string $fileType
     * @param int $width
     * @param int $height
     */
    public function createThumb(string $path, string $thumbPath,  string $fileType,int $width = 200, int $height = 200);
}