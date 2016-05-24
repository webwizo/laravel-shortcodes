<?php

use Orchestra\Testbench\TestCase as TestBenchTestCase;

use Webwizo\Shortcodes\Shortcode;

class TestCase extends TestBenchTestCase
{
    public function testShortcodeClass()
    {
        $shortcode = app('shortcode');
        $this->assertInstanceOf(Shortcode::class, $shortcode);
    }
}