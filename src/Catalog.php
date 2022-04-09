<?php

namespace Gam\LaravelSatCatalogs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Catalog
{
    private const DEFAULT_DRIVER = 'catalogs';

    /**
     * @var Collection|null table name cache
     */
    private ?Collection $cache;

    /**
     * @var string The database connection driver
     */
    private string $driver;

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
    public function availables(): \Illuminate\Support\Collection
    {
        if (empty($this->cache)) {
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
     * @param string $catalog
     * @param string $id
     * @param string $column
     * @return string
     */
    public function textOf(string $catalog, string $id, string $column = 'id'): string
    {
        if (! $this->exists($catalog)) {
            return '';
        }

        $model = $this->of($catalog)
            ->where($column, $id)
            ->first();

        return is_null($model)? '' : $model->texto;
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
     * @return bool
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
     * @return Collection|null
     */
    public function getCache(): ?Collection
    {
        // amm to test?
        return $this->cache;
    }

    /**
     * Return a QueryBuilder instance to execute sql queries
     * @param $catalog
     * @return \Illuminate\Database\Query\Builder
     */
    public function of($catalog): \Illuminate\Database\Query\Builder
    {
        return DB::connection($this->driver)->table($catalog);
    }

    private function guessSearchColumn(string $catalog): string
    {
        $parts = explode('_', $catalog);
        $singleName = end($parts);
        $transform = [
            'colonias' => 'colonia',
            'estados' => 'estado',
            'municipios' => 'municipio',
            'localidades' => 'localidad',
        ];

        return $transform[$singleName] ?? $singleName;
    }
}
