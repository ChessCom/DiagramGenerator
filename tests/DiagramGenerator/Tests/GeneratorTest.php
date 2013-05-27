<?php

namespace DiagramGenerator\Tests;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * GeneratorTest
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildDiagramInvalidThemeConfig()
    {
        $generator = $this
            ->getMockBuilder('DiagramGenerator\Generator')
            ->setConstructorArgs(array($this->getValidator()))
            ->setMethods(null)
            ->getMock();
        $config = $this->getMock('DiagramGenerator\Config');
        $this->setExpectedException('DiagramGenerator\Exception\InvalidConfigException');
        $generator->buildDiagram($config);
    }

    public function testBuildDiagramInvalidSizeConfig()
    {
        $generator = $this
            ->getMockBuilder('DiagramGenerator\Generator')
            ->setConstructorArgs(array($this->getValidator()))
            ->setMethods(null)
            ->getMock();
        $config = $this->getMock('DiagramGenerator\Config');
        $config
            ->expects($this->once())
            ->method('getThemeIndex')
            ->will($this->returnValue(1));
        $this->setExpectedException('DiagramGenerator\Exception\InvalidConfigException');
        $generator->buildDiagram($config);
    }

    public function testBuildDiagram()
    {
        $generator = $this
            ->getMockBuilder('DiagramGenerator\Generator')
            ->setConstructorArgs(array($this->getValidator()))
            ->setMethods(array('createBoard', 'createDiagram'))
            ->getMock();
        $board = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Board')
            ->disableOriginalConstructor()
            ->getMock();
        $generator
            ->expects($this->once())
            ->method('createBoard')
            ->will($this->returnValue($board));
        $diagram = $this
            ->getMockBuilder('DiagramGenerator\Diagram')
            ->disableOriginalConstructor()
            ->getMock();
        $generator
            ->expects($this->once())
            ->method('createDiagram')
            ->will($this->returnValue($diagram));
        $config = $this->getMock('DiagramGenerator\Config');
        $config
            ->expects($this->any())
            ->method('getThemeIndex')
            ->will($this->returnValue(1));
        $config
            ->expects($this->any())
            ->method('getSizeIndex')
            ->will($this->returnValue(1));
        $diagram = $generator->buildDiagram($config);
        $this->assertInstanceOf('DiagramGenerator\Diagram', $diagram);
    }

    protected function getValidator()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Validator\Validator')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
