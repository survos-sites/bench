# SearchBench

A Symfony-based testbed for search and AI-context techniques — built on **Meilisearch** and **DuckDB**,
using real (if small) datasets to test ideas honestly instead of on synthetic examples.

This project serves as:
- A **playground** for experimenting with Meilisearch indexing, faceting, and search UX
- A **reference implementation** for building data-driven Symfony applications backed by Meilisearch
- A **bench for AI-context experiments** — how retrieved records get serialized before they
  reach an LLM, and what that actually costs, measured against real data rather than assumed

## What This Site Offers

- Multiple **example datasets** (e.g. Congress members, wine, Jeopardy clues)
- End‑to‑end **data workflows**: fetch → normalize → enrich → index
- Tight **Meilisearch integration** with automatic indexing and settings
- **Remote DuckDB snapshots on S3** with OpenAI/Nomic semantic product search
- Symfony **Workflows & Messenger** for async data processing
- Ready-made **search and listing UIs** (Datatables, filters, facets)
- A foundation for experimenting with **custom Meilisearch indices**
- A small, well-understood dataset (wine, 1,024 rows) for testing RAG/AI-context approaches
  before trying them against larger, messier production collections

## Requirements

* PHP 8.5 with SQLite (and native `pdo_duckdb` for the DuckDB demo)
* composer
* MeiliSearch
* Symfony CLI
* Castor 

With docker, installing meilisearch is easy.  

```bash
sudo docker run --rm --name meili -d -p 7700:7700 -v $(pwd)/../meili_data:/meili_data getmeili/meilisearch:latest meilisearch
```

## Installation

This project is intended for local development and experimentation.

```bash
git clone git@github.com:survos-sites/searchbench && cd searchbench
composer install
symfony check:req


# if using sqlite
bin/console d:sch:update --force --complete
bin/console cache:pool:clear cache.app

bin/console init:congress --limit 50 
# not needed!  meili automatically updated, but need to consume the meili queue
bin/console meili:index Official
# this loads the wiki data.
bin/console state:iterate Official --marking=new --transition=fetch_wiki
# Fetches register Wikidata images with media-bundle; no Official resize transition.

bin/console messenger:consume official_fetch_wiki

bin/console meili:index App\\Entity\\Official
symfony server:start -d
symfony open:local --path /congress/simple_datatables
```

## Remote DuckDB semantic search

Products are published as immutable DuckDB files in S3. The web app
fetches a small JSON manifest, opens an in-memory DuckDB connection, and attaches
its S3 database **read-only** using `httpfs`. DuckDB requests remote byte ranges;
there is no local database download or persistent database volume. Exact cosine
search scans the embedding column, so large collections still transfer substantial
data. Remote timings in the UI include attaching the database and running SQL.

The default embedding profile is OpenAI `text-embedding-3-large` (3,072 dimensions).
Nomic (`nomic-embed-text`, 768 dimensions) remains available through the model
selector. Both document and query vectors must use the same profile and dimensions.
Nomic additionally verifies the installed Ollama model digest. OpenAI model aliases
have no digest endpoint: the manifest records the model, dimensions and input format.

Configure these in your ignored `.env.local` or deployment environment:

```dotenv
OPENAI_API_KEY=...
AWS_S3_BUCKET_NAME=museado
AWS_S3_ACCESS_ID=...
AWS_S3_ACCESS_SECRET=...
S3_ENDPOINT=https://fsn1.your-objectstorage.com
AWS_S3_REGION=us-east-1
DUCKDB_S3_PREFIX=bench/duckdb
DUCKDB_EMBEDDING_PROFILE=openai
DUCKDB_OPENAI_MODEL=text-embedding-3-large
DUCKDB_OPENAI_DIMENSIONS=3072
OLLAMA_HOST=http://127.0.0.1:11434
```

The local S3 connection uses Harvest's primary storage configuration. This is an
authenticated repository: no public bucket ACL is required. Use read-only object
credentials on web readers; publishers need object write access too. Connections
create temporary, in-memory DuckDB secrets, never persistent credential files.

