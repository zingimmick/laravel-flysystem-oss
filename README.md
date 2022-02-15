# Laravel Flysystem OSS

OSS storage filesystem for Laravel based on [zing/flysystem-oss](https://github.com/zingimmick/flysystem-oss)

[![Build Status](https://github.com/zingimmick/laravel-flysystem-oss/workflows/tests/badge.svg)](https://github.com/zingimmick/laravel-flysystem-oss/actions)
[![Code Coverage](https://codecov.io/gh/zingimmick/laravel-flysystem-oss/branch/master/graph/badge.svg)](https://codecov.io/gh/zingimmick/laravel-flysystem-oss)
[![Latest Stable Version](https://poser.pugx.org/zing/laravel-flysystem-oss/v/stable.svg)](https://packagist.org/packages/zing/laravel-flysystem-oss)
[![Total Downloads](https://poser.pugx.org/zing/laravel-flysystem-oss/downloads)](https://packagist.org/packages/zing/laravel-flysystem-oss)
[![Latest Unstable Version](https://poser.pugx.org/zing/laravel-flysystem-oss/v/unstable.svg)](https://packagist.org/packages/zing/laravel-flysystem-oss)
[![License](https://poser.pugx.org/zing/laravel-flysystem-oss/license)](https://packagist.org/packages/zing/laravel-flysystem-oss)

> **Requires**
> - **[PHP 8.0+](https://php.net/releases/)**
> - **[Laravel 9.0+](https://laravel.com/docs/releases)**

## Version Information

| Version | Illuminate | PHP Version | Status                  |
|:--------|:-----------|:------------|:------------------------|
| 2.x     | 9.x        | >= 8.0      | Active support :rocket: |
| 1.x     | 6.x - 8.x  | >= 7.2      | Active support          |

Require Laravel Flysystem OSS using [Composer](https://getcomposer.org):

```bash
composer require zing/laravel-flysystem-oss
```

## Configuration

```php
return [
    // ...
    'disks' => [
        // ...
        'oss' => [
            'driver' => 'oss',
            'root' => '',
            'key' => env('OSS_KEY'),
            'secret' => env('OSS_SECRET'),
            'bucket' => env('OSS_BUCKET'),
            'endpoint' => env('OSS_ENDPOINT'),
            'bucket_endpoint' => env('OSS_BUCKET_ENDPOINT', false),
        ],
    ]
];
```

## Environment

```dotenv
OSS_KEY=
OSS_SECRET=
OSS_BUCKET=
OSS_ENDPOINT=
OSS_BUCKET_ENDPOINT=false
```

## License

Laravel Flysystem OSS is an open-sourced software licensed under the [MIT license](LICENSE).
