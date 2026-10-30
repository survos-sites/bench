<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\RemoteDuckDb;
use App\Search\RemoteDuckDbConnectionInterface;
use App\Search\S3SnapshotRepository;
use Aws\Command;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;

final class S3SnapshotRepositoryTest extends TestCase
{
    public function testMissingSnapshotIsDifferentFromDeniedAccess(): void
    {
        $handler = new MockHandler([
            new S3Exception('missing', new Command('GetObject'), ['code' => 'NoSuchKey']),
            new S3Exception('denied', new Command('GetObject'), ['code' => 'AccessDenied']),
        ]);
        $repository = $this->repository($handler);
        self::assertNull($repository->manifest('products', 'nomic'));
        $this->expectExceptionMessage('Cannot read the S3 snapshot manifest');
        $repository->manifest('products', 'nomic');
    }

    public function testManifestCannotPointOutsideItsDatasetAndProfile(): void
    {
        $manifest = [
            'schemaVersion' => 1, 'dataset' => 'products', 'profile' => 'nomic',
            'sha256' => str_repeat('a', 64), 'key' => 'other/private.duckdb',
            'table' => 'products', 'count' => 194, 'dimensions' => 768,
            'model' => 'nomic-embed-text', 'digest' => 'test',
        ];
        $repository = $this->repository(new MockHandler([new Result(['Body' => json_encode($manifest)])]));
        $this->expectExceptionMessage('Invalid or incompatible snapshot');
        $repository->manifest('products', 'nomic');
    }

    public function testInvalidDatasetRejectedBeforeS3Access(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repository(new MockHandler())->manifest('../private', 'nomic');
    }

    public function testPublishingRequiresClosedDatabase(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bench-publish-');
        file_put_contents($path.'.wal', 'pending');
        try {
            $this->expectExceptionMessage('closed, checkpointed');
            $this->repository(new MockHandler())->publish($path, []);
        } finally {
            unlink($path);
            unlink($path.'.wal');
        }
    }

    public function testWorkerReusesConnectionAndReplacesPublishedVersion(): void
    {
        $remote = $this->createMock(RemoteDuckDbConnectionInterface::class);
        $first = new \PDO('sqlite::memory:');
        $second = new \PDO('sqlite::memory:');
        $remote->expects(self::exactly(2))->method('open')->willReturnOnConsecutiveCalls($first, $second);
        $s3 = new S3Client(['version' => '2006-03-01', 'region' => 'us-east-1', 'credentials' => ['key' => 'test', 'secret' => 'test']]);
        $repository = new S3SnapshotRepository($s3, $remote, 'test-bucket', 'bench/duckdb');
        $manifest = [
            'schemaVersion' => 1, 'dataset' => 'products', 'profile' => 'openai',
            'sha256' => str_repeat('a', 64), 'table' => 'products',
            'count' => 194, 'dimensions' => 3072, 'model' => 'text-embedding-3-large', 'digest' => 'test',
        ];
        $manifest['key'] = 'bench/duckdb/products/openai/'.$manifest['sha256'].'.duckdb';
        self::assertSame($first, $repository->open($manifest));
        self::assertSame($first, $repository->open($manifest));
        $manifest['sha256'] = str_repeat('b', 64);
        $manifest['key'] = 'bench/duckdb/products/openai/'.$manifest['sha256'].'.duckdb';
        self::assertSame($second, $repository->open($manifest));
        self::assertSame($second, $repository->open($manifest));
    }

    private function repository(MockHandler $handler): S3SnapshotRepository
    {
        $s3 = new S3Client(['version' => '2006-03-01', 'region' => 'us-east-1', 'credentials' => ['key' => 'test', 'secret' => 'test'], 'handler' => $handler]);

        return new S3SnapshotRepository($s3, new RemoteDuckDb(), 'test-bucket', 'bench/duckdb');
    }
}
