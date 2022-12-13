<?php

namespace KnowThat\Finder\Controllers;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * 模板名称前缀
     * @var string
     */
    public $viewPrefix = 'kt.finder::';

    /**
     * 文件路径前缀
     * @var string
     */
    protected $base;

    /**
     * 文件路径前缀名称
     * @var string
     */
    protected $baseName;

    /**
     * constructor
     */
    public function __construct()
    {
        $base = config('know-that.finder.base');
        $this->base = $base === '/' ? '' : $base;
        $names = array_filter(explode('/', $this->base));
        $this->baseName = $names[count($names) - 1] ?? '根';
    }
}
