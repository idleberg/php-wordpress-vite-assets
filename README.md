# Wordpress Vite Assets

> Adds assets from a [Vite](https://vitejs.dev/) manifest to the Wordpress head

[![Packagist](https://flat.badgen.net/packagist/license/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)
[![Packagist](https://flat.badgen.net/packagist/v/idleberg/wordpress-vite-assets)](https://packagist.org/packages/idleberg/wordpress-vite-assets)

## Installation

`composer require idleberg/wordpress-vite-assets`

## Usage

```php
use Idleberg\WordpressViteAssets\WordpressViteAssets;

$viteAssets = new WordpressViteAssets("path/to/manifest.json");
$viteAssets->addAction();
```

### Methods

#### `addAction`

Usage: `addAction(array|string $entries, int $priority = 0)`

Writes tags for entries specified in the manifest to the page header

- script entrypoint
- preloads for imported scripts
- style tags

#### `getScriptTag`

Usage: `getScriptTag(string $fileName)`

Returns the script tag for an entry in the manifest

#### `getStyleTags`

Usage: `getStyleTags(string $fileName)`

Returns the style tags for an entry in the manifest

#### `getPreloadTags`

Usage: `getPreloadTags(string $fileName)`

Returns the preload tags for an entry in the manifest

## License

This work is licensed under [The MIT License](LICENSE)
