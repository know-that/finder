<?php

namespace KnowThat\LaravelLogger\Facades;

use Illuminate\Support\Facades\Facade;
use KnowThat\LaravelLogger\Services\LoggerService;

class Test extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return LoggerService::class;
    }
}
