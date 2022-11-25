<?php

use Illuminate\Support\Facades\Route;
use KnowThat\Finder\Controllers\IndexController;

Route::prefix('know-that/finder')
    ->group(function ($router) {
        $router->get('/', [IndexController::class, 'index']);
        $router->get('contents', [IndexController::class, 'contents']);
    });
