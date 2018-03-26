<?php namespace Webwizo\Shortcodes;

class ShortcodesServiceProviderTest extends TestCase
{
    public function testShortcodeClass()
    {
        $shortcode = app('shortcode');
        $this->assertInstanceOf('Webwizo\Shortcodes\Shortcode', $shortcode);
    }
}
