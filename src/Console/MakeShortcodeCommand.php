<?php

namespace Webwizo\Shortcodes\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeShortcodeCommand extends Command
{
    protected $signature = 'make:shortcode {name} {--force}';
    protected $description = 'Create a new shortcode class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $className = ucfirst($name) . 'Shortcode';
        $path = $this->getShortcodePath($className);
        $force = $this->option('force');

        if ($this->files->exists($path) && !$force) {
            $this->error("Shortcode class already exists: {$className}. Use --force to overwrite.");
            return 1;
        }

        $stub = $this->getStub($className);
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);

        $this->info("Shortcode class created: {$path}");
        return 0;
    }

    protected function getShortcodePath($className)
    {
        // Use Laravel's app_path helper if available, fallback to relative path
        if (function_exists('app_path')) {
            return app_path("Shortcodes/{$className}.php");
        }
        return getcwd() . "/app/Shortcodes/{$className}.php";
    }

    protected function getStub($className)
    {
        $stubPath = base_path('resources/stubs/shortcode.stub');
        if (!$this->files->exists($stubPath)) {
            // fallback to package stub if not published
            $stubPath = __DIR__ . '/../../resources/stubs/shortcode.stub';
        }
        $stub = $this->files->get($stubPath);
        return str_replace('{{class}}', $className, $stub);
    }
}
