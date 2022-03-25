<?php

namespace Gam\LaravelSatCatalogs\Tests\Unit;

use Gam\LaravelSatCatalogs\Tests\TestCase;

class UpdateCatalogsTest extends TestCase
{
    /**
     * @test
     */
    public function updateCatalogs(): void
    {
        $this->artisan('catalogs:update', ['--path' => __DIR__ . '/../_files'])
            ->assertSuccessful();

        $this->assertFileExists(self::DB_PATH);
    }
}
