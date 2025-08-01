<?php

namespace Gam\LaravelSatCatalogs\Facade;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool exists(string $catalog)
 * @method static Collection availables()
 * @method static void begin()
 * @method static void commit()
 * @method static bool unprepared(string $sql)
 * @method static Builder of(string $catalog)
 * @method static bool hasId(string $catalog)
 * @method static string textOf(string $catalog, string $id, string $column = 'id', string $default = '')
 */
class Catalog extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'catalog';
    }
}
