<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;
use OSS\OssClient;
use Zing\Flysystem\Oss\OssAdapter;
use Zing\Flysystem\Oss\PortableVisibilityConverter;

class OssServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('oss', function ($app, $config): FilesystemAdapter {
            $root = $config['root'] ?? '';
            $options = $config['options'] ?? [];
            $portableVisibilityConverter = new PortableVisibilityConverter(
                $config['visibility'] ?? Visibility::PUBLIC,
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PUBLIC
            );

            $config['key'] = $config['key'] ?? $config['access_key_id'] ?? null;
            $config['secret'] = $config['secret'] ?? $config['access_key_secret'] ?? null;
            $config['bucket_endpoint'] = $config['bucket_endpoint'] ?? $config['is_cname'] ?? false;
            $config['token'] = $config['token'] ?? $config['security_token'] ?? null;

            $options = array_merge(
                $options,
                Arr::only($config, ['url', 'temporary_url', 'endpoint', 'bucket_endpoint'])
            );

            $ossClient = new OssClient(
                $config['key'],
                $config['secret'],
                $config['endpoint'],
                $config['bucket_endpoint'] ?? false,
                $config['token'],
                $config['proxy'] ?? null
            );
            $ossAdapter = new OssAdapter(
                $ossClient,
                $config['bucket'],
                $root,
                $portableVisibilityConverter,
                null,
                $options
            );

            return new FilesystemAdapter(new Filesystem($ossAdapter, $config), $ossAdapter, $config);
        });
    }
}
