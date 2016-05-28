<?php namespace Webwizo\Shortcodes\View;

use ArrayAccess;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View as IlluminateView;
use Illuminate\View\Engines\EngineInterface;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;

class View extends IlluminateView implements ArrayAccess, Renderable
{

    /**
     * Short code engine resolver
     *
     * @var \Webwizo\Shortcodes\Compilers\ShortcodeCompiler
     */
    public $shortcode;

    /**
     * Create a new view instance.
     *
     * @param \Illuminate\View\Factory|Factory                           $factory
     * @param \Illuminate\View\Compilers\EngineInterface|EngineInterface $engine
     * @param  string                                                    $view
     * @param  string                                                    $path
     * @param  array                                                     $data
     * @param \Webwizo\Shortcodes\Compilers\ShortcodeCompiler            $shortcode
     */
    public function __construct(Factory $factory, EngineInterface $engine, $view, $path, $data = [], ShortcodeCompiler $shortcode)
    {
        parent::__construct($factory, $engine, $view, $path, $data);
        $this->shortcode = $shortcode;
    }

    /**
     * Enable the shortcodes
     */
    public function withShortcodes()
    {
        $this->shortcode->enable();

        return $this;
    }

    /**
     * Disable the shortcodes
     */
    public function withoutShortcodes()
    {
        $this->shortcode->disable();

        return $this;
    }

    public function withStripShortcodes()
    {
        $this->shortcode->setStrip(true);

        return $this;
    }

    /**
     * Get the contents of the view instance.
     *
     * @return string
     */
    protected function renderContents()
    {
        // We will keep track of the amount of views being rendered so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        $this->factory->incrementRender();
        $this->factory->callComposer($this);
        $contents = $this->getContents();
        if ($this->shortcode->getStrip()) {
            // strip content without shortcodes
            $contents = $this->shortcode->strip($contents);
        } else {
            // compile the shortcodes
            $contents = $this->shortcode->compile($contents);
        }
        // Once we've finished rendering the view, we'll decrement the render count
        // so that each sections get flushed out next time a view is created and
        // no old sections are staying around in the memory of an environment.
        $this->factory->decrementRender();

        return $contents;
    }
}
