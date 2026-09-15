#!/usr/bin/env php
<?php

declare(strict_types=1);

// Development bridge until the ES changes are available as released Composer packages.
$project = dirname(__DIR__);
$mono = realpath($argv[1] ?? dirname($project).'/mono');
if ($mono === false || !is_file($mono.'/bu/search-bundle/src/Http/InstantSearchGateway.php')) {
    throw new RuntimeException('Pass the path to a checkout of survos/mono on its es branch.');
}
foreach (['search-bundle', 'elastic-bundle'] as $bundle) {
    $source = $mono.'/bu/'.$bundle;
    $target = $project.'/vendor/survos/'.$bundle;
    if (is_link($target) && realpath($target) === $source) { continue; }
    if (file_exists($target) || is_link($target)) {
        $backup = $project.'/var/local-bundle-backup/'.date('YmdHis').'-'.bin2hex(random_bytes(3));
        mkdir($backup, 0775, true);
        if (!rename($target, $backup.'/'.$bundle)) { throw new RuntimeException('Could not back up '.$target); }
    }
    if (!symlink($source, $target)) { throw new RuntimeException('Could not link '.$target); }
    echo $bundle.' → '.$source.PHP_EOL;
}
