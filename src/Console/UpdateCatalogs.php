<?php

namespace Gam\LaravelSatCatalogs\Console;

use Gam\LaravelSatCatalogs\Facade\Catalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class UpdateCatalogs extends Command
{
    /**
     * The default catalogs source.
     */
    private const DEFAULT_ZIP_SOURCE = 'https://github.com/phpcfdi/resources-sat-catalogs/archive/master.zip';

    private readonly string $zipSource;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalogs:update
                        {--path=: define root path to store the files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update SAT catalogs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->zipSource = config('catalogs.zip_source') ?? self::DEFAULT_ZIP_SOURCE;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting catalog update process...');

        try {
            $this->validateConfig();
        } catch (\UnexpectedValueException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $rootPath = $this->getRootPath();

        $zipPath = build_path([$rootPath, 'catalogs.zip']);

        if (! $this->downloadZip($zipPath)) {
            return self::FAILURE;
        }

        $databasePath = $this->getCatalogsDriverPath();

        if (! $this->prepareDatabaseFile($databasePath)) {
            return self::FAILURE;
        }

        $zipArchive = new ZipArchive();
        if (! $this->openZipArchive($zipArchive, $zipPath)) {
            return self::FAILURE;
        }

        if (! $this->extractZipFiles($zipArchive, $rootPath)) {
            $zipArchive->close();
            return self::FAILURE;
        }

        $zipArchive->close();

        if (! $this->createSchemas($rootPath)) {
            return self::FAILURE;
        }

        if (! $this->insertData($rootPath)) {
            return self::FAILURE;
        }

        $this->showSummary($rootPath);

        $this->cleanUp($rootPath, $zipPath);

        $this->info('Catalog update completed successfully.');

        return self::SUCCESS;
    }

    private function getRootPath(): string
    {
        return $this->option('path') ?: storage_path('app/catalogs');
    }

    private function downloadZip(string $zipPath): bool
    {
        $this->info('Downloading ' . $this->zipSource . '...');

        $response = Http::get($this->zipSource);

        if (! $response->successful()) {
            $this->error(sprintf(
                'Unable to download %s. HTTP Code: %s',
                $this->zipSource,
                $response->status()
            ));
            return false;
        }

        File::put($zipPath, $response->body());

        return true;
    }

    private function getCatalogsDriverPath(): string
    {
        $catalogDriverName = Config::string('catalogs.driver');

        return Config::string(
            "database.connections.{$catalogDriverName}.database"
        );
    }

    private function prepareDatabaseFile(string $databasePath): bool
    {
        if (File::exists($databasePath)) {
            File::delete($databasePath);
        }
        // Create empty file
        File::put($databasePath, '');

        return File::exists($databasePath);
    }

    private function openZipArchive(ZipArchive $zipArchive, string $zipPath): bool
    {
        $opened = $zipArchive->open($zipPath);
        if (true !== $opened) {
            $this->error("Unable to open ZIP archive: {$zipArchive->getStatusString()}");
            return false;
        }
        return true;
    }

    private function extractZipFiles(ZipArchive $zipArchive, string $destination): bool
    {
        $this->info('Extracting SQL files...');
        $extracted = $zipArchive->extractTo($destination);
        if (! $extracted) {
            $this->error('Unable to extract the files.');
            return false;
        }
        return true;
    }

    private function createSchemas(string $rootPath): bool
    {
        $schemasPath = build_path([
            $rootPath,
            'resources-sat-catalogs-master',
            'database',
            'schemas',
        ]);

        $schemas = File::allFiles($schemasPath);

        if (empty($schemas)) {
            $this->error('No schema files found.');
            return false;
        }

        $this->info('Creating schemas...');
        foreach ($schemas as $schema) {
            Catalog::unprepared(File::get($schema));
        }

        return true;
    }

    private function insertData(string $rootPath): bool
    {
        $dataPath = build_path([$rootPath, 'resources-sat-catalogs-master', 'database', 'data']);
        $catalogs = File::allFiles($dataPath);

        if (empty($catalogs)) {
            $this->error('No catalog data files found.');
            return false;
        }

        $this->info('Inserting data...');
        foreach ($catalogs as $catalog) {
            Catalog::unprepared(File::get($catalog));
        }

        return true;
    }

    private function showSummary(string $rootPath): void
    {
        $schemasPath = build_path([$rootPath, 'resources-sat-catalogs-master', 'database', 'schemas']);
        $dataPath = build_path([$rootPath, 'resources-sat-catalogs-master', 'database', 'data']);
        $schemasCount = count(File::allFiles($schemasPath));
        $catalogsCount = count(File::allFiles($dataPath));

        $this->table(['Schemas', 'Data'], [
            [$schemasCount, $catalogsCount],
        ]);
    }

    private function cleanUp(string $rootPath, string $zipPath): void
    {
        $tempDir = build_path([$rootPath, 'resources-sat-catalogs-master']);
        File::cleanDirectory($tempDir);
        File::deleteDirectory($tempDir);
        File::delete($zipPath);
    }

    private function validateConfig(): void
    {
        $catalogDriverName = Config::string('catalogs.driver');

        if (null === Config::get("database.connections.$catalogDriverName")) {
            throw new \UnexpectedValueException('The driver path is not set.');
        }
    }
}
