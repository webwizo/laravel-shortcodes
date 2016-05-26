<?php namespace Webwizo\Shortcodes;

use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{

    protected function getPackageProviders($app)
    {
        return ['Webwizo\Shortcodes\ShortcodesServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Shortcode' => 'Webwizo\Shortcodes\Facades\Shortcode'
        ];
    }
}
