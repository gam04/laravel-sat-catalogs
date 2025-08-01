<?php

namespace Gam\LaravelSatCatalogs\Tests\Unit;

use Gam\LaravelSatCatalogs\Tests\TestCase;
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
}
