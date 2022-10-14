<?php

namespace KnowThat\LaravelLogger;

use Illuminate\Support\ServiceProvider;

class LoggerProvider extends ServiceProvider
{
    /**
     * Register services.
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot(): void
    {
        // 加载路由
        $this->loadRoutesFrom(__DIR__ . '/route.php');

        // 发布配置文件
        $this->publishes([
            __DIR__.'/config.php' => config_path('know-that/laravel-logger.php')
        ], 'kt.lv.config');
    }
}
