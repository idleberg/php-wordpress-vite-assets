# Wordpress Vite Assets

> Adds assets from a [Vite](https://vitejs.dev/) manifest to the Wordpress head

[![Packagist](https://flat.badgen.net/packagist/license/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/v/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/php/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![CI](https://img.shields.io/github/workflow/status/idleberg/php-wordpress-vite-assets/CI?style=flat-square)](https://github.com/idleberg/php-wordpress-vite-assets/actions)


**Table of contents**

- [Wordpress Vite Assets](#wordpress-vite-assets)
	- [Installation](#installation)
	- [Usage](#usage)
		- [Methods](#methods)
			- [`inject()`](#inject)
			- [`getScriptTag()`](#getscripttag)
			- [`getStyleTags()`](#getstyletags)
			- [`getPreloadTags()`](#getpreloadtags)
		- [Options](#options)
			- [`option.crossorigin`](#optioncrossorigin)
			- [`option.integrity`](#optionintegrity)
	- [License](#license)
	
## Installation

`composer require idleberg/wordpress-vite-assets`

## Usage

To get you going, first instantiate the class exposed by this library

```php
new WordpressViteAssets(string $manifestPath, string $baseUri, string $algorithm = "sha256");
```

**Example**

```php
// functions.php

use Idleberg\WordpressViteAssets\WordpressViteAssets;

$baseUrl = get_stylesheet_directory_uri();
$manifest = "path/to/manifest.json";
$entryPoint = "index.ts";

$viteAssets = new WordpressViteAssets($manifest, $baseUrl);
$viteAssets->auto($entryPoint);
```

### Methods
#### `inject()`

Usage: `inject(array|string $entrypoints, array|int $priority = 0, string $action = null)`

Injects tags for entries specified in the manifest to the page header

- script entrypoint
- preloads for imported scripts
- style tags

The priority argument allows granular control when provided as an array

**Example**

```php
// functions.php

$priorities = [
    "scripts"  => 10,
    "preloads" => 0,
    "styles"   => 20
];

$viteAssets->inject("index.ts", $priorities);
```

The method automatically detects whether your Vite app runs in frontend or backend context, but you can override this behaviour by passing a custom action

**Example**

```php
// plugin.php

$viteAssets->inject("index.ts", 0, "admin_head");
```

:warning: In most cases, you don't need to specify a custom action!

#### `getScriptTag()`

Usage: `getScriptTag(string $entrypoint, array $options)`

Returns the script tag for an entry in the manifest

#### `getStyleTags()`

Usage: `getStyleTags(string $entrypoint, array $options)`

Returns the style tags for an entry in the manifest

#### `getPreloadTags()`

Usage: `getPreloadTags(string $entrypoint)`

Returns the preload tags for an entry in the manifest

### Options

#### `option.crossorigin`

Type: `boolean | "anonymous" | "use-credentials"`

Toggles `crossorigin` attribute on script and style tags, or assigns a value

#### `option.integrity`

Type: `boolean`

Toggles `integrity` attribute on script and style tags

## License

This work is licensed under [The MIT License](LICENSE)
