<?php

namespace Gam\LaravelSatCatalogs;

use Illuminate\Support\Facades\DB;

class Catalog
{
    private const DEFAULT_DRIVER = 'catalogs';

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
        return DB::connection($this->driver)
            ->table('sqlite_master')
            ->where('type', 'table')
            ->get()
            ->pluck('name');
    }

    public function hasId(string $catalog): bool
    {
        return DB::connection($this->driver)
            ->getSchemaBuilder()
            ->hasColumn($catalog, 'id');
    }

    /**
     * Get the text of the given catalog.
     * If 'id' column doesnt exists, the method will try to guess the search column
     * @param string $catalog
     * @param string $id
     * @return string
     */
    public function textOf(string $catalog, string $id): string
    {
        if (! $this->exists($catalog)) {
            return '';
        }

        if ($this->hasId($catalog)) {
            return $this->of($catalog)
                ->find($id)
                ->texto;
        }

        return $this->of($catalog)
            ->where($this->guessSearchColumn($catalog), $id)
            ->first()
            ->texto;
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
