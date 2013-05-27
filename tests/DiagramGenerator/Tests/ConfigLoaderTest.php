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
        $loader = $this->getLoaderMock();
        $loader->loadSizeConfig($this->resourcesDir);
        $sizes = $loader->getSizes();
        $this->assertCount(2, $sizes);
        $this->assertInstanceOf('DiagramGenerator\Config\Size', $sizes[0]);
    }

    public function testLoadThemeConfig()
    {
        $loader = $this->getLoaderMock();
        $loader->loadThemeConfig($this->resourcesDir);
        $themes = $loader->getThemes();
        $this->assertCount(2, $themes);
        $this->assertInstanceOf('DiagramGenerator\Config\Theme', $themes[0]);
    }

    public function testLoadSizeConfigFailed()
    {
        $loader = $this->getLoaderMock();
        $this->setExpectedException('RuntimeException');
        $loader->loadSizeConfig(__DIR__);
    }

    public function testLoadThemeConfigFailed($value='')
    {
        $loader = $this->getLoaderMock();
        $this->setExpectedException('RuntimeException');
        $loader->loadThemeConfig(__DIR__);
    }

    protected function getLoaderMock()
    {
        $validatorMock = $this
            ->getMockBuilder('Symfony\Component\Validator\Validator')
            ->disableOriginalConstructor()
            ->getMock();

        return $this
            ->getMockBuilder('DiagramGenerator\ConfigLoader')
            ->setConstructorArgs(array($validatorMock))
            ->setMethods(null)
            ->getMock();
    }
}
