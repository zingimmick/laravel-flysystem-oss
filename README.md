# Laravel Flysystem OSS

<p align="center">
<a href="https://github.com/zingimmick/laravel-flysystem-oss/actions"><img src="https://github.com/zingimmick/laravel-flysystem-oss/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://codecov.io/gh/zingimmick/laravel-flysystem-oss"><img src="https://codecov.io/gh/zingimmick/laravel-flysystem-oss/branch/master/graph/badge.svg" alt="Code Coverage" /></a>
<a href="https://packagist.org/packages/zing/laravel-flysystem-oss"><img src="https://poser.pugx.org/zing/laravel-flysystem-oss/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-flysystem-oss"><img src="https://poser.pugx.org/zing/laravel-flysystem-oss/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zing/laravel-flysystem-oss"><img src="https://poser.pugx.org/zing/laravel-flysystem-oss/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-flysystem-oss"><img src="https://poser.pugx.org/zing/laravel-flysystem-oss/license" alt="License"></a>
</p>

> **Requires**
> - **[PHP 8.0+](https://php.net/releases/)**
> - **[Laravel 9.0+](https://github.com/laravel/laravel)**

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

## License

Laravel Flysystem OSS is an open-sourced software licensed under the [MIT license](LICENSE).
