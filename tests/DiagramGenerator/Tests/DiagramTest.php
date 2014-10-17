<?php

namespace DiagramGenerator\Tests;

use DiagramGenerator\Diagram;
use DiagramGenerator\Config;

/**
 * DiagramTest
 */
class DiagramTest extends \PHPUnit_Framework_TestCase
{
    /** @var \DiagramGenerator\Config $config */
    protected $config;

    /** @var \DiagramGenerator\Diagram $diagram */
    protected $diagram;

    public function setUp()
    {
        $this->config = new Config();

        $this->diagram = new Diagram($this->config);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Board must be set
     */
    public function testDrawNoBoard()
    {
        $this->diagram->draw();
    }
}
