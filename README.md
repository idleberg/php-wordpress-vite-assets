# Vite Assets for WordPress

> Adds assets from a [Vite](https://vitejs.dev/) manifest to the WordPress head, supports themes and plugins.

[![License](https://img.shields.io/packagist/l/idleberg/wordpress-vite-assets?style=for-the-badge&color=blue)](https://github.com/idleberg/php-wordpress-vite-assets/blob/main/LICENSE)
[![Version](https://img.shields.io/packagist/v/idleberg/wordpress-vite-assets?style=for-the-badge)](https://github.com/idleberg/php-wordpress-vite-assets/releases)
![PHP Version](https://img.shields.io/packagist/dependency-v/idleberg/wordpress-vite-assets/php?style=for-the-badge)
[![Build](https://img.shields.io/github/actions/workflow/status/idleberg/php-wordpress-vite-assets/default.yml?style=for-the-badge)](https://github.com/idleberg/php-wordpress-vite-assets/actions)


**Table of contents**

- [Installation](#installation)
- [Usage](#usage)
	- [Methods](#methods)
		- [`inject()`](#inject)
		- [`getScriptTag()`](#getscripttag)
		- [`getStyleTags()`](#getstyletags)
		- [`getPreloadTags()`](#getpreloadtags)
	- [Options](#options)
		- [`option.action`](#optionaction)
		- [`option.crossorigin`](#optioncrossorigin)
		- [`option.integrity`](#optionintegrity)
		- [`option.priority`](#optionpriority)
- [License](#license)
	
## Installation

`composer require idleberg/wordpress-vite-assets`

## Usage

To get you going, first instantiate the class exposed by this library

```php
new Assets(string $manifestPath, string $baseUri, string $algorithm = "sha256");
```

### Parameters

#### `$manifestPath`

Type: `string`

Specifies the path to the manifest.

#### `$baseUri`

Type: `string`

Specifies the base URI for the assets in the manifest.

#### `$algorithm`

Type: `"sha256"` |`"sha384"` |`"sha512"` | `":manifest:"`  
Default: `"sha256"`  

Specifies the algorithm used for hashing the assets. This will be used for [subsource integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity) when printing script or style tags. You can use `":manifest:"` in conjunction with [vite-plugin-manifest-sri](https://github.com/ElMassimo/vite-plugin-manifest-sri), a plug-in that calculates the hashes at build-time and adds them to the manifest.

**Example**

```php
// functions.php

use Idleberg\WordPress\ViteAssets\Assets;

$baseUrl = get_stylesheet_directory_uri();
$manifest = "path/to/manifest.json";
$entryPoint = "index.ts";

$viteAssets = new Assets($manifest, $baseUrl);
$viteAssets->inject($entryPoint);
```

### Methods

#### `inject()`

Usage: `inject(array|string $entrypoints, array $options = [])`

Injects tags for entries specified in the manifest to the page header

- script entrypoint
- preloads for imported scripts
- style tags

#### `getScriptTag()`

Usage: `getScriptTag(string $entrypoint, array $options = [])`

Returns the script tag for an entry in the manifest

#### `getStyleTags()`

Usage: `getStyleTags(string $entrypoint, array $options = [])`

Returns the style tags for an entry in the manifest

#### `getPreloadTags()`

Usage: `getPreloadTags(string $entrypoint)`

Returns the preload tags for an entry in the manifest

### Options

#### `option.action`

Type: `null | string`

Allows overriding the default action for the [`inject()`](#inject) method.

**Example**

```php
// plugin.php

$viteAssets->inject("index.ts", [
	"action" => "admin_head"
]);
```

:warning: It's unlikely that you want to change the default action, so don't override unless you know what you're doing!

#### `option.crossorigin`

Type: `boolean | "anonymous" | "use-credentials"`

Toggles `crossorigin` attribute on script and style tags, or assigns a value

#### `option.integrity`

Type: `boolean`

Toggles `integrity` attribute on script and style tags

#### `option.priority`

Type: `int | array`

Allows overriding the priority for the [`inject()`](#inject) method. It allows granular control when provided as an array:

**Example**

```php
// functions.php

$viteAssets->inject("index.ts", [
	"priority" => [
		"scripts"  => 10,
		"preloads" => 0,
		"styles"   => 20
	]
]);
```

## License

This work is licensed under [The MIT License](LICENSE).
