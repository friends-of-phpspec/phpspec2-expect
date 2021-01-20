[![Latest Stable Version](https://img.shields.io/packagist/v/friends-of-phpspec/phpspec-expect.svg?style=flat-square)](https://packagist.org/packages/friends-of-phpspec/phpspec-expect)
[![GitHub stars](https://img.shields.io/github/stars/friends-of-phpspec/phpspec-expect.svg?style=flat-square)](https://packagist.org/packages/friends-of-phpspec/phpspec-expect)
[![Total Downloads](https://img.shields.io/packagist/dt/friends-of-phpspec/phpspec-expect.svg?style=flat-square)](https://packagist.org/packages/friends-of-phpspec/phpspec-expect)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/friends-of-phpspec/phpspec-expect/Continuous%20Integration?style=flat-square)](https://github.com/friends-of-phpspec/phpspec-expect/actions)
[![License](https://img.shields.io/packagist/l/friends-of-phpspec/phpspec-expect.svg?style=flat-square)](https://packagist.org/packages/friends-of-phpspec/phpspec-expect)

# phpspec-expect

## Install

Install this package as a development dependency in your project:

    $ composer require --dev friends-of-phpspec/phpspec-expect

## Usage

Inside some example:

```php
expect(file_exists('dummy.txt'))->toBe(true);
```

## Compatibility

Version `2.x` supports PhpSpec 3 and PHP 5.6.

Version `3.0.x` requires PhpSpec 4, and therefore requires PHP 7.

Version `3.1.x` requires PhpSpec 5 and PHP 7.

## Authors

Copyright (c) 2017-2020 BossaConsulting (https://github.com/BossaConsulting/phpspec2-expect).

## License

Licensed under [MIT License](LICENSE).
