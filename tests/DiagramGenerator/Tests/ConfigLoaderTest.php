<?php

namespace DiagramGenerator\Tests;

use DiagramGenerator\ConfigLoader;

/**
 * ConfigLoaderTest
 */
class ConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $resourcesDir;

    public function setUp()
    {
        $this->resourcesDir = sprintf("%s/Fixtures/Resources", __DIR__);
    }

    public function testLoadSizeConfig()
    {
        $loader = new ConfigLoader();
        $loader->loadSizeConfig($this->resourcesDir);
        $sizes = $loader->getSizes();
        $this->assertCount(2, $sizes);
        $this->assertInstanceOf('DiagramGenerator\Config\Size', $sizes[0]);
    }

    public function testLoadThemeConfig()
    {
        $loader = new ConfigLoader();
        $loader->loadThemeConfig($this->resourcesDir);
        $themes = $loader->getThemes();
        $this->assertCount(2, $themes);
        $this->assertInstanceOf('DiagramGenerator\Config\Theme', $themes[0]);
    }

    public function testLoadSizeConfigFailed()
    {
        $loader = new ConfigLoader();
        $this->setExpectedException('RuntimeException');
        $loader->loadSizeConfig(__DIR__);
    }

    public function testLoadThemeConfigFailed($value='')
    {
        $loader = new ConfigLoader();
        $this->setExpectedException('RuntimeException');
        $loader->loadThemeConfig(__DIR__);
    }
}
