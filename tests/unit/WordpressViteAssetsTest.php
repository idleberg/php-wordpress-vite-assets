<?php

use Idleberg\WordpressViteAssets\WordpressViteAssets;

class WordpressViteAssetsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $basePath;
    protected $viteAssets;

    protected function _before()
    {
        $baseUrl = __DIR__ . "/../_data/";
        $manifest = __DIR__ . "/../_data/manifest.json";

        $this->basePath = realpath($baseUrl);
        $this->viteAssets = new WordpressViteAssets($manifest, $baseUrl);
    }

    protected function _after()
    {
    }

    // tests
    public function testGetScriptTag()
    {
        $actual = $this->viteAssets->getScriptTag("demo.ts");
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\" crossorigin integrity=\"sha256-hK5PvH3PaGbMYq5EuedyA6F5uVkfoEwAznLNThffuZ8=\"></script>";

        $this->assertEquals($actual, $expected);
    }

    public function testGetScriptTagWithoutOptionalAttributes()
    {
        $actual = $this->viteAssets->getScriptTag("demo.ts", [ "crossorigin" => false, "integrity" => false]);
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\"></script>";

        $this->assertEquals($actual, $expected);
    }

    public function testGetScriptTagWithoutCrossoriginAttribute()
    {
        $actual = $this->viteAssets->getScriptTag("demo.ts", [ "crossorigin" => false]);
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\" integrity=\"sha256-hK5PvH3PaGbMYq5EuedyA6F5uVkfoEwAznLNThffuZ8=\"></script>";

        $this->assertEquals($actual, $expected);
    }

    public function testGetScriptTagWithoutIntegrityAttribute()
    {
        $actual = $this->viteAssets->getScriptTag("demo.ts", [ "integrity" => false]);
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\" crossorigin></script>";

        $this->assertEquals($actual, $expected);
    }

    public function testGetStyletags()
    {
        foreach ($this->viteAssets->getStyleTags("demo.ts") as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" crossorigin integrity=\"sha256-EEEKapOxnF8qZUxsx0ksgdBVnEB+8dXUJvH75TwCWvU=\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsWithoutAttributes()
    {
        foreach ($this->viteAssets->getStyleTags("demo.ts", [ "crossorigin" => false, "integrity" => false]) as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsWithoutCrossoriginAttribute()
    {
        foreach ($this->viteAssets->getStyleTags("demo.ts", [ "crossorigin" => false ]) as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" integrity=\"sha256-EEEKapOxnF8qZUxsx0ksgdBVnEB+8dXUJvH75TwCWvU=\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsWithoutIntegrityAttribute()
    {
        foreach ($this->viteAssets->getStyleTags("demo.ts", [ "integrity" => false ]) as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" crossorigin />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetPreloadtags()
    {
        foreach ($this->viteAssets->getPreloadTags("demo.ts") as $actual) {
            $expected = "<link rel=\"modulepreload\" href=\"{$this->basePath}/assets/vendor.deadbeef.js\" />";

            $this->assertEquals($actual, $expected);
        }
    }
}
