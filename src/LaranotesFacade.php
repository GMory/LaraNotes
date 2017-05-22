<?php

namespace Gmory\Laranotes;

use Illuminate\Support\Facades\Facade;

class LaranotesFacade extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'gmory-laranotes';
    }
}