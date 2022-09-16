<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Zing\LaravelFlysystem\Oss\OssServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param mixed $app
     *
     * @return array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [OssServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        Config::set('filesystems.disks.oss', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'your-endpoint',
        ]);
        Config::set('filesystems.disks.oss-url', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'your-endpoint',
            'url' => 'https://test-url',
        ]);
        Config::set('filesystems.disks.oss-temporary-url', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'your-endpoint',
            'temporary_url' => 'https://test-temporary-url',
        ]);
        Config::set('filesystems.disks.oss-bucket-endpoint', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'https://your-endpoint',
            'bucket_endpoint' => true,
        ]);
        Config::set('filesystems.disks.oss-is-cname', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'https://your-endpoint',
            'is_cname' => true,
        ]);        Config::set('filesystems.disks.oss-read-only', [
        'driver' => 'oss',
        'key' => 'aW52YWxpZC1rZXk=',
        'secret' => 'aW52YWxpZC1zZWNyZXQ=',
        'bucket' => 'your-bucket',
        'endpoint' => 'https://your-endpoint',
        'read-only' => true,
    ]);
        Config::set('filesystems.disks.oss-prefix-url', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'https://your-endpoint',
            'root' => 'root',
            'prefix' => 'prefix',
        ]);
        Config::set('filesystems.disks.oss-read-only-and-prefix-url', [
            'driver' => 'oss',
            'key' => 'aW52YWxpZC1rZXk=',
            'secret' => 'aW52YWxpZC1zZWNyZXQ=',
            'bucket' => 'your-bucket',
            'endpoint' => 'https://your-endpoint',
            'root' => 'root',
            'prefix' => 'prefix',
            'read-only' => true,
        ]);
    }
}
