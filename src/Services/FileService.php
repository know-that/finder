<?php

namespace KnowThat\Finder\Services;

class FileService
{
    /**
     * 字节转换可读形式
     *
     * @param $filesize
     * @return string
     */
    public function getByteSize($filesize): string
    {
        if ($filesize >= 1073741824) {
            //转成GB
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            //转成MB
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            //转成KB
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            //不转换直接输出
            $filesize .= ' byte';
        }
        return $filesize;
    }
}
