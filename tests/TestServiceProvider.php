<?php
use Webwizo\Shortcodes\Shortcode;

class TestServiceProvider extends TestCase
{
    public function testShortcodeClass()
    {
        $shortcode = app('shortcode');
        $this->assertInstanceOf(Shortcode::class, $shortcode);
    }

}