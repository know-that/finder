<?php

namespace KnowThat\LogViewer;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function boot()
    {
        // 加载路由
        $this->loadRoutesFrom(__DIR__ . '/route.php');

        // 发布配置文件
        $this->publishes([
            __DIR__.'/config.php' => config_path('know-that/log-viewer.php')
        ], 'kt.lv.config');
    }
}
