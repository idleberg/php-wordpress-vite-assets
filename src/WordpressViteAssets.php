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
    private $defaultOptions = [
        "crossorigin" => true,
        "integrity" => true
    ];

    public function __construct(string $manifestFile, string $basePath)
    {
        $this->vm = new ViteManifest($manifestFile, $basePath);
    }

    /**
     * Writes tags for entries specified in the manifest to the page header
     *
     * @param array|string $entrypoint
     * @return void
     */
    public function addAction(string $entrypoint, int $priority = 0): void
    {
        if (!function_exists('add_action')) {
            throw new \Exception("WordPress function add_action() not found");
        }

        $entries = is_array($entrypoint) ? $entrypoint : [$entrypoint];

        add_action('wp_head', function () use ($entries) {
            foreach ($entries as $entry) {
                $scriptTag = $this->getScriptTag($entry);

                if ($scriptTag) {
                    echo $scriptTag . PHP_EOL;
                }
            }
        }, $this->getPriority($priority, "scripts"), 1);

        add_action('wp_head', function () use ($entries) {
            foreach ($entries as $entry) {
                foreach ($this->getPreloadTags($entry) as $preloadTag) {
                    echo $preloadTag . PHP_EOL;
                }
            }
        }, $this->getPriority($priority, "preloads"), 1);

        add_action('wp_head', function () use ($entries) {
            foreach ($entries as $entry) {
                foreach ($this->getStyleTags($entry) as $styleTag) {
                    echo $styleTag . PHP_EOL;
                }
            }
        }, $this->getPriority($priority, "styles"), 1);
    }

    /**
     * Returns the script tag for an entry in the manifest
     *
     * @param string $entrypoint
     * @param array $options
     * @return string
     */
    public function getScriptTag(string $entrypoint, array $options = []): string
    {
        $url = $this->vm->getEntrypoint($entrypoint);

        if (!$url) {
            return null;
        }

        return "<script type=\"module\" src=\"{$url['url']}\" {$this->getAttributes($url, $options)}></script>";
    }

    /**
     * Returns the style tags for an entry in the manifest
     *
     * @param string $entrypoint
     * @param array $options
     * @return array
     */
    public function getStyleTags(string $entrypoint, array $options = []): array
    {
        return array_map(function ($url) use ($options) {
            return "<link rel=\"stylesheet\" href=\"{$url['url']}\" {$this->getAttributes($url, $options)} />";
        }, $this->vm->getStyles($entrypoint));
    }

    /**
     * Returns the preload tags for an entry in the manifest
     *
     * @param string $entry
     * @return array
     */
    public function getPreloadTags(string $entry): array
    {
        return array_map(function ($import) {
            return "<link rel=\"modulepreload\" href=\"{$import['url']}\">";
        }, $this->vm->getImports($entry));
    }

    /**
     * Returns priority for an action
     *
     * @param array|int $priority
     * @param string $key
     * @return int
     */
    private function getPriority(int $priority, string $key)
    {
        switch (true) {
            case is_integer($priority):
                return $priority;

            case is_array($priority) && is_integer($priority[$key]):
                return $priority[$key];

            default:
                return 0;
        }
    }

    /**
    * Returns optional attribues for script or link tags
    *
    * @param string $url
    * @param array $options
    * @return array
    */
    private function getAttributes($url, array $options)
    {
        ["crossorigin" => $crossorigin, "integrity" => $integrity] = [
            ...$this->defaultOptions,
            ...$options
        ];

        $attributes = [];

        if ($crossorigin === true) {
            $attributes[] = "crossorigin";
        } else if (in_array($crossorigin, ["anonymous", "use-credentials"])) {
            $attributes[] = "crossorigin=\"{$crossorigin}\"";
        }

        if($integrity === true) {
            $attributes[] = "integrity=\"{$url['hash']}\"";
        }

        return join(" ", $attributes);
    }
}
