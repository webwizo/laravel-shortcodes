<?php namespace Webwizo\Shortcodes\View;

use Illuminate\Events\Dispatcher;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as IlluminateViewFactory;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;

class Factory extends IlluminateViewFactory
{
    /**
     * Short code engine resolver
     *
     * @var ShortcodeCompiler
     */
    public $shortcode;

    /**
     * Create a new view factory instance.
     *
     * @param \Illuminate\View\Compilers\EngineResolver|EngineResolver $engines
     * @param  \Illuminate\View\ViewFinderInterface                    $finder
     * @param  \Illuminate\Events\Dispatcher                           $events
     * @param \Webwizo\Shortcodes\Compilers\ShortcodeCompiler          $shortcode
     */
    public function __construct(EngineResolver $engines, ViewFinderInterface $finder, Dispatcher $events, ShortcodeCompiler $shortcode)
    {
        parent::__construct($engines, $finder, $events);
        $this->shortcode = $shortcode;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     *
     * @return \Illuminate\Contracts\View\View|string|View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        $path = $this->finder->find(
            $view = $this->normalizeName($view)
        );

        // Next, we will create the view instance and call the view creator for the view
        // which can set any data, etc. Then we will return the view instance back to
        // the caller for rendering or performing other view manipulations on this.
        $data = array_merge($mergeData, $this->parseData($data));

        return tap(new View($this, $this->getEngineFromPath($path), $view, $path, $data, $this->shortcode), function ($view) {
            $this->callCreator($view);
        });
    }
}
