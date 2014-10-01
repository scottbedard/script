<?php namespace Bedard\Script;

use Illuminate\Support\Facades\Facade;

class ScriptFacade extends Facade {
    public static function getFacadeAccessor() { return 'script';  }
}