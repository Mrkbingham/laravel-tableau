<?php

namespace InterWorks\Tableau\Tests;

use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use InterWorks\Tableau\TableauServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Load the .env.testing file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
        $dotenv->load();

        // Set up factory names
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'YourVendor\\Tableau\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            TableauServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-tableau_table.php.stub';
        $migration->up();
        */
    }
}
