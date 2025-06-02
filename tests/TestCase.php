<?php

namespace InterWorks\Tableau\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use InterWorks\Tableau\TableauServiceProvider;
use InterWorks\Tableau\Tests\Traits\MocksTableauAPI;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use MocksTableauAPI;

    protected function setUp(): void
    {
        parent::setUp();

        // make sure, our .env file is loaded
        $this->app->useEnvironmentPath(__DIR__ . '/..');
        $this->app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($this->app);

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'InterWorks\\Tableau\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
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
