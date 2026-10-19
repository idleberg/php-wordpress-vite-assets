<?php

use Idleberg\WordPress\ViteAssets\Assets;

// Minimal WordPress stubs: add_action() collects callbacks, do_action() runs them
if (!function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        $GLOBALS['wp_actions'][$hook][$priority][] = $callback;
    }

    function has_action(string $hook): bool
    {
        return true;
    }

    function is_admin(): bool
    {
        return false;
    }

    function do_action(string $hook): void
    {
        $actions = $GLOBALS['wp_actions'][$hook] ?? [];
        ksort($actions);

        foreach ($actions as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback();
            }
        }
    }
}

class InjectTest extends \Codeception\Test\Unit
{
    protected $manifest = __DIR__ . "/../_data/manifest.json";

    protected function _before()
    {
        $GLOBALS['wp_actions'] = [];
    }

    private function render(array|string $entrypoint): string
    {
        (new Assets($this->manifest, "https://example.invalid/"))->inject($entrypoint, ["integrity" => false]);

        ob_start();
        do_action('wp_head');

        return (string) ob_get_clean();
    }

    // tests
    public function testInjectPreprocessedStylesheetEntry()
    {
        $actual = $this->render("demo.sass");
        $expected = "<link rel=\"stylesheet\" href=\"https://example.invalid/assets/demo.deadbeef.css\" crossorigin />" . PHP_EOL;

        $this->assertSame($expected, $actual);
    }

    public function testInjectDeduplicatesSharedTags()
    {
        $actual = $this->render(["demo.ts", "demo2.ts"]);
        $expected = implode(PHP_EOL, [
            "<script type=\"module\" src=\"https://example.invalid/assets/index.deadbeef.js\" crossorigin></script>",
            "<script type=\"module\" src=\"https://example.invalid/assets/demo2.deadbeef.js\" crossorigin></script>",
            "<link rel=\"modulepreload\" href=\"https://example.invalid/assets/vendor.deadbeef.js\" />",
            "<link rel=\"stylesheet\" href=\"https://example.invalid/assets/index.deadbeef.css\" crossorigin />",
        ]) . PHP_EOL;

        $this->assertSame($expected, $actual);
    }
}
