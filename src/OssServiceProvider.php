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
                $config['visibility'] ?? Visibility::PUBLIC
            );
            if (! isset($config['is_cname']) && isset($config['bucket_endpoint'])) {
                $config['is_cname'] = $config['bucket_endpoint'];
            }

            if (isset($config['is_cname']) && ! isset($config['bucket_endpoint'])) {
                $config['bucket_endpoint'] = $config['is_cname'];
            }

            $options = array_merge(
                $options,
                Arr::only($config, ['url', 'temporary_url', 'endpoint', 'bucket_endpoint'])
            );

            $ossClient = new OssClient(
                $config['key'],
                $config['secret'],
                $config['endpoint'],
                $config['bucket_endpoint'] ?? false
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
