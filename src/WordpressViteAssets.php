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
		"action" => null,
		"crossorigin" => true,
		"integrity" => true,
		"priority" => 0
	];

	public function __construct(string $manifestFile, string $basePath, string $algorithm = "sha256")
	{
		// Let ViteManifest handle errors
		$this->vm = new ViteManifest($manifestFile, $basePath, $algorithm);
	}

	/**
	 * Injects tags for entries specified in the manifest to the page header
	 *
	 * @param array|string $entrypoint
	 * @param array $customOptions (optional)
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
			foreach ($entries as $entry) {
				$scriptTag = $this->getScriptTag($entry, $options);

				if ($scriptTag) {
					echo $scriptTag . PHP_EOL;
				}
			}
		}, $this->getPriority($priority, "scripts"), 1);

		add_action($action, function () use ($entries) {
			foreach ($entries as $entry) {
				foreach ($this->getPreloadTags($entry) as $preloadTag) {
					echo $preloadTag . PHP_EOL;
				}
			}
		}, $this->getPriority($priority, "preloads"), 1);

		add_action($action, function () use ($entries, $options) {
			foreach ($entries as $entry) {
				foreach ($this->getStyleTags($entry, $options) as $styleTag) {
					echo $styleTag . PHP_EOL;
				}
			}
		}, $this->getPriority($priority, "styles"), 1);
	}

	/**
	 * Returns the script tag for an entry in the manifest
	 *
	 * @param string $entrypoint
	 * @param array $customOptions (optional)
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

		$defaultAttributes = [
			"type=\"module\"",
			"src=\"{$url['url']}\""
		];

		return "<script {$this->getAttributes($url,$defaultAttributes,$options)}></script>";
	}

	/**
	 * Returns the style tags for an entry in the manifest
	 *
	 * @param string $entrypoint
	 * @param array $customOptions (optional)
	 * @return array
	 */
	public function getStyleTags(string $entrypoint, array $customOptions = []): array
	{
		$options = $this->mergeOptions($customOptions);
		$hash = $options["integrity"] ?? true;

		return array_map(function ($url) use ($options) {
			$defaultAttributes = [
				"rel=\"stylesheet\"",
				"href=\"{$url['url']}\""
			];
			return "<link {$this->getAttributes($url,$defaultAttributes,$options)} />";
		}, $this->vm->getStyles($entrypoint, $hash));
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
			return "<link rel=\"modulepreload\" href=\"{$import['url']}\" />";
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
	 * @param array $attributes
	 * @param array $customOptions
	 * @return array
	 */
	private function getAttributes($url, array $attributes, array $customOptions)
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
	 * Merges custom options with defaults
	 *
	 * @param array $options (optional)
	 * @return array
	 */
	private function mergeOptions(array $options = [])
	{
		return array_merge(
			$this->defaultOptions,
			$options
		);
	}
}
