<?php

namespace KnowThat\Finder\Constants;

class FileTypeConstant
{
    const DIR = 'dir';
    const FILE = 'file';

    /**
     * 文本转换
     *
     * @param string $type
     * @return string|null
     */
    public static function text(string $type): ?string
    {
        switch ($type) {
            case self::DIR:
                $string = "文件夹";
                break;

            case self::FILE:
                $string = "文件";
                break;

            default:
                $string = null;
                break;
        }
        return $string;
    }
}
