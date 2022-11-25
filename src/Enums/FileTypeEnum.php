<?php

namespace KnowThat\Finder\Enums;

enum FileTypeEnum: string
{
    case Dir = 'dir';
    case File = 'file';

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match($this) {
            self::Dir => '文件夹',
            self::File => '文件'
        };
    }
}
