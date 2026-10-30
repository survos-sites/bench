<?php

declare(strict_types=1);

namespace App\Search;

/** Opens remote blocks through httpfs; never stages the database on local disk. */
final class RemoteDuckDb implements RemoteDuckDbConnectionInterface
{
    public function open(string $url, #[\SensitiveParameter] array $s3 = []): \PDO
    {
        if (!in_array(parse_url($url, PHP_URL_SCHEME), ['https', 's3'], true)) {
            throw new \InvalidArgumentException('Remote snapshots require HTTPS or S3.');
        }
        $pdo = new \PDO('duckdb::memory:');
        try {
            $pdo->exec('LOAD httpfs');
            if ('Darwin' === PHP_OS_FAMILY) {
                // httpfs's bundled curl initializes macOS proxy preferences after
                // FPM forks, which crashes in CoreFoundation. httplib avoids that.
                $pdo->exec("SET httpfs_client_implementation = 'httplib'");
            }
            // Do not silently download the whole file if the server ignores Range.
            $pdo->exec('SET auto_fallback_to_full_download = false');
            if ([] !== $s3) {
                $parts = ['TYPE s3', 'URL_STYLE path', 'USE_SSL true'];
                foreach ($s3 as $name => $value) {
                    if (!in_array($name, ['KEY_ID', 'SECRET', 'SESSION_TOKEN', 'REGION', 'ENDPOINT', 'SCOPE'], true)) {
                        throw new \InvalidArgumentException('Invalid S3 secret parameter.');
                    }
                    $parts[] = $name.' '.$pdo->quote($value);
                }
                $pdo->exec('CREATE SECRET bench_s3 ('.implode(', ', $parts).')');
            }
            $pdo->exec('ATTACH '.$pdo->quote($url).' AS snapshot (READ_ONLY)');
            $pdo->exec('USE snapshot');
        } catch (\Throwable $exception) {
            // DuckDB errors can include the signed URL. Never expose it to logs/profilers.
            $reason = preg_replace('~https?://[^\s\'"<>]+~', '[redacted remote URL]', $exception->getMessage());
            foreach (['KEY_ID', 'SECRET', 'SESSION_TOKEN'] as $key) {
                if (isset($s3[$key]) && '' !== $s3[$key]) {
                    $reason = str_replace($s3[$key], '[redacted]', $reason);
                }
            }
            throw new \RuntimeException('Cannot attach the remote DuckDB snapshot: '.$reason);
        }

        return $pdo;
    }
}