Install `pdo_duckdb` and its matching `httpfs` extension on each reader/publisher
runtime (including the deployed PHP runtime). Check readiness with
`php bin/console app:duckdb:check`; it does not install or change anything.
The existing Herokuish `searchbench` runtime was checked on October 30, 2026 and
does not have `pdo_duckdb`. Prepare a compatible image or custom extension package
before enabling remote search there. Installing the extension on the host alone
does not install it in the application container.

With native PDO DuckDB installed, install its matching HTTP extension:

```bash
php -r '$pdo = new PDO("duckdb::memory:"); $pdo->exec("INSTALL httpfs; LOAD httpfs");'
```

On macOS the reader selects httpfs's `httplib` transport to avoid a bundled-curl
CoreFoundation crash in forked PHP-FPM workers. Linux retains the default transport.

Build and publish with the configured default model, or select one explicitly:

```bash
php bin/console app:duckdb:index --dataset=products --embedder=openai
php bin/console app:duckdb:index --dataset=products --embedder=nomic
php bin/console app:duckdb:query "romantic gifts" --dataset=products --embedder=openai
```

Products use fetch-bundle's cached DummyJSON download; pass `--refresh` to refresh
it. Movies use the existing `data/movie.jsonl` from the Castor movie workflow.
Embedding caches under `var/data` avoid repeat API work during imports and cache
search queries, but are disposable: losing them does not lose published indexes.
`OPENAI_API_KEY` is the canonical key name (the old `OPENAI_KEY` name is not used).

The publisher builds in temporary storage, checkpoints and closes the database,
uploads it under its SHA-256 hash, validates its row count with remote SQL, then
publishes `current.json`. Failed publication leaves the previous manifest intact;
unreferenced objects can remain after a failed verification. Completed historical
snapshots are retained. Do not overwrite a database object while readers use it.
The original local product snapshot can be migrated without re-embedding using
`app:duckdb:products:publish` (the local source is retained as a backup).

Repository layout:

```text
s3://<bucket>/<prefix>/products/openai/current.json
s3://<bucket>/<prefix>/products/openai/<sha256>.duckdb
s3://<bucket>/<prefix>/products/nomic/...
s3://<bucket>/<prefix>/movies/openai/...
s3://<bucket>/<prefix>/movies/nomic/...
```

Each manifest records the object key, row count, model, dimensions, input version,
creation time, checksum and file size. New snapshots also record the writer's
DuckDB version. Readers need a compatible DuckDB version (the initial repository
was built with DuckDB 2.0). Another DuckDB client with S3 credentials can attach
any published object and query its `products` or `movies` table directly:

```sql
ATTACH 's3://<bucket>/<prefix>/movies/openai/<sha256>.duckdb' AS catalog (READ_ONLY);
SELECT id, metadata->>'title' AS title FROM catalog.movies LIMIT 10;
```

Open `/demo/product-duckdb`; use `?profile=nomic` to compare rankings (absolute
distances are not comparable across models). Movie support is experimental and
unlinked from navigation pending a segmentation strategy. The initial 9,751-row
Nomic snapshot is retained in S3; the OpenAI movie publication was stopped.
Production deployments only need the code, native extension, S3 reader credentials,
and OpenAI key (or reachable Ollama for Nomic). Publish once from any configured
machine; subsequent deployments discover the same remote manifests automatically.

## Adding Meilisearch Datasets

A common goal of this project is to quickly prototype new Meilisearch datasets.
The typical workflow looks like this:

```bash
wget https://github.com/algolia/datasets/raw/refs/heads/master/wine/bordeaux.json -O data/wine.json
c inspect:json --dto src/Dto/Wine.php 
# Add Facet and other attributes to Wine.php
c meili:index Wine data/wine.json --update-settings --import
symfony open:local /meili/wine

```

## Survos Bundle Development

When working on this project alongside the Survos bundles, you can link a local checkout for faster iteration:
```bash
git clone git@github.com:survos/survos ../survos
cd ../survos && composer install && cd ../bench
../survos/link . 
```

@todo: meili-bundle component


## Example Dataset: Jeopardy

The Jeopardy dataset is a good example of importing large, structured trivia data into Meilisearch.

https://github.com/jwolle1/jeopardy_clue_dataset/raw/refs/heads/main/combined_season1-40.tsv

