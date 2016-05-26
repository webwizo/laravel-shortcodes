<?php namespace Webwizo\Shortcodes;

class ShortcodesServiceProviderTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    }

    public function testShortcodeClass()
    {
        $shortcode = app('shortcode');
        $this->assertInstanceOf('Webwizo\Shortcodes\Shortcode', $shortcode);
    }
}
