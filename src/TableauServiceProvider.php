<?php

namespace InterWorks\Tableau;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use InterWorks\Tableau\Commands\TableauCommand;

class TableauServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-tableau')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_tableau_table')
            ->hasCommand(TableauCommand::class);
    }
}
