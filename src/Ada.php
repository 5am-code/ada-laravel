<?php

namespace Ada;

use Ada\Engine\Engine;
use Ada\Index\Index;

class Ada
{
    public static function index(): Index
    {
        return app()->make(Index::class);
    }

    public static function engine(): Engine
    {
        return app()->make(Engine::class);
    }
}
