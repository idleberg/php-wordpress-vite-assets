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
    protected $baseUrl = __DIR__ . "/../_data/";
    protected $manifest = __DIR__ . "/../_data/manifest.json";

    protected function _before()
    {
        $this->basePath = realpath($this->baseUrl);
        $this->viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl);
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

    public function testGetScriptTagSHA256()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha256");

        $actual = $viteAssets->getScriptTag("demo.ts");
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\" crossorigin integrity=\"sha256-hK5PvH3PaGbMYq5EuedyA6F5uVkfoEwAznLNThffuZ8=\"></script>";

        $this->assertEquals($actual, $expected);
    }

    public function testGetScriptTagSHA384()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha384");

        $actual = $viteAssets->getScriptTag("demo.ts");
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\" crossorigin integrity=\"sha384-fWetO954Htoz6cSa6ZLx231UagP8VTXlwaO1g/JisfA9TLZnHPlgPBUwsqrWHjg0\"></script>";

        $this->assertEquals($actual, $expected);
    }

    public function testGetScriptTagSHA512()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha512");

        $actual = $viteAssets->getScriptTag("demo.ts");
        $expected = "<script type=\"module\" src=\"{$this->basePath}/assets/index.deadbeef.js\" crossorigin integrity=\"sha512-yD2Vb8LCDxC5ingFUTEa50J7EaqoK4xJzwimk2+7PPM9jczPfTDHngkduhYar/pz4dCW7qWIhm0fXFDXm1lL/A==\"></script>";

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

    public function testGetStyletagsSHA256()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha256");

        foreach ($viteAssets->getStyleTags("demo.ts") as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" crossorigin integrity=\"sha256-EEEKapOxnF8qZUxsx0ksgdBVnEB+8dXUJvH75TwCWvU=\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsSHA384()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha384");

        foreach ($viteAssets->getStyleTags("demo.ts") as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" crossorigin integrity=\"sha384-hRJLv1qN+U3dkKJIw8ANFbwPS/ED0NHZfZU96sK3vRe3evsIbIxjnkoFcJeryuVC\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsSHA512()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha512");

        foreach ($viteAssets->getStyleTags("demo.ts") as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" crossorigin integrity=\"sha512-vmI3y876ZfoogL2eJuRJy4ToOnrfwPVE7T9yMlhJp5lpSGHZ3ejDNqd7A0QYFlk0/SOugOwB1x0FCWqO95pz4Q==\" />";

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

    public function testGetStyletagsWithoutCrossoriginAttributeSHA256()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha256");

        foreach ($viteAssets->getStyleTags("demo.ts", [ "crossorigin" => false ]) as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" integrity=\"sha256-EEEKapOxnF8qZUxsx0ksgdBVnEB+8dXUJvH75TwCWvU=\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsWithoutCrossoriginAttributeSHA384()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha384");

        foreach ($viteAssets->getStyleTags("demo.ts", [ "crossorigin" => false ]) as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" integrity=\"sha384-hRJLv1qN+U3dkKJIw8ANFbwPS/ED0NHZfZU96sK3vRe3evsIbIxjnkoFcJeryuVC\" />";

            $this->assertEquals($actual, $expected);
        }
    }

    public function testGetStyletagsWithoutCrossoriginAttributeSHA512()
    {
        $viteAssets = new WordpressViteAssets($this->manifest, $this->baseUrl, "sha512");

        foreach ($viteAssets->getStyleTags("demo.ts", [ "crossorigin" => false ]) as $actual) {
            $expected = "<link rel=\"stylesheet\" href=\"{$this->basePath}/assets/index.deadbeef.css\" integrity=\"sha512-vmI3y876ZfoogL2eJuRJy4ToOnrfwPVE7T9yMlhJp5lpSGHZ3ejDNqd7A0QYFlk0/SOugOwB1x0FCWqO95pz4Q==\" />";

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
