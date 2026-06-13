<?php

namespace Kinetics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakePipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kinetics:pipe
                            {name : The name of the pipe class (e.g. MyCustomPipe)}
                            {--path= : Custom output path relative to app/ (default: app/Pipes)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Kinetics custom pipe class';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->normalizeName($this->argument('name'));

        $outputPath = $this->option('path')
            ? app_path($this->option('path'))
            : app_path(config('kinetics.pipe_path', 'Pipes'));

        $filePath = $outputPath.DIRECTORY_SEPARATOR.$name.'.php';

        if ($this->files->exists($filePath)) {
            $this->components->error("Pipe [{$name}] already exists.");

            return self::FAILURE;
        }

        $this->makeDirectory($outputPath);

        $namespace = $this->resolveNamespace($outputPath);

        $stub = $this->buildStub($name, $namespace);

        $this->files->put($filePath, $stub);

        $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $filePath);

        $this->components->info("Pipe [{$relative}] created successfully.");

        return self::SUCCESS;
    }

    /**
     * Normalize class name: ensure it ends with "Pipe".
     */
    protected function normalizeName(string $name): string
    {
        $name = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
        $base = basename($name);

        if (! str_ends_with($base, 'Pipe')) {
            $base .= 'Pipe';
        }

        return $base;
    }

    /**
     * Resolve the PSR-4 namespace from the output path.
     */
    protected function resolveNamespace(string $outputPath): string
    {
        $appPath = realpath(app_path()) ?: app_path();
        $realOutput = realpath($outputPath) ?: $outputPath;

        $relative = ltrim(str_replace($appPath, '', $realOutput), DIRECTORY_SEPARATOR.'/');

        $rootNamespace = rtrim(app()->getNamespace(), '\\');

        if ($relative === '') {
            return $rootNamespace;
        }

        $suffix = str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

        return $rootNamespace.'\\'.$suffix;
    }

    /**
     * Build file content from stub.
     */
    protected function buildStub(string $class, string $namespace): string
    {
        $stub = $this->files->get($this->stubPath());

        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub,
        );
    }

    /**
     * Path to the stub file.
     */
    protected function stubPath(): string
    {
        $custom = base_path('stubs/kinetics/pipe.stub');

        if ($this->files->exists($custom)) {
            return $custom;
        }

        return __DIR__.'/../stubs/pipe.stub';
    }

    /**
     * Create directory if it does not exist.
     */
    protected function makeDirectory(string $path): void
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }
}
