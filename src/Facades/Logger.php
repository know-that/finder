<?php
namespace KnowThat\Test\Facades;

use Illuminate\Support\Facades\Facade;
use KnowThat\LogViewer\Services\LoggerService;

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
