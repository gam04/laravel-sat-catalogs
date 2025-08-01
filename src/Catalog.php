<?php

namespace Gam\LaravelSatCatalogs;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Catalog
{
    private const DEFAULT_DRIVER = 'catalogs';

    /**
     * @var Collection|null table name cache
     */
    private ?Collection $cache = null;

    /**
     * @var string The database connection driver
     */
    private readonly string $driver;

    public function __construct()
    {
        $this->driver = config('catalogs.driver') ?? self::DEFAULT_DRIVER;
    }

    public function exists(string $catalog): bool
    {
        return $this->availables()->contains($catalog);
    }

    /**
     * Return the table names in sqlite db
     */
    public function availables(): Collection
    {
        if (! isset($this->cache) || ! $this->cache instanceof Collection) {
            $this->cache = DB::connection($this->driver)
                ->table('sqlite_master')
                ->where('type', 'table')
                ->get()
                ->pluck('name');
        }

        return $this->cache;
    }

    public function hasId(string $catalog): bool
    {
        return DB::connection($this->driver)
            ->getSchemaBuilder()
            ->hasColumn($catalog, 'id');
    }

    /**
     * Get the text of the given catalog.
     */
    public function textOf(string $catalog, string $id, string $column = 'id', string $default = ''): string
    {
        if (! $this->exists($catalog)) {
            return $default;
        }

        $model = $this->of($catalog)
            ->where($column, $id)
            ->first();

        return is_null($model)? $default : $model->texto;
    }

    /**
     * Begin a transaction
     */
    public function begin(): void
    {
        DB::connection($this->driver)->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit(): void
    {
        DB::connection($this->driver)->commit();
    }

    /**
     * Execute a raw sql query
     * @param $sql
     */
    public function unprepared($sql): bool
    {
        return DB::connection($this->driver)->unprepared($sql);
    }

    public function clearCatalogsCache(): void
    {
        $this->cache = null;
    }

    /**
     * @internal
     */
    public function getCache(): ?Collection
    {
        // amm to test?
        return $this->cache;
    }

    /**
     * Return a QueryBuilder instance to execute sql queries
     * @param $catalog
     */
    public function of($catalog): Builder
    {
        return DB::connection($this->driver)->table($catalog);
    }
}
