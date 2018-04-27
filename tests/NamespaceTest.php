<?php

namespace Webwizo\Shortcodes;

use Orchestra\Testbench\TestCase;

class NamespaceTest extends TestCase
{
    public function testShouldLoadPreviousNamespaces()
    {
        $factory = app('view')->getFinder();

        app()->register('Webwizo\Shortcodes\ShortcodesServiceProvider');

        $freshFactory = app('view')->getFinder();

        $this->assertEquals($factory, $freshFactory);
    }
}
