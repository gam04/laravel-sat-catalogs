<?php

namespace Gam\LaravelSatCatalogs\Tests\Unit;

use Gam\LaravelSatCatalogs\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;

class UpdateCatalogsTest extends TestCase
{
    #[Test]
    public function updateCatalogs(): void
    {
        $this->artisan('catalogs:update', ['--path' => build_path([__DIR__ , '..', '_files'])])
            ->assertSuccessful();

        $this->assertFileExists(self::DB_PATH);
    }

    #[Test]
    public function updateCatalogsWithoutDriverFails(): void
    {
        Config::set('database.connections.catalogs');
        $this->artisan('catalogs:update', ['--path' => build_path([__DIR__ , '..', '_files'])])
            ->assertFailed()
            ->expectsOutputToContain('The driver path is not set');
    }
}
