<?php

namespace Gam\LaravelSatCatalogs\Console;

use Gam\LaravelSatCatalogs\Facade\Catalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class UpdateCatalogs extends Command
{
    /**
     * The default catalogs source.
     */
    private const DEFAULT_ZIP_SOURCE = 'https://github.com/phpcfdi/resources-sat-catalogs/archive/master.zip';

    /**
     * @var string
     */
    private string $zipSource;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalogs:update {--path : the root path} ';

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
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Downloading ' . $this->zipSource . '...');
        $response = Http::get($this->zipSource);
        if (! $response->successful()) {
            $this->error(
                sprintf(
                    'Unable to download %s. Http Code: %s',
                    $this->zipSource,
                    $response->status()
                )
            );
            return 1;
        }

        // where's zip goes
        $rootPath = $this->hasOption('path')
            ? $this->option('path')
            : __DIR__ . '/../Resource';

        $zipPath = build_path([$rootPath, 'catalogs.zip']);
        $databasePath = build_path([$rootPath, 'catalogs.sqlite3']);

        File::put($zipPath, $response->body());
        // Storage::put(build_path([$destinationPath, 'catalogs.zip']), $response->body());
        $zip = new ZipArchive();
        //$opened = $zip->open(storage_path(build_path(['app', $destinationPath, 'catalogs.zip'])));
        $opened = $zip->open($zipPath);
        if (true !== $opened) {
            $this->error("Unable to extract the sql files: {$zip->getStatusString()}");
            return 1;
        }

        // if a previous sqlite db exists, delete it & create a new one
        if (File::exists($databasePath)) {
            File::delete($databasePath);
        }

        File::put($databasePath, '');

        // try to extract the files
        $this->info('Extracting sql files...');
        $res = $zip->extractTo($rootPath);
        if (false === $res) {
            $this->error('Unable to extract the files');
            $zip->close();
            return 1;
        }

        // read all schema files and run them
        $schemas = File::allFiles(build_path([$rootPath, 'resources-sat-catalogs-master', 'database', 'schemas']));

        $this->info('Creating schemas...');
        foreach ($schemas as $schema) {
            Catalog::unprepared(File::get($schema));
        }

        // read all data file and run them
        $catalogs = File::allFiles(build_path([$rootPath, 'resources-sat-catalogs-master', 'database', 'data']));

        $this->info('Inserting data...');
        foreach ($catalogs as $catalog) {
            Catalog::unprepared(File::get($catalog));
        }
        $this->table(['Schemas', 'Data'], [
            [count($schemas), count($catalogs)],
        ]);

        // clean
        File::cleanDirectory(build_path([$rootPath, 'resources-sat-catalogs-master']));
        File::deleteDirectory(build_path([$rootPath, 'resources-sat-catalogs-master']));
        File::delete($zipPath);

        return 0;
    }
}
