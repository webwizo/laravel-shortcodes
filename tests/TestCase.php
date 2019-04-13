<?php

namespace Webwizo\Shortcodes;

use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->shortcode = app()->make('shortcode');
    }

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
