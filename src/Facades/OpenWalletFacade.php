<?php

namespace Cathaybk\Api\Facades;

use Illuminate\Support\Facades\Facade;

class OpenWalletFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OpenWallet';
    }
}