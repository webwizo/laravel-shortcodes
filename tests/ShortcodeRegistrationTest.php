<?php namespace Webwizo\Shortcodes;

class ShortcodeRegistrationTest extends TestCase
{

    protected $string = '[b class="bold"]Bold Text[/b]';

    protected $compiled = '<strong class="bold">Bold Text</strong>';

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->shortcode = app()->make('shortcode');
    }

    public function testInstance()
    {
        $this->assertInstanceOf(\Webwizo\Shortcodes\Shortcode::class, $this->shortcode);
    }

    public function testRegistrationAndCompileShortcode()
    {
        $this->shortcode->register('b', function ($shortcode, $content) {
            return sprintf('<strong class="%s">%s</strong>', $shortcode->class, $content);
        });

        $compiled = $this->shortcode->compile($this->string);

        $this->assertEquals($this->compiled, $compiled);
    }
}
