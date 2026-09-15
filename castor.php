<?php

use Castor\Attribute\AsTask;
use Survos\MeiliBundle\Model\Dataset;

use function Castor\{io, run, capture, import, http_download};

//import('src/Command/LoadCongressCommand.php');
//import('src/Command/LoadDummyCommand.php');
//import('src/Command/JeopardyCommand.php');

//try {
//    import('.castor/vendor/tacman/castor-tools/castor.php');
//} catch (Throwable $e) {
//    io()->error($e->getMessage());
//}

$autoloadCandidates = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];
foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) {
        require_once $autoload;
        break;
    }
}


#[AsTask('congress:details', description: 'Fetch details from wikipedia')]
function congress_details(): void
{
    run('bin/console state:iterate Official --marking=new --transition=fetch_wiki');
    run('bin/console mess:stats');
    io()->writeln('make sure the message consumer is running');
}

#[AsTask('load:marvel', description: 'Fetch details from wikipedia')]
function marvel(): void
{
    run(sprintf('bin/console import:convert %s --output=%s --zip-path=marvel-search-master/records', 'zip/marvel.zip', 'data/marvel.jsonl'));
    $importCmd = sprintf(
        'bin/console import:entities App\\\\Entity\\\\Marvel data/marvel.jsonl',
    );
    run($importCmd);

}

/**
 * Return the available demo datasets, keyed by code.
 *
 * @return array<string, Dataset>
 */
function demo_datasets(): array
{

        $datasets = [
            new Dataset(
                name: 'wcma',
                url: 'https://github.com/wcmaart/collection/raw/refs/heads/master/wcma-collection.csv',
                target: 'data/wcma.csv',
            ),
            new Dataset(
                name: 'amst_en',
                url: 'https://statics.belowthesurface.amsterdam/downloadbare-datasets/Downloadtabel_EN.csv',
                target: 'data/amst_en.csv',
            ),
            // amst_nl intentionally dropped: it's the same Amst entity as amst_en, just a
            // second, Dutch-language copy of the same records. Folio's proper multi-lingual
            // support is the right way to model one dataset in multiple languages -- adding
            // a second flat dataset per locale doesn't scale past two, and English-only is a
            // fine default for this bench. Revisit if/when Amst moves onto folio.
            new Dataset(
                name: 'car',
                url: 'https://corgis-edu.github.io/corgis/datasets/csv/cars/cars.csv',
                target: 'data/cars.csv',
                jsonl: 'data/car.jsonl',
            ),
            new Dataset(
                name: 'wine',
                url: 'https://github.com/algolia/datasets/raw/refs/heads/master/wine/bordeaux.json',
                target: 'data/wine.json',
            ),
            new Dataset(
                name: 'movie',
                url: 'https://github.com/metarank/msrd/raw/master/dataset/movies.csv.gz',
                target: 'data/movies.csv.gz',
                jsonl: 'data/movie.jsonl',
                afterDownload: 'gunzip data/movies.csv.gz -k'
            ),
            new Dataset(
                name: 'marvel',
                url: 'https://github.com/algolia/marvel-search/archive/refs/heads/master.zip',
                target: 'zip/marvel.zip',
                jsonl: 'data/marvel.jsonl', // output of convert/import target
            ),
            new Dataset(
                name: 'wam',
                url: null, // downloaded / prepared manually or via separate script
                target: 'data/wam-dywer.csv',
                jsonl: 'data/wam.jsonl'
            )
        ];
    foreach ($datasets as $dataset) {
        $map[$dataset->name] = $dataset;
    }
    return $map;
}

function wam(Dataset $dataset)
{
    $zipPath = 'zip/wam.zip';
    if (!file_exists($zipPath)) {
        throw new \RuntimeException(sprintf('WAM zip not found at %s', $zipPath));
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) === true) {
        io()->writeln('Unzipping wam.zip');
        $destDir = __DIR__ . '/data/';
        if (!\is_dir($destDir)) {
            \mkdir($destDir, 0777, true);
        }
        $zip->extractTo($destDir, 'wam-dywer.csv');
        $zip->close();
        io()->writeln('WAM CSV was extracted to ' . realpath($dataset->target));
    } else {
        throw new \RuntimeException('Failed to open WAM ZIP file at ' . $zipPath);
    }

}

