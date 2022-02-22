<?php

/**
 * Copyright 2022 Jan T. Sott
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

namespace Idleberg\WordpressViteAssets;

use Idleberg\ViteManifest\ViteManifest;

class WordpressViteAssets
{
    private $vm;

    public function __construct(string $manifestFile, string $basePath)
    {
        $this->vm = new ViteManifest($manifestFile, $basePath);
    }

    /**
     * Writes tags for entries specified in the manifest to the page header
     *
     * @param array|string $entry
     * @return void
     */
    public function addAction(array|string $entries, int $priority = 0): void
    {
        if (!function_exists('add_action')) {
            throw new \Exception("WordPress function add_action() not found");
        }

        $entries = is_array($entries) ? $entries : [$entries];

        add_action('wp_head', function() use ($entries) {
            foreach($entries as $entry) {
                $scriptTag = $this->getScriptTag($entry);

                if ($scriptTag) {
                    echo $scriptTag . PHP_EOL;
                }

                foreach($this->getPreloadTags($entry) as $preloadTag) {
                    echo $preloadTag . PHP_EOL;
                }

                foreach($this->getStyleTags($entry) as $styleTag) {
                    echo $styleTag . PHP_EOL;
                }
            }
        }, $priority, 1);
    }

    /**
     * Returns the script tag for an entry in the manifest
     *
     * @param string $entry
     * @return string
     */
    public function getScriptTag(string $entry): string
    {
        $url = $this->vm->getEntrypoint($entry);

        if (!$url) {
            return null;
        }

        return "<script type=\"module\" src=\"{$url['url']}\" crossorigin integrity=\"{$url['hash']}\"></script>";
    }

    /**
     * Returns the style tags for an entry in the manifest
     *
     * @param string $entry
     * @return array
     */
    public function getStyleTags(string $entry): array
    {
        return array_map(function($url) {
            return "<link rel=\"stylesheet\" href=\"{$url['url']}\" crossorigin integrity=\"{$url['hash']}\" />";
        }, $this->vm->getStyles($entry));
    }

    /**
     * Returns the preload tags for an entry in the manifest
     *
     * @param string $entry
     * @return array
     */
    public function getPreloadTags(string $entry): array
    {
        return array_map(function($import) {
            return "<link rel=\"modulepreload\" href=\"{$import['url']}\">";
        }, $this->vm->getImports($entry));
    }
}
