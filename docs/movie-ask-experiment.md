# Movie Ask: local snapshot experiment

Experiment date: 2026-11-27. This is a development experiment, not a current movie service.

## What is running

`/ask/movies` uses the shared `survos/ai-chat-bundle` composer and a Symfony AI agent.
The agent has bounded movie search, detail and aggregate tools; it cannot submit SQL.
`survos/duckdb-bundle` opens the named read-only `movie_snapshot` source and adds SQL/connection/schema profiling.
The app retains `survos/ai-duckdb-store`, but this experiment queries the movie-native schema directly: that store's generic document schema is not required for these structured filters.

The page is available only in dev/test. POST and reset validate CSRF, history is session-scoped and bounded,
tool calls are capped at three, and answers render through CommonMark with raw HTML escaped and unsafe links disabled.
The latest retrieved movie IDs link to TMDB. The page distinguishes database timings from full answer time.
No production deployment, ES indexing, InstantSearch projection, dbt installation, scheduler or public HF upload is part of this experiment.

Index inventory: the `.duckdb` file contains a unique `movies_id` index on TMDB `id`. Vectors are typed columns searched by exact cosine scans, not an ANN/HNSW index. No DuckDB FTS index has been built; title/director filters are literal case-insensitive substring scans. The original Parquet is retained as source evidence and is not queried by Ask.

## Source and limits