#[AsTask('download')]
function download(?string $code=null): void
{


    $map = demo_datasets();
    if ($code && !array_key_exists($code, $map)) {
        io()->error("The code '{$code}' does not exist: " . implode('|', array_keys($map)));
        return;
    }
    $datasets = $code ? [$map[$code]] : array_values($map);
    foreach ($datasets as $dataset) {
        if ($dataset->name === 'wam') {
            wam($dataset);
            continue;
        }
        // use fs()?
        if ($dataset->url) {
            if (!file_exists($dataset->target)) {
                $dir = \dirname($dataset->target);
                if ($dir !== '' && !\is_dir($dir)) {
                    \mkdir($dir, 0777, true);
                }

                io()->writeln(sprintf('Downloading %s → %s', $dataset->url, $dataset->target));
                http_download($dataset->url, $dataset->target);
                io()->writeln(realpath($dataset->target) . ' written');
                if ($dataset->afterDownload) {
                    io()->warning($dataset->afterDownload);
                    run($dataset->afterDownload);
                }

            } else {
                io()->writeln(sprintf('Target %s already exists, skipping download.', $dataset->target));
            }
        }
    }
}

/**
 * Runs load_database() for every dataset in demo_datasets(), one at a time.
 * Best-effort: a dataset that fails (missing zip, bad download, whatever) is
 * reported and skipped rather than aborting the whole run -- these are
 * independent network/import operations, not a single transaction.
 */
#[AsTask('load:all', description: 'Loads every demo dataset')]
function load_all_databases(
    #[Opt(description: 'Limit number of entities to import, per dataset')]
    ?int $limit = null,
    #[\Castor\Attribute\AsOption(description: 'Reset each database before importing')] bool $reset = false
): void {
    $map = demo_datasets();
    $failed = [];

    foreach (array_keys($map) as $code) {
        io()->section("Loading dataset: {$code}");

        try {
            load_database($code, $limit, $reset);
        } catch (\Throwable $e) {
            io()->error("{$code} failed: " . $e->getMessage());
            $failed[] = $code;
        }
    }

    $total = count($map);
    $okCount = $total - count($failed);
    io()->writeln("");
    if ($failed) {
        io()->warning(sprintf('%d/%d datasets loaded; failed: %s', $okCount, $total, implode(', ', $failed)));
    } else {
        io()->success(sprintf('All %d datasets loaded.', $total));
    }
}

/**
 * Loads the database for a given demo dataset:
 *   - downloads the raw dataset (if needed)
 *   - runs import:convert to produce JSONL + profile
 *   - runs import:entities to import into Doctrine
 *
 * Code generation (code:entity) remains a separate, explicit step.
 */
