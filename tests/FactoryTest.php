<?php


namespace Webwizo\Shortcodes;


use Webwizo\Shortcodes\View\View;

class FactoryTest extends TestCase
{

    public function testMake()
    {
        $factory = app('view');

        $factory->addNamespace('Test',  __DIR__.'\views');

        $this->assertTrue($factory->make('Test::test') instanceof View);
    }
}
