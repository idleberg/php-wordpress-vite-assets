<?php

/**
 * Copyright 2022-2025 Jan T. Sott & contributors
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

namespace Idleberg\WordPress\ViteAssets;

use Idleberg\ViteManifest\Manifest;

class Assets
{
    private Manifest $vm;

    /** @var array{action: string|null, crossorigin: bool|string, integrity: bool, priority: int|array<string, int>} */
    private array $defaultOptions = [
        "action" => null,
        "crossorigin" => true,
        "integrity" => true,
        "priority" => 0
    ];

    public function __construct(string $manifestFile, string $basePath, string $algorithm = "sha256")
    {
        // Let ViteManifest handle errors
        $this->vm = new Manifest($manifestFile, $basePath, $algorithm);
    }

    /**
     * Injects tags for entries specified in the manifest to the page header.
     *
     * @param array<string>|string $entrypoint
     * @param array<string, mixed> $customOptions (optional)
     * @return void
     */
    public function inject(array|string $entrypoint, array $customOptions = []): void
    {
        if (!function_exists('add_action')) {
            throw new \Exception("WordPress function add_action() not found");
        }

        $options = $this->mergeOptions($customOptions);
        ["action" => $action, "priority" => $priority] = $options;

        if ($action === null || !is_string($action)) {
            $action = is_admin() ? 'admin_head' : 'wp_head';
        }

        if (!has_action($action)) {
            throw new \Exception("The hook '$action' could not be found");
        }

        $entries = is_array($entrypoint) ? $entrypoint : [$entrypoint];

        add_action($action, function () use ($entries, $options) {
            array_map(function ($entry) use ($options) {
                $tag = $this->isStylesheet($entry)
                    ? array_key_first($this->getStyleTags($entry, $options))
                    : $this->getScriptTag($entry, $options);

                if ($tag) {
                    echo $tag . PHP_EOL;
                }
            }, $entries);
        }, $this->getPriority($priority, "scripts"), 1);

        add_action($action, function () use ($entries) {
            array_map(function ($entry) {
                array_map(function ($preloadTag) {
                    echo $preloadTag . PHP_EOL;
                }, $this->getPreloadTags($entry));
            }, $entries);
        }, $this->getPriority($priority, "preloads"), 1);

        add_action($action, function () use ($entries, $options) {
            array_map(function ($entry) use ($options) {
                array_map(function ($styleTag) {
                    echo $styleTag . PHP_EOL;
                }, $this->getStyleTags($entry, $options));
            }, $entries);
        }, $this->getPriority($priority, "styles"), 1);
    }

    /**
     * Returns the script tag for an entry in the manifest.
     *
     * @param string $entrypoint
     * @param array<string, mixed> $customOptions (optional)
     * @return string
     */
    public function getScriptTag(string $entrypoint, array $customOptions = []): string
    {
        $options = $this->mergeOptions($customOptions);
        $hash = $options["integrity"] ?? true;
        $url = $this->vm->getEntrypoint($entrypoint, $hash);

        if (!$url) {
            return "";
        }

        $escapedUrl = $this->escapeUrl($url['url']);

        $defaultAttributes = [
            "type=\"module\"",
            "src=\"{$escapedUrl}\""
        ];

        return "<script {$this->getAttributes($url, $defaultAttributes, $options)}></script>";
    }

    /**
     * Returns the style tags for an entry in the manifest.
     *
     * @param string $entrypoint
     * @param array<string, mixed> $customOptions (optional)
     * @return array<string>
     */
    public function getStyleTags(string $entrypoint, array $customOptions = []): array
    {
        $options = $this->mergeOptions($customOptions);
        $hash = $options["integrity"] ?? true;

        return array_map(function ($url) use ($options) {
            $escapedUrl = $this->escapeUrl($url['url']);

            $defaultAttributes = [
                "rel=\"stylesheet\"",
                "href=\"{$escapedUrl}\""
            ];

            return "<link {$this->getAttributes($url, $defaultAttributes, $options)} />";
        }, $this->vm->getStyles($entrypoint, $hash));
    }

    /**
     * Returns the preload tags for an entry in the manifest.
     *
     * @param string $entry
     * @return array<string>
     */
    public function getPreloadTags(string $entry): array
    {
        return array_map(function ($import) {
            $escapedUrl = $this->escapeUrl($import['url']);

            return "<link rel=\"modulepreload\" href=\"{$escapedUrl}\" />";
        }, $this->vm->getImports($entry));
    }

    /**
     * Returns priority for an action.
     *
     * @param array<string, int>|int $priority
     * @param string $key
     * @return int
     */
    private function getPriority(array|int $priority, string $key): int
    {
        if (is_integer($priority)) {
            return $priority;
        }

        return $priority[$key] ?? 0;
    }

    /**
     * Returns optional attributes for script or link tags.
     *
     * @param array<string, string> $url
     * @param array<string> $attributes
     * @param array<string, mixed> $customOptions
     * @return string
     */
    private function getAttributes(array $url, array $attributes, array $customOptions): string
    {
        ["crossorigin" => $crossorigin, "integrity" => $integrity] = $this->mergeOptions($customOptions);

        if ($crossorigin === true) {
            $attributes[] = "crossorigin";
        } elseif (in_array($crossorigin, ["anonymous", "use-credentials"])) {
            $attributes[] = "crossorigin=\"{$crossorigin}\"";
        }

        if ($integrity === true) {
            $attributes[] = "integrity=\"{$url['hash']}\"";
        }

        return join(" ", $attributes);
    }

    /**
     * Merges custom options with defaults.
     *
     * @param array<string, mixed> $options (optional)
     * @return array<string, mixed>
     */
    private function mergeOptions(array $options = []): array
    {
        return array_merge(
            $this->defaultOptions,
            $options
        );
    }

    /**
     * Determines whether a file is a stylesheet based on its extension.
     *
     * @param string $entry
     * @return bool
     */
    private function isStylesheet(string $entry): bool
    {
        $styleSheets = ['css', 'less', 'scss', 'styl'];

        return in_array(pathinfo($entry, PATHINFO_EXTENSION), $styleSheets);
    }

    private function escapeUrl(string $url): string
    {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}
