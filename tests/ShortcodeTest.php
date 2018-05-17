<?php

namespace Webwizo\Shortcodes;

use Webwizo\Shortcodes\View\View;

class ShortcodeTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(\Webwizo\Shortcodes\Shortcode::class, $this->shortcode);
    }

    public function testRegistrationAndCompileShortcode()
    {
        $this->shortcode->register('b', function ($shortcode, $content) {
            return sprintf('<strong class="%s">%s</strong>', $shortcode->class, $content);
        });

        $compiled = $this->shortcode->compile('[b class="bold"]Bold Text[/b]');

        $this->assertEquals('<strong class="bold">Bold Text</strong>', $compiled);
    }

    public function testFactoryViewMake()
    {
        $factory = app('view');

        $factory->addNamespace('Test', __DIR__ . '/views');

        $this->assertTrue($factory->make('Test::test') instanceof View);
    }

    public function testStripShortcode()
    {
        $this->shortcode->register('shortcode', function ($shortcode, $content) {
            return 'foobar';
        });

        $compiled = $this->shortcode->strip('[shortcode]Text[/shortcode]');

        $this->assertEmpty($compiled);
    }
}
