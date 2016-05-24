<?php namespace Webwizo\ShortCodes;

use TestCase;

class TestServiceProvider extends TestCase
{
    public function testShortcodeClass()
    {
        $shortcode = app('shortcode');
        $this->assertInstanceOf(Shortcode::class, $shortcode);
    }

}