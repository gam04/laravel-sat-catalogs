<?php

namespace Gam\LaravelSatCatalogs\Tests\Unit;

use Gam\LaravelSatCatalogs\Catalog;
use Gam\LaravelSatCatalogs\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;

class CatalogTest extends TestCase
{
    private Catalog $catalog;

    public function setUp(): void
    {
        parent::setUp();
        $this->catalog = new Catalog();
        // create the database if does not exist
        if (! file_exists(self::DB_PATH)) {
            $this->artisan('catalogs:update', ['--path' => build_path([__DIR__ , '..', '_files'])])
                ->execute();
        }
    }

    #[Test]
    public function exists(): void
    {
        $this->assertTrue($this->catalog->exists('cfdi_40_productos_servicios'));
    }

    #[Test]
    public function hasId(): void
    {
        $this->assertTrue($this->catalog->hasId('cfdi_40_productos_servicios'));
    }

    #[Test]
    public function doesNotHaveId(): void
    {
        $this->assertFalse($this->catalog->hasId('cfdi_40_colonias'));
    }

    #[Test]
    public function textOfWithId(): void
    {
        $this->assertEquals('Enero', $this->catalog->textOf('cfdi_40_meses', '01'));
        $this->assertEquals('Aguascalientes', $this->catalog->textOf('cfdi_40_estados', 'AGU', 'estado'));
        $this->assertEquals('N/A', $this->catalog->textOf('cfdi_40_estados', 'NO_EXISTE', 'estado', 'N/A'));
    }

    #[Test]
    public function clearCache(): void
    {
        $this->catalog->availables();
        $this->assertInstanceOf(Collection::class, $this->catalog->getCache());
        $this->catalog->clearCatalogsCache();
        $this->assertNotInstanceOf(Collection::class, $this->catalog->getCache());
    }
}