or https://github.com/jwolle1/jeopardy_clue_dataset/archive/refs/heads/main.zip

or kaggle, but can't find direct download link


## Workflow and menu integration

Symfony 8.2 discovers `OfficialWorkflow` through native attributes extended by
workflow-extras. `OfficialPlace` defines the existing `new` and `details` values;
the database marking column remains a string, so this upgrade does not rewrite data.
workflow-messenger queues `fetch_wiki` on the Doctrine `official_fetch_wiki` transport.
Use `state:iterate Official --marking=new --transition=fetch_wiki --sync` to run it inline.
State-bundle remains enabled for media-bundle and the legacy Image marking.

Application menus use `AsMenuListener` with `MenuSlot` and the Survos renderer.

## FrankenPHP runtime

`Dockerfile` builds PHP 8.5 ZTS with FrankenPHP 1.13.1, native PDO DuckDB
2.0.0.0-beta2, and its matching `httpfs` extension. Composer dependencies and
AssetMapper assets are built into the image; credentials and local databases are
excluded. Build with `docker build --load -t bench-frankenphp .`.

The product DuckDB route uses two long-running Symfony workers. Other playground
routes use classic mode until their services have been audited for retained request
state. Each worker retains at most four read-only DuckDB connections. Connections
are keyed by immutable snapshot object, replaced when `current.json` changes, and
renewed before temporary S3 credentials expire. The manifest is still fetched on
each request. Worker recycling (500 requests by default) also clears the pool.

Runtime controls: `FRANKENPHP_NUM_WORKERS=2`, `FRANKENPHP_NUM_THREADS=2`,
`FRANKENPHP_MAX_THREADS=8`, `FRANKENPHP_LOOP_MAX=500`.
`FRANKENPHP_RESET_KERNEL=1` disables retained container state as an emergency
fallback, including connection reuse.

Before deploying, select Dokku's `dockerfile` builder for `searchbench` and map
its HTTP proxy to container port 80. Retain the existing database, Redis, and
persistent mounts. Configure `OPENAI_API_KEY` and the S3 variables above on the
server; local `.env.local` is never shipped. Predeploy checks the DuckDB runtime
before migrations. Test `app:duckdb:query` inside the deployed container and the
public demo after release. The S3 snapshots require no population step on deploy.
Persistent `var/data` is useful for caching query embeddings, but the DuckDB files
are read directly from S3.

Local ARM64 container rehearsal (one worker, October 30, 2026): the first
OpenAI product search took 4.78 s end to end; repeated requests took 0.25 s and
0.18 s. Remote DuckDB time dropped from 2,753 ms to 38 ms and 24 ms. A different
query reused the connection (25 ms SQL) while paying for its new query embedding.
The classic home page, category filtering, and compiled asset also returned 200.
These are local measurements, not a production latency guarantee.

## Local faceted product search

With the development search-bundle linked from mono, run:

```console
php bin/console app:duckdb:products:local
```

This builds a fresh local immutable snapshot under `var/data/product-search`,
using cached product data and embeddings. It adds a real DuckDB FTS index.
Requires the native driver plus its matching `fts` extension (`INSTALL fts` once
on the development machine). `--embedder=nomic` builds the alternate profile;
the reader always uses the profile recorded in the local manifest. No S3 writes
or production deployment are involved.

Open `/demo/products/search`, or **Products** in the menu.
The page uses search-bundle's shared LiveComponent layout. Search uses keyword
FTS; Smart Search uses exact cosine similarity and a configurable minimum score.
The button is offered only when the local table contains vectors. Category,
brand, price range, sorting, and pagination operate on the same matching set.
Query, mode, cutoff, filters and page are preserved in the URL. Changing a filter,
mode or cutoff resets the page.

Set `PRODUCT_SEARCH_MIN_SIMILARITY` in `.env.local` to change the default (0.3).
This cutoff is model-dependent, not a percentage confidence. At the default,
“tech gifts” currently yields 23 products; setting maximum price 1200 yields 19,
with 12 on page one and 7 on page two. Query embeddings use the existing local
cache; database retrieval is local. The `/demo/product-duckdb` S3 demo remains
available separately.
