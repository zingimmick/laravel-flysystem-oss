<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use OSS\OssClient;
use Zing\Flysystem\Oss\OssAdapter;
use Zing\Flysystem\Oss\Plugins\FileUrl;
use Zing\Flysystem\Oss\Plugins\Kernel;
use Zing\Flysystem\Oss\Plugins\SetBucket;
use Zing\Flysystem\Oss\Plugins\SignUrl;
use Zing\Flysystem\Oss\Plugins\TemporaryUrl;

class OssServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('oss', function ($app, $config): Filesystem {
            $root = $config['root'] ?? '';
            $options = $config['options'] ?? [];
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

            $ossAdapter = new OssAdapter(
                new OssClient(
                    $config['key'],
                    $config['secret'],
                    $config['endpoint'],
                    $config['bucket_endpoint'] ?? false
                ),
                $config['bucket'],
                $root,
                $options
            );

            $filesystem = new Filesystem($ossAdapter, $config);

            $filesystem->addPlugin(new FileUrl());
            $filesystem->addPlugin(new SignUrl());
            $filesystem->addPlugin(new TemporaryUrl());
            $filesystem->addPlugin(new SetBucket());
            $filesystem->addPlugin(new Kernel());

            return $filesystem;
        });
    }
}
