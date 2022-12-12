<?php

namespace KnowThat\Finder\Services;

/**
 * 文件内容
 */
trait ContentsTrait
{
    /**
     * 日志行数
     *
     * @return void
     */
    public function logLines(): void
    {
        preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/', $this->data->get('contents'), $lines);
        dump($lines);
    }
}
