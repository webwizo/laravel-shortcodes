<?php namespace Webwizo\Shortcodes\Facades;

use Illuminate\Support\Facades\Facade;

class Shortcode extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shortcode';
    }
}
