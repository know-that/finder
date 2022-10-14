<?php

namespace KnowThat\LaravelLogger\Controllers;

class IndexController
{
    public function __invoke(): string
    {
        return "Hello Know-That Log Viewer";
    }
}
