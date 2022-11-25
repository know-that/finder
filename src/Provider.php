<?php

namespace KnowThat\Finder;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
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

        // 视图
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'kt.finder');

        // 资源文件
        $this->publishes([
            __DIR__.'/config.php' => config_path('know-that/finder.php'),
            __DIR__.'/../public' => public_path('vendor/kt.finder'),
        ], 'kt.finder');
    }
}
