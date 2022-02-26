# Wordpress Vite Assets

> Adds assets from a [Vite](https://vitejs.dev/) manifest to the Wordpress head

[![Packagist](https://flat.badgen.net/packagist/license/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/php/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/v/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)

## Installation

`composer require idleberg/wordpress-vite-assets`

## Usage

To get you going, first instantiate the class exposed by this library

Usage: `new WordpressViteAssets(string $manifestPath, string $baseUri)`

**Example**

```php
// functions.php

use Idleberg\WordpressViteAssets\WordpressViteAssets;

$baseUrl = get_stylesheet_directory();
$manifest = $baseUrl . "/manifest.json";
$entryPoint = "index.ts";

$viteAssets = new WordpressViteAssets($manifest);
$viteAssets->addAction($entryPoint, $baseUrl);
```

### Methods

#### `addAction`

Usage: `addAction(array|string $entrypoints, array|int $priority = 0)`

Writes tags for entries specified in the manifest to the page header

- script entrypoint
- preloads for imported scripts
- style tags

The priority argument allows granular control when provided as an array

**Example**

```php
$priorities = [
    "scripts" => 10,
    "preloads" => 0,
    "styles" => 20
];

$viteAssets->addAction($entrypoints, $priorities);
```

#### `getScriptTag`

Usage: `getScriptTag(string $entrypoint)`

Returns the script tag for an entry in the manifest

#### `getStyleTags`

Usage: `getStyleTags(string $entrypoint)`

Returns the style tags for an entry in the manifest

#### `getPreloadTags`

Usage: `getPreloadTags(string $entrypoint)`

Returns the preload tags for an entry in the manifest

## License

This work is licensed under [The MIT License](LICENSE)
