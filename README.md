# SearchBench

A Symfony-based testbed for search and AI-context techniques — built on **Meilisearch**,
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
- Symfony **Workflows & Messenger** for async data processing
- Ready-made **search and listing UIs** (Datatables, filters, facets)
- A foundation for experimenting with **custom Meilisearch indices**
- A small, well-understood dataset (wine, 1,024 rows) for testing RAG/AI-context approaches
  before trying them against larger, messier production collections

## Requirements

* PHP 8.4 with Sqlite
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

## Adding New Datasets

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
