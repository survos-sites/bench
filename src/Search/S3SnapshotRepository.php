<?php

declare(strict_types=1);

namespace App\Search;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class S3SnapshotRepository implements SnapshotRepositoryInterface
{
    /**
     * Shared read-only state intentionally survives Symfony's request reset.
     * Each FrankenPHP worker owns its own bounded pool; no user state is stored.
     *
     * @var array<string, array{key: string, connection: \PDO, expires: int|null}>
     */
    private array $connections = [];

    public function __construct(
        #[Autowire(service: 'bench.duckdb.s3')]
        private readonly S3Client $s3,
        private readonly RemoteDuckDbConnectionInterface $remote,
        #[Autowire('%env(AWS_S3_BUCKET_NAME)%')]
        private readonly string $bucket,
        #[Autowire('%env(DUCKDB_S3_PREFIX)%')]
        private readonly string $prefix,
    ) {
    }

    public function manifest(string $dataset, string $profile): ?array
    {
        $key = $this->base($dataset, $profile).'/current.json';
        try {
            $response = $this->s3->getObject(['Bucket' => $this->bucket, 'Key' => $key]);
        } catch (S3Exception $exception) {
            if ('NoSuchKey' === $exception->getAwsErrorCode()) {
                return null;
            }
            throw new \RuntimeException('Cannot read the S3 snapshot manifest. Check bucket, endpoint and credentials.');
        }
        $manifest = json_decode((string) $response['Body'], true, flags: JSON_THROW_ON_ERROR);
        $this->validate($manifest, $dataset, $profile);

        return $manifest;
    }

    public function publish(string $path, array $manifest): array
    {
        if (!is_file($path) || is_file($path.'.wal')) {
            throw new \RuntimeException('Publish a closed, checkpointed DuckDB database without a WAL.');
        }
        $base = $this->base($manifest['dataset'], $manifest['profile']);
        $manifest['sha256'] = hash_file('sha256', $path);
        $manifest['bytes'] = filesize($path);
        $manifest['key'] = $base.'/'.$manifest['sha256'].'.duckdb';
        $manifest['uri'] = 's3://'.$this->bucket.'/'.$manifest['key'];
        $manifest['schemaVersion'] = 1;
        $this->validate($manifest, $manifest['dataset'], $manifest['profile']);
        $stream = fopen($path, 'rb');
        try {
            $this->s3->upload($this->bucket, $manifest['key'], $stream, 'private', [
                'concurrency' => 5, 'part_size' => 8 * 1024 * 1024,
                'params' => [
                    'ContentType' => 'application/octet-stream',
                    'CacheControl' => 'max-age=31536000, immutable',
                    'Metadata' => ['sha256' => $manifest['sha256']],
                ],
            ]);
        } catch (\Aws\Exception\MultipartUploadException $exception) {
            // Failed parts must not accumulate indefinitely in the bucket.
            if (!empty($exception->getState()->getId()['UploadId'])) {
                $this->s3->abortMultipartUpload($exception->getState()->getId());
            }
            throw new \RuntimeException('S3 multipart upload failed; the previous snapshot remains active.');
        } finally {
            fclose($stream);
        }
        // Validate via remote SQL before making the object discoverable.
        $pdo = $this->open($manifest);
        if ((int) $pdo->query('SELECT count(*) FROM "'.$manifest['table'].'"')->fetchColumn() !== $manifest['count']) {
            throw new \RuntimeException('Remote row count does not match the completed snapshot.');
        }
        unset($pdo);
        $this->s3->putObject([
            'Bucket' => $this->bucket, 'Key' => $base.'/current.json',
            'Body' => json_encode($manifest, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            'ContentType' => 'application/json', 'CacheControl' => 'no-cache',
        ]);

        return $manifest;
    }

    public function open(array $manifest): \PDO
    {
        $this->validate($manifest, $manifest['dataset'], $manifest['profile']);
        $slot = $manifest['dataset'].'/'.$manifest['profile'];
        $cached = $this->connections[$slot] ?? null;
        if (null !== $cached && $cached['key'] === $manifest['key'] && (null === $cached['expires'] || $cached['expires'] > time() + 60)) {
            unset($this->connections[$slot]);
            $this->connections[$slot] = $cached;

            return $cached['connection'];
        }
        $credentials = $this->s3->getCredentials()->wait();
        $endpoint = $this->s3->getEndpoint();
        if ('https' !== $endpoint->getScheme()) {
            throw new \RuntimeException('The S3 endpoint must use HTTPS.');
        }
        $secret = [
            'KEY_ID' => $credentials->getAccessKeyId(), 'SECRET' => $credentials->getSecretKey(),
            'REGION' => $this->s3->getRegion(), 'ENDPOINT' => $endpoint->getAuthority(),
            'SCOPE' => 's3://'.$this->bucket.'/'.trim($this->prefix, '/').'/',
        ];
        if (null !== $credentials->getSecurityToken()) {
            $secret['SESSION_TOKEN'] = $credentials->getSecurityToken();
        }

        $connection = $this->remote->open('s3://'.$this->bucket.'/'.$manifest['key'], $secret);
        unset($this->connections[$slot]);
        $this->connections[$slot] = ['key' => $manifest['key'], 'connection' => $connection, 'expires' => $credentials->getExpiration()];
        if (count($this->connections) > 4) {
            unset($this->connections[array_key_first($this->connections)]);
        }

        return $connection;
    }

    private function base(string $dataset, string $profile): string
    {
        if ('' === $this->bucket) {
            throw new \RuntimeException('Configure AWS_S3_BUCKET_NAME and S3 credentials first.');
        }
        foreach ([$dataset, $profile] as $value) {
            if (!preg_match('/^[a-z0-9][a-z0-9-]*$/D', $value)) {
                throw new \InvalidArgumentException('Invalid snapshot dataset or embedding profile.');
            }
        }

        return trim($this->prefix, '/').'/'.$dataset.'/'.$profile;
    }

    private function validate(array $manifest, string $dataset, string $profile): void
    {
        $base = $this->base($dataset, $profile);
        if (($manifest['schemaVersion'] ?? null) !== 1 || ($manifest['dataset'] ?? null) !== $dataset
            || ($manifest['profile'] ?? null) !== $profile
            || !preg_match('/^[a-f0-9]{64}$/D', $manifest['sha256'] ?? '')
            || ($manifest['key'] ?? null) !== $base.'/'.$manifest['sha256'].'.duckdb'
            || !in_array($manifest['table'] ?? null, ['products', 'movies'], true)
            || $manifest['table'] !== $dataset
            || !is_int($manifest['count'] ?? null) || $manifest['count'] < 1
            || !is_int($manifest['dimensions'] ?? null) || $manifest['dimensions'] < 1
            || !is_string($manifest['model'] ?? null) || !is_string($manifest['digest'] ?? null)) {
            throw new \RuntimeException('Invalid or incompatible snapshot manifest.');
        }
    }
}
