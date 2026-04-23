# Laravel-Shortcodes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

## WordPress-like shortcodes for **Laravel 11, 12, and 13** (PHP 8.2+).
![Laravel Shortcodes](https://github.com/webwizo/laravel-shortcodes/blob/master/preview.png?raw=true)

**Documentation last updated:** 2026-04-22

```php
[b class="bold"]Bold text[/b]

[tabs]
  [tab]Tab 1[/tab]
  [tab]Tab 2[/tab]
[/tabs]

[user id="1" display="name"]
```

If you are looking for Laravel 4.2, see: https://github.com/patrickbrouwers/Laravel-Shortcodes

## Install

Via Composer:

```bash
composer require webwizo/laravel-shortcodes
```

Laravel 11+ discovers the package automatically. On older setups, register `Webwizo\Shortcodes\ShortcodesServiceProvider` in `config/app.php` (or `bootstrap/providers.php` where applicable).

### Service provider (manual registration)

```php
Webwizo\Shortcodes\ShortcodesServiceProvider::class,
```

You can use the facade for shorter code. Add this to your aliases (optional):

```php
'Shortcode' => Webwizo\Shortcodes\Facades\Shortcode::class,
```

The class is bound to the ioC as `shortcode`

```php
$shortcode = app('shortcode');
```

## Usage

### withShortcodes()

To enable the view compiling features:

```php
return view('view')->withShortcodes();
```

This will enable shortcode rendering for that view only.

### withShortcodes() in Mailables

You can also enable shortcode compilation for a mailable view:

Available since: `v1.0.31`

```php
use Illuminate\Mail\Mailable;

class NewsletterMail extends Mailable
{
    public function build()
    {
        return $this
            ->subject('Weekly newsletter')
            ->view('emails.newsletter', [
                'content' => 'Mail content',
            ])
            ->withShortcodes();
    }
}
```

`emails/newsletter.blade.php`

```blade
[b class="mail"]{{ $content }}[/b]
```

The shortcode tags in that mailable view will be compiled during rendering.

### Enable through class

```php
Shortcode::enable();
```

### Disable through class

```php
Shortcode::disable();
```

### Disabling some views from shortcode compiling

With the config set to true, you can disable the compiling per view.

```php
return view('view')->withoutShortcodes();
```

## Default compiling

To use default compiling:

```php
Shortcode::compile($contents);
```

### Strip shortcodes from rendered view.

```php
return view('view')->withStripShortcodes();
```

## Strip shortcode through class

```php
Shortcode::strip($contents);
```

## Shortcode syntax and parser

The compiler scans templates left to right. Behaviour relevant to authoring tags:

| Topic | Behaviour |
|--------|------------|
| **Attributes** | Quoted values may contain spaces. Keys may include hyphens (e.g. `data-size="large"`). Parsing follows the same general rules as WordPress shortcode attributes. |
| **Void / self-closing** | A tag such as `[alert type="info"]` with **no** matching `[/alert]` is treated as self-closing (no inner body). Use explicit `[/tag]` when you need wrapping content. |
| **Nested same-name tags** | Nested tags with the **same** name (e.g. `[div]…[div]…[/div]…[/div]`) are matched using balanced opening/closing pairs, innermost structure preserved. |
| **Unknown `[` text** | Text in square brackets that does **not** start with a registered shortcode name is left unchanged (so prose like `[I agree …]` is not eaten). |
| **Escaping** | Use doubled brackets to output a literal shortcode: `[[b]]` → `[b]`. |

PHPUnit regression tests for these cases live under `tests/GitHubIssue*.php`.

## Registering new shortcodes

Create a new ServiceProvider where you can register all the shortcodes.

```bash
php artisan make:provider ShortcodesServiceProvider
```

After defining shortcodes, add the ServiceProvider to the providers array in `config/app.php` (or `bootstrap/providers.php`).

### Example: register your shortcodes provider

```php
App\Providers\ShortcodesServiceProvider::class,
```

### Callback

Shortcodes can be registered within ShortcodesServiceProvider with a callback:

```bash
php artisan make:provider ShortcodesServiceProvider
```

ShortcodesServiceProvider.php Class File

```php
<?php namespace App\Providers;

use App\Shortcodes\BoldShortcode;
use Illuminate\Support\ServiceProvider;
use Shortcode;

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Shortcode::register('b', BoldShortcode::class);
        Shortcode::register('i', 'App\Shortcodes\ItalicShortcode@custom');
    }
}
```

### Default class for BoldShortcode

You can store each shortcode within their class `app/Shortcodes/BoldShortcode.php`

```php
namespace App\Shortcodes;

class BoldShortcode {

  public function register($shortcode, $content, $compiler, $name, $viewData)
  {
    return sprintf('<strong class="%s">%s</strong>', $shortcode->class, $content);
  }

}
```

### Class with custom method

You can store each shortcode within their class `app/Shortcodes/ItalicShortcode.php`

```php
namespace App\Shortcodes;

class ItalicShortcode {

  public function custom($shortcode, $content, $compiler, $name, $viewData)
  {
    return sprintf('<i class="%s">%s</i>', $shortcode->class, $content);
  }

}
```

### Register helpers

If you only want to show the html attribute when the attribute is provided in the shortcode, you can use `$shortcode->get($attributeKey, $fallbackValue = null)`

```php
class BoldShortcode {

  public function register($shortcode, $content, $compiler, $name, $viewData)
  {
    return '<strong '. $shortcode->get('class', 'default') .'>' . $content . '</strong>';
  }

}
```

## Shortcode Artisan Generator Command

This package provides an Artisan command to quickly generate shortcode classes:

```php
php artisan make:shortcode YourShortcodeName
```

-   By default, this creates a new class in `app/Shortcodes/YourShortcodeNameShortcode.php`.
-   If the file already exists, use the `--force` option to overwrite:

```bash
php artisan make:shortcode YourShortcodeName --force
```

### Customizing the Stub

You can publish the stub file to customize the generated class:

```bash
php artisan vendor:publish --tag=shortcode-stubs
```

This will copy the stub to `resources/stubs/shortcode.stub` in your Laravel app. Edit this file to change the template for new shortcode classes.

## Testing

From the package root (after `composer install`):

```bash
composer test
# or
vendor/bin/phpunit
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email webwizo@gmail.com instead of using the issue tracker.

## Credits

-   [Asif Iqbal][link-author]
-   [All Contributors][link-contributors]

## Support me

<a href="https://www.buymeacoffee.com/webwizo" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/webwizo/laravel-shortcodes.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/webwizo/laravel-shortcodes/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/webwizo/laravel-shortcodes.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/webwizo/laravel-shortcodes.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/webwizo/laravel-shortcodes.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/webwizo/laravel-shortcodes
[link-travis]: https://travis-ci.org/webwizo/laravel-shortcodes
[link-scrutinizer]: https://scrutinizer-ci.com/g/webwizo/laravel-shortcodes/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/webwizo/laravel-shortcodes
[link-downloads]: https://packagist.org/packages/webwizo/laravel-shortcodes
[link-author]: https://github.com/webwizo
[link-contributors]: ../../contributors
