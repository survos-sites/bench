# Proposal: a DuckDB store bridge for Symfony AI

A working implementation and deployment experiment, recorded October 30, 2026.
This is a discussion proposal, not an accepted Symfony AI API or an upstream PR.

## Motivation

Small semantic-search datasets should be easy to build once and distribute as
queryable artifacts. A DuckDB file can hold document IDs, embeddings, and metadata
without running a separate vector database server. Readers can use a local file
or attach an immutable remote file through DuckDB's `httpfs` extension.

SearchBench demonstrates this with 194 DummyJSON products. It fetches the corpus,
generates embeddings, writes a complete database, and publishes it to private S3
storage. The web application queries that object directly; it does not first
download the database into its persistent volume.

## Working implementation

- [Published package: survos/ai-duckdb-store](https://packagist.org/packages/survos/ai-duckdb-store)
- [Store implementation and integration tests](https://github.com/survos/ai-duckdb-store/tree/2.45.9)
- [SearchBench application implementation](https://github.com/survos-sites/bench/commit/bef860d)

The bridge implements `StoreInterface` and `ManagedStoreInterface`. Its constructor
accepts an existing native PDO DuckDB connection, a table name, and dimensions:

```php
use Survos\Ai\DuckDb\Store;

$store = new Store(new PDO('duckdb:documents.duckdb'), 'documents', 768);
$store->setup(); // writer only
```

The bridge supports vector-document upserts, removal, clear, count, setup, drop,
and `VectorQuery`. It stores vectors as `DOUBLE[N]` and metadata as JSON, preserving
`Metadata::KEY_TEXT`. Search uses exact `array_cosine_distance`, ordered ascending
with document ID as the tie-breaker. The returned score is a distance, not a
relevance probability. It requires neither VSS nor an approximate-nearest-neighbor
index.

Filters are an AND of top-level metadata keys with scalar equality values. Keys
and values are bound parameters. Null means explicit JSON null, not a missing key.
Unknown options, mismatched dimensions, non-finite coordinates, and zero vectors
are rejected. Batch writes use a transaction unless the caller already owns one.

The connection remains caller-owned. Constructing a store, counting, and querying
do not create tables or perform writes. A reader can therefore use a read-only
connection without calling `setup()`.

## Remote snapshots: application composition around the bridge

Remote access does not require a different store implementation. The caller opens
an in-memory DuckDB connection, loads `httpfs`, configures credentials, attaches
the remote database read-only, selects its catalog, and injects that PDO into the
same store. Bench uses this lifecycle:

1. Fetch the product data through a cached HTTP client.
2. Embed in bounded batches and write a fresh local temporary database.
3. Checkpoint and close the writers; reject publication when a WAL remains.
4. Upload the completed file under an immutable content-hash key.
5. Verify the remote row count before publishing `current.json`.
6. Read the manifest and query its referenced object through S3 range requests.

The manifest includes the embedding model, dimensions, digest/revision, text
format version, row count, checksum, and object key. Query vectors must use the
same embedding profile as the indexed documents. Matching dimensions alone is
insufficient. Bench defaults to OpenAI `text-embedding-3-large` at 3,072 dimensions
and retains Nomic at 768 dimensions in a separate snapshot for comparison.

Bench disables DuckDB's automatic fallback to downloading the entire remote
file. Credentials use a temporary DuckDB S3 secret, rather than a public object
or persisted credential file. Existing published objects are never overwritten.

Exact search can still read much or all of the vector data. Range access avoids
mandatory whole-file staging; it does not make exhaustive vector search cheap
for arbitrarily large datasets. Larger corpora and segmentation need separate
measurements. Movies are deferred for that reason.

## Long-running workers and measured behavior

Bench's FrankenPHP product-search workers keep their Symfony container alive.
Each worker retains a bounded pool of up to four read-only connections. A changed
snapshot key or expiring temporary credentials causes a new connection to open.
The manifest is fetched on each request, and worker recycling clears the pool.
The bridge itself does not implement this application-level pool.

In a local ARM64 container with PHP 8.5.12 ZTS, DuckDB 2.0.0, and one worker:

| Measurement | First request | Repeated requests |
| --- | ---: | ---: |
| HTTP response time | 4.78 s | 0.25 s / 0.18 s |
| Remote DuckDB attach/query time | 2,753 ms | 38 ms / 24 ms |

The repeated query also benefited from a cached query embedding and DuckDB's
warm caches. These figures do not isolate connection setup from all caching
benefits. A different query reused the connection and took 25 ms in DuckDB while
incurring the separate cost of generating its query embedding. These are small
corpus development measurements, not a production benchmark.

## Proposed first upstream PR

Port the small native-PDO store bridge, its documentation, and deterministic
integration tests into Symfony AI's store bridge structure. Adapt the namespace,
package metadata, supported PHP versions, and CI installation to upstream
conventions. Keep the PR focused on the store contract:

- Vector persistence, exact cosine-distance retrieval, and metadata filters.
- Explicit dimensions and validation of vectors and options.
- Caller-owned connection and transaction behavior.
- Read-only retrieval from a completed database.
- Native-driver integration tests, including separate writer/reader connections.

Data fetching, embedding-provider selection, S3 publication, manifest design,
credential provisioning, and FrankenPHP worker pooling remain in the consuming
application. A remote-attachment example can demonstrate composition without
adding AWS SDK, Docker, Symfony kernel, or application dependencies to the bridge.

Discussion points before opening the PR:

- Is native `pdo_duckdb` an acceptable required extension for this bridge?
- What PHP/driver/DuckDB versions should upstream support and test? The prototype
  currently requires PHP 8.5 and was validated with DuckDB 2.0.0.
- Does the exact distance score and limited equality-filter contract fit the
  conventions expected of other store bridges?
- Should remote attachment be documented here or in a separate cookbook example?

The package's native integration suite previously passed 31 tests / 61 assertions.
Bench's application suite passed 13 tests / 73 assertions, including connection
reuse and replacement after a published snapshot changes. Its FrankenPHP image
was exercised with real remote S3 reads, category filters, and compiled assets.
Production deployment verification is still pending; the local measurements above
must not be presented as live production results.

## Suggested discussion comment

> We have a working native-PDO DuckDB bridge for Symfony AI, plus a small
> SearchBench demo that queries immutable DuckDB snapshots directly from S3.
> The store handles vector persistence and exact cosine-distance retrieval;
> publication and worker connection reuse live in the application. The linked
> proposal includes implementation links, limitations, measurements, and a
> focused first-PR scope. Would this be a useful store bridge to contribute?
