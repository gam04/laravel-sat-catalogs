<?php

namespace Gam\LaravelSatCatalogs\Tests;

use Gam\LaravelSatCatalogs\CatalogsServiceProvider;
use Illuminate\Support\Facades\Config;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected const DB_PATH = __DIR__ . '/_files/catalogs.sqlite3';

    public function setUp(): void
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

    protected function getPackageProviders($app)
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
