<?php

declare(strict_types=1);

namespace Zing\LaravelFlysystem\Oss\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Zing\Flysystem\Oss\OssAdapter;

/**
 * @internal
 */
final class DriverTest extends TestCase
{
    public function testDriverRegistered(): void
    {
        self::assertInstanceOf(OssAdapter::class, Storage::disk('oss')->getAdapter());
    }

    public function testUrl(): void
    {
        self::assertStringStartsWith('https://test-url', Storage::disk('oss-url')->url('test'));
    }

    public function testTemporaryUrl(): void
    {
        self::assertStringStartsWith(
            'https://test-temporary-url',
            Storage::disk('oss-temporary-url')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }

    public function testBucketEndpoint(): void
    {
        self::assertStringStartsWith('https://your-endpoint', Storage::disk('oss-bucket-endpoint')->url('test'));
    }

    public function testIsCname(): void
    {
        self::assertStringStartsWith(
            'https://your-endpoint',
            Storage::disk('oss-bucket-endpoint')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
        self::assertStringStartsWith(
            'https://your-endpoint',
            Storage::disk('oss-is-cname')->temporaryUrl('test', Carbon::now()->addMinutes())
        );
    }
}
