<?php namespace Webwizo\ShortCodes;

class TestCase extends \TestCase
{

    public function testShortcodeClass()
    {
        $shortcode = app('shortcode');
        $this->assertInstanceOf(Shortcode::class, $shortcode);
    }

}