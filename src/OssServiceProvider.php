<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use League\Flysystem\Visibility;
use OSS\OssClient;
use Zing\Flysystem\Oss\OssAdapter as Adapter;
use Zing\Flysystem\Oss\PortableVisibilityConverter;

/**
 * ServiceProvider for OSS
 */
class OssServiceProvider extends ServiceProvider
{
    /**
     * Register the OSS driver creator Closure.
     */
    public function boot(): void
    {
        Storage::extend('oss', static function ($app, $config): FilesystemAdapter {
            $root = $config['root'] ?? '';
            $options = $config['options'] ?? [];
            $portableVisibilityConverter = new PortableVisibilityConverter(
                $config['visibility'] ?? Visibility::PUBLIC,
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PUBLIC
            );
            $config['key'] ??= $config['access_key_id'] ?? null;
            $config['secret'] ??= $config['access_key_secret'] ?? null;
            $config['bucket_endpoint'] ??= $config['is_cname'] ?? false;
            $config['token'] ??= $config['security_token'] ?? null;
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
            $ossAdapter = new Adapter(
                $ossClient,
                $config['bucket'],
                $root,
                $portableVisibilityConverter,
                null,
                $options
            );
            $adapter = $ossAdapter;
            if (($config['read-only'] ?? false) === true) {
                $adapter = new ReadOnlyFilesystemAdapter($adapter);
            }

            if (! empty($config['prefix'])) {
                $adapter = new PathPrefixedAdapter($adapter, $config['prefix']);
            }

            return new OssAdapter(new Filesystem($adapter, $config), $ossAdapter, $config, $ossClient);
        });
    }
}
