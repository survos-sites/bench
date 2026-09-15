# SearchBench Elasticsearch evaluation

## Decision — 2026-09-15

SearchBench is the principal integration testcase for improving SearchBundle's ES support.
Preserve all existing adapters. Apps select a default engine and may override it per named
search/dataset; multiple engines are allowed, normally over distinct datasets. Comparison
fixtures may intentionally index the same data twice. Do not retire Meili for purity.

See [baseline and evaluation plan](docs/search-baseline.md) and
[the shared bundle decision](https://github.com/survos/mono/issues/53).

## Sequence

- [x] Review the existing Bench search paths and record baseline limitations.
- [x] Publish the current source baseline as GitHub release `search-baseline-2026-09-15`
  before further ES changes (source checkpoint with known test limitations).
- [x] Inventory Mac data and tools: raw inputs present, Symfony/Castor boot, local SQLite
  only partly populated. Restore conversions/imports before re-fetching existing inputs.
- [x] Repair baseline test infrastructure and capture repeatable corpus/query fixtures.
- [x] Validate Movie through SearchBundle/ES: text, typo/prefix behavior, array facets,
  numeric ranges, sort, paging, highlights, and detail links.
- [x] Restore and index Car, Marvel, and WCMA; verify the shared browse UI.
- [x] Implement reusable fixes in the bundles; preserve direct Meili demonstrations.
- [ ] Apply lessons to KPA/packages and other consumers.
- [ ] Follow with a separate Folio ES evaluation, including tenant scope and aggregation.

The baseline review is preserved in the release. Implementation and setup details are in
[Elasticsearch evaluation](docs/elasticsearch.md). KPA/packages and Folio remain follow-up work.
