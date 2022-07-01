# Wordpress Vite Assets

> Adds assets from a [Vite](https://vitejs.dev/) manifest to the Wordpress head

[![Packagist](https://flat.badgen.net/packagist/license/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/php/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/v/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![CI](https://img.shields.io/github/workflow/status/idleberg/php-wordpress-vite-assets/CI?style=flat-square)](https://github.com/idleberg/php-wordpress-vite-assets/actions)

## Installation

`composer require idleberg/wordpress-vite-assets`

## Usage

To get you going, first instantiate the class exposed by this library

Usage: `new WordpressViteAssets(string $manifestPath, string $baseUri, string $algorithm = "sha256")`

**Example**

```php
// functions.php

use Idleberg\WordpressViteAssets\WordpressViteAssets;

$baseUrl = get_stylesheet_directory();
$manifest = "path/to/manifest.json";
$entryPoint = "index.ts";

$viteAssets = new WordpressViteAssets($manifest, $baseUrl);
$viteAssets->addAction($entryPoint);
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
    "scripts"  => 10,
    "preloads" => 0,
    "styles"   => 20
];

$viteAssets->addAction($entrypoints, $priorities);
```

#### `getScriptTag`

Usage: `getScriptTag(string $entrypoint, bool $options)`

Returns the script tag for an entry in the manifest

#### `getStyleTags, bool $options`

Usage: `getStyleTags(string $entrypoint)`

Returns the style tags for an entry in the manifest

#### `getPreloadTags`

Usage: `getPreloadTags(string $entrypoint)`

Returns the preload tags for an entry in the manifest

### Options

#### crossorigin

Toggles `crossorigin` attribute on script and style tags. Can be `boolean`, `"anonymous"` or `"use-credentials"`

#### integrity

Toggles `integrity` attribute on script and style tags

## License

This work is licensed under [The MIT License](LICENSE)
