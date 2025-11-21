<?php

namespace DiagramGenerator\Tests;

use DiagramGenerator\Config;
use PHPUnit\Framework\TestCase;

/**
 * ConfigTest
 */
class ConfigTest extends TestCase
{
    public function testGetFen()
    {
        $sanitizedFen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR';
        $config = new Config();

        $config->setFen('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
        $this->assertEquals($sanitizedFen, $config->getFen());

        $config->setFen($sanitizedFen);
        $this->assertEquals($sanitizedFen, $config->getFen());
    }

    /**
     * @dataProvider colorProvider
     */
    public function testGetLight($color)
    {
        $config = new Config();

        $config->setLight($color);
        $this->assertEquals('#FFFFFF', $config->getLight());

        $config->setLight($color);
        $this->assertEquals('#FFFFFF', $config->getLight());
    }

    /**
     * @dataProvider colorProvider
     */
    public function testGetDark($color)
    {
        $config = new Config();

        $config->setDark($color);
        $this->assertEquals('#FFFFFF', $config->getDark());

        $config->setDark($color);
        $this->assertEquals('#FFFFFF', $config->getDark());
    }

    public function colorProvider()
    {
        return array(
            array('#FFFFFF'),
            array('FFFFFF')
        );
    }

    public function testGetThemeUrlsReturnsNullWhenNotSet()
    {
        $config = new Config();
        $this->assertNull($config->getThemeUrls());
    }

    public function testSetThemeUrls()
    {
        $config = new Config();
        $themeUrls = [
            'board' => 'https://example.com/board.png',
            'wp' => 'https://example.com/wp.png',
            'bp' => 'https://example.com/bp.png',
        ];

        $result = $config->setThemeUrls($themeUrls);
        $this->assertSame($config, $result);
        $this->assertEquals($themeUrls, $config->getThemeUrls());
    }

    public function testHasThemeUrlsReturnsFalseWhenNotSet()
    {
        $config = new Config();
        $this->assertFalse($config->hasThemeUrls());
    }

    public function testHasThemeUrlsReturnsFalseWhenEmptyArray()
    {
        $config = new Config();
        $config->setThemeUrls([]);
        $this->assertFalse($config->hasThemeUrls());
    }

    public function testHasThemeUrlsReturnsTrueWhenSet()
    {
        $config = new Config();
        $config->setThemeUrls([
            'board' => 'https://example.com/board.png',
            'wp' => 'https://example.com/wp.png',
        ]);
        $this->assertTrue($config->hasThemeUrls());
    }
}
