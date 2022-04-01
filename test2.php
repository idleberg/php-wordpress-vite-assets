<?php

include __DIR__ . '/vendor/autoload.php';

use Idleberg\WordpressViteAssets\WordpressViteAssets;

$baseUrl = __DIR__ . "/tests/_data/";
$manifest = __DIR__ . "/tests/_data/manifest.json";

$viteAssets = new WordpressViteAssets($manifest, $baseUrl);

// foreach ($viteAssets->getStyleTags("demo.ts") as $actual) {
//     echo $actual;
// }

foreach ($viteAssets->getPreloadTags("demo.ts") as $actual) {
    echo $actual;
}
