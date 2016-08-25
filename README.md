# Laravel-Shortcodes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI](https://styleci.io/repos/59507292/shield)](https://styleci.io/repos/59507292)

WordPress like shortcodes for Laravel 5.3

```php
[b class="bold"]Bold text[/b]

[tabs]
  [tab]Tab 1[/tab]
  [tab]Tab 2[/tab]
[/tabs]

[user id="1" display="name"]
```

If you are looking for Laravel 5.2, https://github.com/webwizo/laravel-shortcodes/tree/v1.0.4

If you are looking for Laravel 4.2, see: https://github.com/patrickbrouwers/Laravel-Shortcodes

## Install

Via Composer

``` bash
$ composer require "webwizo/laravel-shortcodes:1.0.*"
```

After updating composer, add the ServiceProvider to the providers array in `config/app.php`

## Usage

```php
Webwizo\Shortcodes\ShortcodesServiceProvider::class,
```

You can use the facade for shorter code. Add this to your aliases:

```php
'Shortcode' => Webwizo\Shortcodes\Facades\Shortcode::class,
```

The class is bound to the ioC as `shortcode`

```php
$shortcode = app('shortcode');
```

# Usage

### withShortcodes()

To enable the view compiling features:

```php
return view('view')->withShortcodes();
```

This will enable shortcode rendering for that view only.

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

## Registering new shortcodes

Create a new ServiceProvider where you can register all the shortcodes.

``` bash
php artisan make:provider ShortcodesServiceProvider
```

After defining shortcodes, add the ServiceProvider to the providers array in `config/app.php`

## Usage

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

  public function register($shortcode, $content, $compiler, $name)
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

  public function custom($shortcode, $content, $compiler, $name)
  {
    return sprintf('<i class="%s">%s</i>', $shortcode->class, $content);
  }
  
}
```

### Register helpers

If you only want to show the html attribute when the attribute is provided in the shortcode, you can use `$shortcode->get($attributeKey, $fallbackValue = null)`

```php
class BoldShortcode {

  public function register($shortcode, $content, $compiler, $name)
  {
    return '<strong '. $shortcode->get('class', 'default') .'>' . $content . '</strong>';
  }
  
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email webwizo@gmail.com instead of using the issue tracker.

## Credits

- [Asif Iqbal][link-author]
- [All Contributors][link-contributors]

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
