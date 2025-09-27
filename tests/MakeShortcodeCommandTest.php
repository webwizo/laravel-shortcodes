<?php

namespace Webwizo\Shortcodes\Tests;

use Illuminate\Filesystem\Filesystem;
use Webwizo\Shortcodes\Console\MakeShortcodeCommand;
use Webwizo\Shortcodes\ShortcodesServiceProvider;
use Orchestra\Testbench\TestCase;

class MakeShortcodeCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ShortcodesServiceProvider::class];
    }

    public function test_it_creates_shortcode_class()
    {
        $filesystem = new Filesystem();
        $name = 'Demo';
        $className = 'DemoShortcode';
        $path = app_path("Shortcodes/{$className}.php");

        // Remove file if it exists
        if ($filesystem->exists($path)) {
            $filesystem->delete($path);
        }

        $this->artisan('make:shortcode', ['name' => $name])
            ->expectsOutput("Shortcode class created: {$path}")
            ->assertExitCode(0);

        $this->assertTrue($filesystem->exists($path));
        $contents = $filesystem->get($path);
        $this->assertStringContainsString('class DemoShortcode', $contents);
    }

    public function test_force_overwrites_existing_class()
    {
        $filesystem = new Filesystem();
        $name = 'Demo';
        $className = 'DemoShortcode';
        $path = app_path("Shortcodes/{$className}.php");

        // Create file with dummy content
        $filesystem->ensureDirectoryExists(dirname($path));
        $filesystem->put($path, '<?php // dummy');

        $this->artisan('make:shortcode', ['name' => $name, '--force' => true])
            ->expectsOutput("Shortcode class created: {$path}")
            ->assertExitCode(0);

        $contents = $filesystem->get($path);
        $this->assertStringContainsString('class DemoShortcode', $contents);
    }
}