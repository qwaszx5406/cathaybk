<?php

namespace Cathaybk\Api\Facades;

use Illuminate\Support\Facades\Facade;

class CathaybkApiFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CathaybkApi';
    }
}