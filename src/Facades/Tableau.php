<?php

namespace InterWorks\Tableau\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \InterWorks\Tableau\Tableau
 */
class Tableau extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \InterWorks\Tableau\Tableau::class;
    }
}