- Download: [remsky/Embeddings__Ultimate_1Million_Movies_Dataset](https://huggingface.co/datasets/remsky/Embeddings__Ultimate_1Million_Movies_Dataset).
- Pinned HF revision: `e59e9402f747fbe56f5c429f22a6eced2219f34d`; source Parquet SHA-256: `5d210a0c71238fabde1d6ed276bd01ea867b9aa780634e0cdd9836f23df8bdf9`.
- Upstream: [Alan Vourch's TMDB + IMDb dataset](https://www.kaggle.com/datasets/alanvourch/tmdb-movies-daily-updates).
- 1,035,695 movie records; 31 columns. Source Parquet: 7,029,396,264 bytes, **one row group**.
- The publisher describes an early-2025 capture. Download time is not source freshness.
- Newer upstream data reportedly added keywords and US certifications on June 20, 2026. Neither field exists in this downloaded schema.
- Existing embeddings: Nomic Embed Text through Ollama, 768 dimensions, originally strings. No movie was re-embedded.
- Source model is named `nomic-embed-text:latest`; its digest is unknown. Most records include genre in their embedding input; the initial 10–15% reportedly do not. Source prefix details are not pinned.
- Local query model: `nomic-embed-text`, digest `0a109f422b47e3a30ba2b10eca18548e944e8a23073ee3f3e947efcf3c45e59f`, using `search_query: ` prefix.
- Matching dimensions/model family is encouraging, but does not prove exact compatibility. Example retrieval was plausible, not a systematic quality evaluation.

## Conversion

DuckDB 2.0.1, native PDO, two conversion threads. All original metadata is preserved. Only `embedding` changes to `FLOAT[768]`; this reduces precision to float32 without calling a model.

```sh
hf download remsky/Embeddings__Ultimate_1Million_Movies_Dataset --type dataset --revision e59e9402f747fbe56f5c429f22a6eced2219f34d --local-dir /absolute/path/source
hf cache verify remsky/Embeddings__Ultimate_1Million_Movies_Dataset --type dataset --local-dir /absolute/path/source --fail-on-missing-files
php tools/movie-snapshot/convert.php /absolute/path/source/movies_with_embeddings.parquet /absolute/path/movies.duckdb
```

Use a new output path. The converter writes a staging file, validates, adds the ID index, checkpoints/closes, rejects a pending WAL, then renames and writes a checksum manifest.
The source's single giant column chunk required a 16 GB DuckDB memory budget on a 48 GiB machine; a 4 GB budget failed before conversion. Future Parquet exports should use bounded row groups.

The completed pilot took 102.19 seconds for conversion, followed by validation/index/checkpoint.
It contains 1,035,695 unique non-null IDs, zero failed embedding casts, and zero null-element/non-finite/zero-length-norm vectors.
Serving file: 3,721,146,368 bytes; SHA-256 `2d1b062dec62658b9a8d1615490fb8240b6269d7e170fc4e64e9ed44768c04bc`.

## Run Ask

Point `MOVIE_DUCKDB_URI` at the completed local file; default: `var/data/movies/movies.duckdb`.
On the pilot machine this is a local symlink to the chat's downloaded/converted artifact. The database is not committed.
Existing `OPENAI_API_KEY` and `OLLAMA_HOST` configuration is reused. Answer generation uses `gpt-4o-mini`; question embedding uses local Nomic. No new external API service was provisioned.

```sh
php bin/console app:movies:ask 'Which Akira Kurosawa films from the 1950s are in this snapshot?'
php bin/console app:movies:ask 'Recommend three science-fiction movies about memory and identity with at least 100 TMDB votes.'
php bin/console app:movies:ask 'How many movies are in this snapshot, and what are its most common original languages?'
```

Open `https://127.0.0.1:8040/ask/movies` (Bench's existing PHP-FPM development server) or the local Bench domain.
Use the DuckDB profiler on the POST request for query details; the redirected GET itself does no retrieval.

## Local retrieval measurements

Four query threads, 4 GB query memory limit, same connection across three repetitions. Results fully fetched. Local SSD copy; normal OS cache, **not an OS cold-cache benchmark**. Semantic query: “science fiction about memory and identity”.

| Query | First measured run | Repeat 1 | Repeat 2 |
|---|---:|---:|---:|
| Count all movies | 2.20 ms | 0.37 ms | 0.33 ms |
| Kurosawa, 1950–1959 | 21.67 ms | 12.57 ms | 12.08 ms |
| Exact cosine, TMDB votes ≥100 | 436.87 ms | 21.89 ms | 17.43 ms |
| Exact cosine, all movies | 537.19 ms | 221.90 ms | 189.80 ms |

These are SQL/execute/fetch times, excluding question embedding and answer generation. Earlier queries can warm later ones.
To repeat the measurements with the full-vector query first: `php tools/movie-snapshot/benchmark.php /absolute/path/movies.duckdb`. The same command accepts an S3 URI with `MOVIE_S3_ENDPOINT`, `MOVIE_S3_ACCESS_KEY` and `MOVIE_S3_SECRET_KEY` supplied through local environment configuration. It prints JSON including the local query-model digest and separate embedding/open/query times; it never creates an index or writes to the source database.
No ANN index is used. Unfiltered semantic results favor obscure short films whose descriptions closely match the query; a stated minimum-vote constraint produces more recognizable recommendations. Never apply that constraint silently.
Real agent smoke tests: director answer ~5.2 s; semantic recommendations ~10.05 s (retrieval 49.5 ms for the agent's particular constrained query). A browser POST returned a director answer with clickable sources in ~5.54 s.

## X10 S3 status

X10 storage is the local SeaweedFS service on `http://127.0.0.1:8333`, rclone remote `owc:`.
The initial upload to `museado` failed: SeaweedFS reported I/O errors writing filer metadata and no writable volumes for that bucket. Repeated retries were stopped. Local validation and retrieval results do not imply a successful remote upload.
After the approved restart, a small write succeeded in the existing writable `survos-platform` bucket. The full upload completed at approximately 196 MiB/s. A full S3 readback SHA-256 matched the local file.

Published object:
`s3://survos-platform/bench/experiments/movies-2025/2d1b062dec62658b9a8d1615490fb8240b6269d7e170fc4e64e9ed44768c04bc.duckdb`

Manifest: `s3://survos-platform/bench/experiments/movies-2025/manifest.json`.
The local Ask page reads the local copy by default; these S3 measurements came from an independent read-only connection. No S3 credentials are committed.

| X10 S3 query | First measured run | Repeat 1 | Repeat 2 |
|---|---:|---:|---:|
| Kurosawa, 1950–1959 | 81.81 ms | 12.84 ms | 12.57 ms |
| Exact cosine, votes ≥100 | 1048.17 ms | 23.53 ms | 14.99 ms |
| Exact cosine, all movies, after filtered query | 292.02 ms | 192.85 ms | 193.81 ms |
| Exact cosine, all movies, **first query on a fresh connection** | 2636.53 ms | 317.41 ms | 216.74 ms |

Connection open/attach took 72–79 ms. The fresh-connection run overlapped a readback checksum scan and the service/OS cache was not cleared. These are localhost S3-on-X10 results, not WAN latency claims. Local and S3 queries returned identical IDs and scores. This is adequate for a local Ask experiment; benchmark fresh PHP-FPM requests and WAN access separately before choosing production serving.

## Re-embedding estimate on this machine

A deterministic hash sample of 4,096 movies, 128 batches of 32, took **31.58 seconds**: **129.7 movies/second**, projecting to **2.22 hours** for all 1,035,695 records. Budget **3–4 hours** for a full rebuild including writing, validation, retry/resume overhead and competing workloads. This is an estimate, not a completed full re-embedding run.

The warm local Nomic model used a consistent proposed recipe: `search_document: ` + existing title/tagline/overview text + ` Genres: ` + genres, with truncation disabled. Every sample returned a 768-element vector. Sample vectors were discarded; the serving database still has the publisher's vectors. No embedding cache was used in this timing. Mean original text length is 249 characters, median 196; 183,202 movies lack an overview. Larger inputs, a different recipe/model or an updated corpus require a fresh estimate.

## Lessons for Smithsonian and other collection snapshots

1. Preserve provider identity and record ID; keep source schema separate from serving projections. TMDB `id` is stable for this pilot, not a universal cross-provider identity scheme.
2. Publish a versioned manifest: source URL/revision/hash, capture date vs build date, schema version, row counts, validation results, exact engine version, and artifact hash/size.
3. Keep immutable source and derived artifacts. Upload under a content hash, verify remotely, then update a small manifest pointer. Do not overwrite a database while readers use it.
4. Publish typed vectors with exact model revision, dimensions, precision, normalization, prefixes, and the versioned text-building recipe. Store per-record embedding-input hashes so metadata-only changes do not require re-embedding.
5. Test missing values, duplicate IDs, bad casts, vector shape/finiteness/norms and relational integrity before publication. Do not silently discard failed records.
6. Measure metadata, filtered vectors and full scans separately; distinguish local SSD, localhost S3 and WAN S3, first measured vs warm runs, embedding time and answer time.
7. Document absent fields and source drift. Do not infer new metadata from an old snapshot or call it current because it was downloaded today.
8. Keep ES/InstantSearch a derived projection keyed by the same IDs. Ask tools should expose a bounded retrieval contract that can later use either backend.
9. For a public HF refresh, verify redistribution rights and attribution, join new source data by ID, classify inserts/updates/deletes, and re-embed only changed inputs. Publish a fresh immutable release with changed schema/coverage explained.

## A later dbt comparison

This is a better bounded dbt exercise than migrating Harvest first. Keep the downloaded source fixed and reproduce the same typed movie projection with a dbt model. Add uniqueness/null/vector tests and compare every output field and vector against this baseline. dbt would manage transformation dependencies/tests/documentation; it would not own downloads, embedding API calls, S3 publication or the Ask UI. No dbt setup is needed to use today's pilot.

## Newer Kaggle source downloaded separately

The current upstream release was downloaded after the pilot: observed version **1075**, updated **2026-11-27 07:29:19 UTC** (02:29:19 America/New_York).
The 346,100,175-byte ZIP was extracted with ZIP CRC validation. `TMDB_all_movies.csv` is 808,630,597 bytes with **1,279,518 records and 30 columns**; `etl_state.json` is also preserved.
New fields `keywords` and `certification_us` are present. This release has `cast`, whereas the HF derivative has `movie_cast`; it does not have the HF derivative's embedding/token-count/combined-text columns.

CSV SHA-256: `da64e36756843a65824ce698830ec3abbb0396aa5403b842cd82a7859a2b3ac8`.
Archive SHA-256: `4b4046ab1b79d243493961d90215fa2b6831f8c49afce37cc109e9d9ca004c66`.
The newer release is downloaded only: it has not been merged into Ask or embedded. Its greater row count is not proof that all older IDs are retained; compare by ID before planning an incremental rebuild.

## Responsiveness finding and next step

The user found the page slow. The observed 5–10 second answer time is mostly model/tool orchestration and answer generation, not DuckDB. The current browser form waits for the completed answer and redirects, although it immediately disables submission and announces pending work. Streaming progress/tool events and text deltas is the next improvement; it has not been implemented in this commit. Symfony AI's execution iterator already exposes `Progress`, `ToolCall` and `TextDelta`, and FF's Ask controller provides a working pattern. Keep the shared ai-chat composer and preserve CSRF, history, reset and error handling when adding a streaming endpoint.
