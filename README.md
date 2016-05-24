# Laravel-Shortcodes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

WordPress like shortcodes for Laravel 5.2

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

Via Composer

``` bash
$ composer require "webwizo/laravel-shortcodes:1.0.0"
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

## View compiling

By default shortcode compiling is set to false inside the config. 

### withShortcodes()

To enable the view compiling features:

```php
return view('view')->withShortcodes();
```

This will enable shortcode rendering for that view only.

### Config

Enabeling the shortcodes through config `shortcodes::enabled` will enable shortcoding rendering for all views.

### Enable through class

```php
Shortcode::enable();
```

### Disable through class

```php
Shortcode::disable();
```

### Disabeling some views from shortcode compiling

With the config set to true, you can disable the compiling per view.

```php
return view('view')->withoutShortcodes();
```

## Default compiling

To use default compiling:

```php
Shortcode::compile($contents);
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

Shortcodes can be registered like Laravel macro's with a callback:

```php
Shortcode::register('b', function($shortcode, $content, $compiler, $name)
{
  return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
});
```

### Default class

```php
namespace App\Shortcodes;

class BoldShortcode {

  public function register($shortcode, $content, $compiler, $name)
  {
    return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
  }
  
}

Shortcode::register('b', \App\Shortcodes\BoldShortcode::class);
```

### Class with custom method

```php
namespace App\Shortcodes;

class BoldShortcode {

  public function custom($shortcode, $content, $compiler, $name)
  {
    return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
  }
  
}

Shortcode::register('b', 'App\Shortcodes\BoldShortcode@custom');

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

- [Asif iqbal][link-author]
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
