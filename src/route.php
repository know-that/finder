<?php

use Illuminate\Support\Facades\Route;
use KnowThat\LaravelLogger\Controllers\IndexController;

Route::prefix('know-that/laravel-logger')
    ->group(function ($router) {
        $router->get('/', IndexController::class);
    });
