# Compare product vectors across backends

The local products page at `/demo/products/search` offers DuckDB and PostgreSQL.
Both use the same published product snapshot and OpenAI `text-embedding-3-large`
vectors with 3,072 dimensions. Switching backends retains the current search and
filters and returns to page one. Smart Search uses a configurable cosine cutoff,
with matching totals, self-excluding facets, and stable pagination.

## PostgreSQL setup

Set `PRODUCT_POSTGRES_URL` in `.env.local` to a PostgreSQL database with pgvector
installed. Enable `CREATE EXTENSION IF NOT EXISTS vector` in that database. The
named Doctrine connection `products` is separate from Bench's default database.

After publishing the local DuckDB product snapshot, run:

```sh
php bin/console app:products:postgres:import
php bin/console app:products:compare 'tech gifts'
php bin/console app:products:compare 'tech gifts' --max-price=100
php bin/console app:products:compare 'tech gifts' --category=mobile-accessories
```

Import validates canonical product text and vectors, then transactionally replaces
the PostgreSQL search projection. It is repeatable and makes no embedding API
calls. Comparisons check ranked IDs across all pages, totals, facets, and range
statistics. Query embeddings use the existing cache and may incur an API call
for an uncached phrase.

## Reuse in Elasticsearch

```sh
php bin/console elastic:index:populate product_vectors --batch-size=32
```

The document provider copies PostgreSQL's persisted full-precision embedding JSON.
It verifies the content hash, snapshot, and embedding fingerprint before export.
The fingerprint includes model, dimensions, revision, text version, and canonical
input; changed content or profiles are rejected rather than silently reusing stale
vectors. No embedding provider is invoked by this projection.

Each snapshot gets a distinct index name. The mapping stores a 3,072-dimensional
`dense_vector` with `index: false`; this is vector propagation, not yet an ES Smart
Search implementation. A healthy Elasticsearch cluster with sufficient disk
headroom is required for population. PostgreSQL and DuckDB comparison does not
depend on Elasticsearch.
