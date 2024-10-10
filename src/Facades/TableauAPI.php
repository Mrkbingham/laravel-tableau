<?php

namespace InterWorks\Tableau\Facades;

use Illuminate\Support\Facades\Facade;
use InterWorks\Tableau\API\Workbooks;

/**
 * @see \InterWorks\Tableau\TableauAPI
 *
 * @method static Workbooks workbooks()
 * @method static \InterWorks\Tableau\API\Views views()
 * @method static \InterWorks\Tableau\API\Datasources datasources()
 */
class TableauAPI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \InterWorks\Tableau\TableauAPI::class;
    }
}
