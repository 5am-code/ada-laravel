<?php

namespace Ada;

use Illuminate\Support\Facades\Facade;

/**
 * @see \5amcode\AdaLaravel\Skeleton\SkeletonClass
 */
class Ada extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ada';
    }
}
