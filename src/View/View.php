<?php

namespace Webwizo\Shortcodes\View;

use ArrayAccess;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View as IlluminateView;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;
use Illuminate\Contracts\View\Engine as EngineInterface;

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
     * @param \Webwizo\Shortcodes\Compilers\ShortcodeCompiler       $shortcode
     * @param \Illuminate\View\Factory|Factory                      $factory
     * @param \Illuminate\Contracts\View\Engine|EngineInterface     $engine
     * @param  string                                               $view
     * @param  string                                               $path
     * @param  array                                                $data
     */
    public function __construct(ShortcodeCompiler $shortcode, Factory $factory, EngineInterface $engine, $view, $path, $data = [])
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
        $enableForThisRender = !empty($this->data['__laravel_shortcodes_enable_for_render']);
        if ($enableForThisRender) {
            unset($this->data['__laravel_shortcodes_enable_for_render']);
        }

        $wasEnabled = $this->shortcode->isEnabled();
        if ($enableForThisRender && !$wasEnabled) {
            $this->shortcode->enable();
        }

        // Reset the accumulated view data only at the top-level render boundary.
        // Nested views (rendered while an outer view is still in progress) keep
        // merging so parent/layout data stays available to shortcode callbacks
        // (preserving #88). Once a top-level render has completed, its data is
        // released here instead of accumulating on the shared singleton compiler
        // for the lifetime of the request.
        if ($this->factory->doneRendering()) {
            $this->shortcode->clearViewData();
        }

        $this->shortcode->viewData($this->getData());
        // We will keep track of the amount of views being rendered so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        try {
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
        } finally {
            if ($enableForThisRender && !$wasEnabled) {
                $this->shortcode->disable();
            }
        }
    }
}
