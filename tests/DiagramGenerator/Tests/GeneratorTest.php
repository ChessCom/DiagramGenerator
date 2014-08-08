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
        $this->setExpectedException('DiagramGenerator\Exception\UnsupportedConfigException');
        $generator->buildDiagram($config);
    }

    /**
     * @dataProvider buildDiagramInvalidSizeConfigProvider
     */
    public function testBuildDiagramInvalidSizeConfig($getSizeIndexCalls, $size)
    {
        $generator = $this->getMockBuilder('DiagramGenerator\Generator')
            ->setConstructorArgs(array($this->getValidator()))
            ->setMethods(null)
            ->getMock();

        $config = $this->getMock('DiagramGenerator\Config');

        $config->expects($this->once())
            ->method('getThemeIndex')
            ->will($this->returnValue(1));

        $config->expects($this->exactly($getSizeIndexCalls))
            ->method('getSizeIndex')
            ->will($this->returnValue($size));

        $this->setExpectedException('DiagramGenerator\Exception\UnsupportedConfigException');
        $generator->buildDiagram($config);
    }

    public function buildDiagramInvalidSizeConfigProvider()
    {
        return array(
            array(3, -1),
            array(3, 4),
            array(3, 30),
            array(2, '220px'),
            array(2, '19px')
        );
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
        $config
            ->expects($this->any())
            ->method('getTextureIndex')
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
