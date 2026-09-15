# Elasticsearch evaluation

The `es` branch makes Elasticsearch the main lexical search experience. The source baseline
is [search-baseline-2026-09-15](https://github.com/survos-sites/bench/releases/tag/search-baseline-2026-09-15).
Meilisearch and the other adapters remain supported. Bench can deliberately compare engines
over the same entities; ordinary applications can select different engines per dataset.

## Architecture

- `survos/elastic-bundle` owns console commands, mapped index generations, bulk population,
  and Doctrine/Messenger synchronization. No FOSElasticaBundle.
- `survos/search-bundle` owns named searches, engine selection, querying, and the small
  InstantSearch HTTP adapter. A default adapter is a fallback, not a project-wide restriction.
- Actual InstantSearch widgets manage query, facets, ranges, sorting, pagination, and URL state.
  Search hits are rendered **in JavaScript** using twig-browser. FOS JSRouting supplies `path()`;
  it is unrelated to FOSElasticaBundle and remains required for result links.
- `/search` opens movies; `/search/car`, `/search/marvel`, and `/search/wcma` use the same UI.
  Existing direct Meili demonstrations remain. Automatic Meili event indexing is disabled on
  this evaluation branch so normal writes have one owner. Explicit comparison indexing is separate.
- Semantic search, vectors, chat, and Folio are outside this evaluation.

## Mac setup

Use PHP 8.5, Composer, Symfony CLI, Castor, and a local Elasticsearch 9 node. The tested node
was 9.5.3 at `http://127.0.0.1:9200`. Existing raw files were reusable; no downloads were needed.

Until companion bundle changes are released, check out `survos/mono` on branch `es` alongside
this repository. After `composer install`, run:

```bash
php bin/link-search-bundles.php ../mono
php bin/console cache:clear
```

The helper backs up installed bundle directories under ignored `var/local-bundle-backup/`, then
links only search-bundle and elastic-bundle. Composer's lock remains the release baseline;
rerun the helper after Composer reinstalls either bundle. CI uses the same companion checkout.
Replace this bridge with released Composer requirements when the bundle evaluation is merged.

Use an isolated development database and index prefix in `.env.local`:

```dotenv
DATABASE_URL=sqlite:///%kernel.project_dir%/var/searchbench-es.db
SEARCH_INDEX_PREFIX=searchbench_es_
ELASTICSEARCH_DSN=elasticsearch://127.0.0.1:9200
```

Check the configured DSN before changing it: this repo already supplies the local ES connection.
Create the schema only when starting with a new database:

```bash
php bin/console doctrine:schema:create
php bin/console messenger:setup-transports elastic
ELASTIC_ASYNC=0 castor es:load --code=movie
ELASTIC_ASYNC=0 castor es:load --code=car
ELASTIC_ASYNC=0 castor es:load --code=marvel
ELASTIC_ASYNC=0 castor es:load --code=wcma
php bin/console elastic:spool:flush
symfony server:start -d
```

`es:load` reuses downloaded inputs, fetches only missing files, converts to JSONL, imports,
then rebuilds the selected index. It does not reset an existing database. Cars need synthetic
row IDs because the raw feed has no primary key and Identification.ID contains duplicates.
IDs are repeatable for the same ordered snapshot; a replacement/reordered feed requires an
explicit database reset/reimport. Marvel's object-valued ranking, image, URL, and color fields
are not flat facets. Scalar-list facets remain supported, and Meili retains its original nested-object filter declarations.

## Empty indexes and ongoing writes

```bash
php bin/console elastic:index:populate app_movie  # creates a missing mapped index, then fills it
php bin/console elastic:index:rebuild app_movie   # fill a new generation, then atomically swap alias
php bin/console elastic:index:status
php bin/console messenger:consume elastic --no-debug
php bin/console elastic:spool:flush               # recover spooled imports or queue outages
```

The Symfony local-server configuration starts a dedicated `elastic` worker. Restart an already
running server after adding its worker configuration, or run the consumer directly. After changing
configuration with debug disabled, clear the cache and restart workers.

Normal writes enqueue IDs after Doctrine flush. The worker loads current database state, indexes
present records and removes missing ones. Capturing deletions before Doctrine clears generated
IDs is essential. A stale deletion job reconciles a replacement row instead of deleting it blindly.
The elastic queue uses the same database connection, so jobs dispatched inside an outer transaction
are visible after commit. A full transactional outbox covering a process crash between a standalone
flush commit and dispatch is not implemented. Use one indexing consumer per dataset; do not assume
concurrent workers provide version ordering. DQL/SQL bulk updates bypass Doctrine events and need
an explicit populate/rebuild. Pause writes or arrange replay during a full rebuild.

With `ELASTIC_ASYNC=0`, changes go to the durable JSONL spool. A failed drain retains its claim;
new writes arriving during a drain remain pending. Missing indexes cause incremental jobs to fail
with a population instruction rather than silently creating an index with inferred mappings.

## Validation on 2026-09-15

| Dataset | Database / indexed documents |
|---|---:|
| Movies | 9,751 |
| Cars | 5,076 |
| Marvel | 1,807 |
| WCMA | 17,092 |

```bash
ELASTIC_LIVE=1 vendor/bin/phpunit
vendor/bin/phpunit --no-configuration --bootstrap vendor/autoload.php ../mono/bu/elastic-bundle/tests ../mono/bu/search-bundle/tests/Adapter/Elasticsearch
```

The live suite uses in-memory SQLite and unique `searchbench_test_*` indices, cleaned up afterward.
It checks missing-index population, Doctrine updates/deletes, replacement IDs, rollback recovery,
typo/prefix search, multi-select facets, range filters, sorting, facet-only requests, and escaped
highlighting. Unit tests cover failed spool claims, concurrent appends, generated-ID deletion,
cleared units of work, queue failures, bulk errors, and existing adapter behavior.

This is feature parity for lexical browsing, not identical Meili ranking. The movie feed contains
mixed-case genre labels (for example Action/action); they remain distinct source values. Browser
pagination stops at Elasticsearch's 10,000-result window; use filters to narrow larger collections.
The HTTP endpoint only exposes explicitly configured `public_searches`; arbitrary index names,
raw Elasticsearch DSL, and private named searches are rejected. It returns index document arrays
without Doctrine entity hydration. Alternative ES UI clients can reuse the named-query layer.

Next: use the bundle fixes in KPA/packages, then evaluate Folio tenant scoping and aggregated data
separately. Do not turn Folio or experimental vector parity into a prerequisite for this branch.
