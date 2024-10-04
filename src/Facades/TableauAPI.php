<?php

namespace InterWorks\Tableau\Facades;

use Illuminate\Support\Facades\Facade;
use InterWorks\Tableau\Api\Workbooks;

/**
 * @see \InterWorks\Tableau\TableauAPI
 *
 * @method static Workbooks workbooks()
 * @method static \InterWorks\Tableau\Api\Views views()
 * @method static \InterWorks\Tableau\Api\Datasources datasources()
 */
class TableauAPI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \InterWorks\Tableau\TableauAPI::class;
    }
}
