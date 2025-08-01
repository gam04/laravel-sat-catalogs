<?php

namespace Gam\LaravelSatCatalogs\Tests;

use Gam\LaravelSatCatalogs\CatalogsServiceProvider;
use Illuminate\Support\Facades\Config;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected const DB_PATH = __DIR__ . '/_files/catalogs.sqlite3';

    protected function setUp(): void
    {
        parent::setUp();
        Config::set([
            /* 'catalogs' => [
                 'driver' => 'custom'
             ],*/
            'database' => [
                'connections' => [
                    'catalogs' => [ // custom
                        'driver' => 'sqlite',
                        'url' => '',
                        'database' => self::DB_PATH,
                        'prefix' => '',
                        'foreign_key_constraints' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param $app
     * @return class-string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            CatalogsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
