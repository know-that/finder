<?php

namespace KnowThat\Finder\Enums;

enum FileTypeEnum: string
{
    case Dir = 'dir';
    case File = 'file';

    public function text()
    {
        return match($this) {
            self::Dir => '文件夹',
            self::File => '文件'
        };
    }
}
