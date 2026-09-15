# Search baseline — 2026-09-15

## Checkpoint

Baseline application commit: `50c7811029e86bb8140aef63e7f783cecb0e461b` (main, matching
origin at review time). The release also includes this documentation; runtime code is
unchanged. This is a source checkpoint, not a certified passing release or a dataset backup.

The app already configures `survos_search.default_adapter: es`. It also retains Meili
homepage/InstantSearch integration, Meili entity metadata, Meili chat configuration, and
an API Platform Meili Movie grid demonstration. Therefore the checkpoint is not labelled
Meili-only or pre-Elasticsearch. Further work should complete and validate the existing ES
path while preserving these deliberate examples.

## Why Bench is the principal testcase

Movie supplies title/overview text, array facets (genres, actors, characters, tags), numeric
filters (year, votes, budget), and sorting. Car, Marvel, WCMA and other museum collections
broaden the field types and metadata shapes. The app also overrides SearchBundle's Hits
template, making it a useful test of real consumer integration rather than an isolated API.

The repo has SearchBundle and elastic-bundle dependencies, default/bm25/es adapter entries,
and engine-specific Meili/API Platform integrations. Migration requires an inventory of
which route reads which dataset and which indexing path owns it; changing the default alone
does not migrate the direct Meili pages.

## Validation at checkpoint

- Working tree was clean and local main matched origin before documentation.
- `php vendor/bin/phpunit --no-progress` fails during discovery: missing
  `Pierstoval\SmokeTesting\SmokeTestStaticRoutes` in `tests/AllRoutesTest.php`.
  `FunctionalTest.php` also depends on that smoke-testing library.
- The GitHub Symfony workflow selects PHP 8.3, while composer.json requires PHP ^8.5.
  Correct the workflow and restore/update smoke tests before using CI as migration evidence.
- No live corpus counts, ES/Meili parity, throughput, or browser checks were established
  during this review. Do not interpret the release as those checks passing.

## Mac data inventory

PHP 8.5.10 / Symfony 8.1.4 boot with `php bin/console about`; `castor list --raw`
discovers download/load/load:all. Raw inputs already exist for Movie (CSV and gzip), Car,
Wine, WCMA, Amsterdam (English and Dutch), WAM, and Marvel (`zip/marvel.zip`). Do not
re-fetch them merely because this is a different machine; first verify conversion/import.

A read-only inventory of `var/data.db` found WAM 9,925 and Officials 540 rows; Movie, Car,
Wine, Marvel, WCMA, Amsterdam, and Jeopardy tables were empty. This inventory is of that
file, not a claim that the active DATABASE_URL points to it. WAM JSONL/profile files exist;
Movie/Car/Marvel JSONL outputs were absent in the top-level data directory.

Use `castor.php`'s dataset registry as the starting point. `bin/download-dataset.sh`
fetches a different, older set (books/recipes/movies). `bin/load-data.sh` calls all loaders,
then grid indexing and `elastic:index:create --drop`; it must not be used as a harmless
inventory or fetch command. `bin/release.sh` starts the app, compiles assets, and applies
database migrations; it is not a GitHub release tool.

Restore one dataset at a time after the source release: confirm the active database,
validate the existing input, convert/profile, import into an isolated development database,
then explicitly build the selected search index. Avoid `--reset`/`--drop` against existing
data. Validate command exit status and resulting row counts rather than trusting the
best-effort load:all summary. Missing raw files can use `castor download <code>`.

The `load` task does not run `afterDownload` like `download` does, and duplicates the
limit option when supplied. Verify gzip conversion and current Dataset-derived output
paths before relying on a complete fresh-machine rebuild. WAM has no public URL in the
registry and relies on its prepared local CSV/archive. Record source checksums and counts
when the evaluation fixture is established.

## Evaluation contract

Start with lexical retrieval and fixed corpus/query fixtures. Record result judgments and
typo/prefix cases, OR-within/AND-across facet semantics, selected-facet count scope, numeric
ranges, empty queries/results, stable sorting/paging, highlighting and detail links.
Measure end-to-end latency under stated load, indexing duration, memory, index size, and
update/delete propagation. Verify safe rebuild publication and recovery before rollout.

GlobalGiving's lab provides a useful pattern for shared snapshots, cached embeddings with
model provenance, per-engine results, and relevance judgments. It is not yet a full faceted
browsing benchmark. Reuse the experiment method after lexical behavior is established.

Fix shared shortcomings in SearchBundle and engine bundles. Named searches/datasets may
override the application default. Retain Postgres BM25, SQLite FTS5, Meili, and other
adapters; do not introduce automatic multi-engine fan-out or mandatory duplicate indexing.

Folio ES search is the next separate workstream. Bench should establish reusable behavior
for KPA/packages too, with application-specific validation before migration.
