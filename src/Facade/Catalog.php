<?php

namespace Gam\LaravelSatCatalogs\Facade;

/**
 * @method static bool exists(string $catalog)
 * @method static \Illuminate\Support\Collection availables()
 * @method static void begin()
 * @method static void commit()
 * @method static bool unprepared(string $sql)
 * @method static \Illuminate\Database\Query\Builder of(string $catalog)
 * @method static bool hasId(string $catalog)
 * @method static string textOf(string $catalog, string $id, string $column)
 */
class Catalog extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'catalog';
    }
}
