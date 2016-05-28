<?php namespace Webwizo\Shortcodes;

use Illuminate\Support\ServiceProvider;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;
use Webwizo\Shortcodes\View\Factory;

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->enableCompiler();
    }

    /**
     * Enable the compiler
     */
    public function enableCompiler()
    {
        // Check if the compiler is auto enabled
        $state = $this->app['config']->get('laravel-shortcodes::enabled', false);
        // enable when needed
        if ($state) {
            $this->app['shortcode.compiler']->enable();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerShortcodeCompiler();
        $this->registerShortcode();
        $this->registerView();
    }

    /**
     * Register short code compiler
     */
    public function registerShortcodeCompiler()
    {
        $this->app->singleton('shortcode.compiler', function ($app) {
            return new ShortcodeCompiler();
        });
    }

    /**
     * Register the shortcode
     */
    public function registerShortcode()
    {
        $this->app->singleton('shortcode', function ($app) {
            return new Shortcode($app['shortcode.compiler']);
        });
    }

    /**
     * Register Laravel view
     */
    public function registerView()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];
            $finder = $app['view.finder'];
            $env = new Factory($resolver, $finder, $app['events'], $app['shortcode.compiler']);
            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $env->setContainer($app);
            $env->share('app', $app);

            return $env;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'shortcode',
            'shortcode.compiler',
            'view'
        ];
    }
}
