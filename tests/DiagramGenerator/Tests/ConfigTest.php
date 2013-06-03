<?php

namespace DiagramGenerator\Tests;

use DiagramGenerator\Config;

/**
 * ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
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
}
