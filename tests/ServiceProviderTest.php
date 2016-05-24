<?php
use Webwizo\Shortcodes\Shortcode;

class ServiceProviderTest extends TestCase
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
        $this->assertInstanceOf(Shortcode::class, $shortcode);
    }

}