#[AsTask('load', description: 'Loads the database for a demo dataset')]
function load_database(
    #[\Castor\Attribute\AsArgument(description: 'Dataset code (e.g. wcma|car|wine|marvel|wam)')]
    string $code = '',
    #[Opt(description: 'Limit number of entities to import')]
    ?int $limit = null,
    #[\Castor\Attribute\AsOption(description: "reset the database")] bool $reset = false
): void {
    /** @var array<string, Dataset> $map */
    $map = demo_datasets();

    if ($code === '') {
        io()->writeln('Available dataset codes:');
        foreach ($map as $k => $dataset) {
            io()->writeln(sprintf('  - %s (%s)', $k, $dataset->target));
        }

        return;
    }

    if (!\array_key_exists($code, $map)) {
        io()->error("The code '{$code}' does not exist: " . implode('|', array_keys($map)));

        return;
    }

    $dataset = $map[$code];

    // 1) Ensure raw data exists (download if URL is defined)
    if ($dataset->url) {
        if (!file_exists($dataset->target)) {
            $dir = \dirname($dataset->target);
            if ($dir !== '' && !\is_dir($dir)) {
                \mkdir($dir, 0777, true);
            }

            io()->writeln(sprintf('Downloading %s → %s', $dataset->url, $dataset->target));
            http_download($dataset->url, $dataset->target);
            io()->writeln(realpath($dataset->target) . ' written');
        } else {
            io()->writeln(sprintf('Target %s already exists, skipping download.', $dataset->target));
        }
    }

    // 2) WAM special case: extract CSV from zip if needed.
    //
    // The original WAM flow:
    //   - zip/wam.zip holds wam-dywer.csv
    //   - wam-dywer.csv has been pre-cleaned (header fixes, etc.)
    //
    // We keep that behavior but line it up with Dataset::target ("data/wam-dywer.csv").
    if ($code === 'wam' && !file_exists($dataset->target)) {
        $zipPath = 'zip/wam.zip';
        if (!file_exists($zipPath)) {
            throw new \RuntimeException(sprintf('WAM zip not found at %s', $zipPath));
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) === true) {
            io()->writeln('Unzipping wam.zip');
            $destDir = __DIR__ . '/data/';
            if (!\is_dir($destDir)) {
                \mkdir($destDir, 0777, true);
            }
            $zip->extractTo($destDir, 'wam-dywer.csv');
            $zip->close();
            io()->writeln('WAM CSV was extracted to ' . realpath($dataset->target));
        } else {
            throw new \RuntimeException('Failed to open WAM ZIP file at ' . $zipPath);
        }
    }

    // 3) Convert + profile using the new import:convert pipeline.
    //
    // This will:
    //   - write JSONL to $dataset->jsonl (or derived from target)
    //   - write profile JSON alongside it
    if (!$dataset->jsonl) {
        io()->warning("stopped, no jsonl, maybe run another command?");
        return;
    }
    $cmd = 'bin/console import:convert %s --dataset=%s';
    $convertCmd = sprintf(
        $cmd,
        $dataset->target,
        $dataset->name
    );
    io()->writeln($convertCmd);
    run($convertCmd);

    // 4) Import entities into Doctrine.
    //
    // Note: This assumes your entity class is App\Entity\<Code>, e.g. App\Entity\Car
    // and that you've already generated the entity via code:entity.
    $limitArg = $limit ? sprintf(' --limit=%d', $limit) : '';
    $cmd = 'bin/console import:entities %s %s%s';
    if ($limit) {
        $cmd .= ' --limit ' . $limit;
    }
    if ($reset) {
        $cmd .= ' --reset';
    }
    $importCmd = sprintf(
        $cmd,
        entity_class_for_code($code),
        $dataset->jsonl,
        $limitArg
    );
    io()->writeln($importCmd);
    run($importCmd);
}

/**
 * Dataset code -> short entity class name. Defaults to ucfirst($code), which
 * covers everything except the locale-suffixed 'amst_en' -- ucfirst('amst_en')
 * is 'Amst_en', which doesn't exist; the entity is just 'Amst'.
 */
function entity_class_for_code(string $code): string
{
    return match ($code) {
        'amst_en' => 'Amst',
        default => ucfirst($code),
    };
}

#[AsTask('es:prepare-cars', description: 'Assign repeatable row IDs to the converted CORGIS cars dataset')]
function es_prepare_cars(): void
{
    // Identification.ID has duplicates. Preserve every source row, including variants.
    $writer = \Survos\JsonlBundle\IO\JsonlWriter::open('data/car-import.jsonl', 'w');
    try {
        $id = 0;
        foreach (\Survos\JsonlBundle\IO\JsonlReader::open('data/car.jsonl') as $row) {
            $row['id'] = ++$id;
            $writer->write($row);
        }
    } finally {
        $writer->close();
    }
    io()->success(sprintf('Prepared %d cars in data/car-import.jsonl', $id));
}

#[AsTask('es:load', description: 'Restore one lexical demo dataset and rebuild its Elasticsearch index')]
function es_load(string $code = 'movie'): void
{
    if (!in_array($code, ['movie', 'car', 'marvel', 'wcma'], true)) {
        throw new InvalidArgumentException('Use movie, car, marvel, or wcma.');
    }
    $dataset = demo_datasets()[$code];
    if (!is_file($dataset->target)) { download($code); }
    if ($code === 'movie' && !is_file('data/movies.csv')) { run(['gunzip', '-k', $dataset->target]); }
    $input = $code === 'movie' ? 'data/movies.csv' : $dataset->target;
    $output = 'data/'.$code.'.jsonl';
    $command = ['php', 'bin/console', 'import:convert', $input, '--output='.$output, '--no-debug', '-n'];
    if ($code === 'marvel') { $command[] = '--zip-path=marvel-search-master/records'; }
    run($command);
    if ($code === 'car') { es_prepare_cars(); $output = 'data/car-import.jsonl'; }
    run(['php', 'bin/console', 'import:entities', ucfirst($code), $output, '--no-debug', '-n']);
    run(['php', 'bin/console', 'elastic:index:rebuild', 'app_'.$code, '--no-debug', '-n']);
}
