<?php

namespace Gam\LaravelSatCatalogs\Tests\Unit;

use Gam\LaravelSatCatalogs\Catalog;

class CatalogTest extends \Gam\LaravelSatCatalogs\Tests\TestCase
{
    /**
     * @var Catalog
     */
    private $catalog;

    public function setUp(): void
    {
        parent::setUp();
        $this->catalog = new Catalog();
        // create the database if does not exist
        if (! file_exists(self::DB_PATH)) {
            $this->artisan('catalogs:update', ['--path' => __DIR__ . '/../_files'])
                ->execute();
        }
    }

    /**
     * @test
     */
    public function exists(): void
    {
        self::assertTrue($this->catalog->exists('cfdi_40_productos_servicios'));
    }

    /**
     * @test
     */
    public function hasId(): void
    {
        self::assertTrue($this->catalog->hasId('cfdi_40_productos_servicios'));
    }

    /**
     * @test
     */
    public function doesNotHaveId(): void
    {
        self::assertFalse($this->catalog->hasId('cfdi_40_colonias'));
    }

    /**
     * @test Retrieve text column with given ID
     */
    public function textOfWithId(): void
    {
        self::assertEquals('Enero', $this->catalog->textOf('cfdi_40_meses', '01'));
        self::assertEquals(
            'Aguascalientes',
            $this->catalog->textOf('cfdi_40_estados', 'AGU', 'estado')
        );
        self::assertEquals(
            'N/A',
            $this->catalog->textOf('cfdi_40_estados', 'NO_EXISTE', 'estado', 'N/A')
        );
    }

    /**
     * @test
     */
    public function clearCache(): void
    {
        $this->catalog->availables();
        self::assertNotNull($this->catalog->getCache());
        $this->catalog->clearCatalogsCache();
        self::assertNull($this->catalog->getCache());
    }
}
