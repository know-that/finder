<?php

use Illuminate\Support\Facades\Route;
use KnowThat\LogViewer\Controllers\IndexController;

Route::prefix('know-that/log-viewer')
    ->group(function ($router) {
        $router->get('/', IndexController::class);
    });
