<?php

namespace DiagramGenerator\Tests\Diagram;

/**
 * CoordinatesTest
 */
class CoordinatesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDraw()
    {
        $configMock = $this->getMock('DiagramGenerator\Config');
        $coordinate = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Coordinate')
            ->setConstructorArgs(array($configMock))
            ->setMethods(array('getFont', 'getCoordinatesSize'))
            ->getMock();
        $coordinate
            ->expects($this->once())
            ->method('getFont')
            ->will($this->returnValue(__DIR__.'/../Fixtures/Resources/fonts/default.ttf'));
        $coordinate
            ->expects($this->any())
            ->method('getCoordinatesSize')
            ->will($this->returnValue(36));
        $draw = $coordinate->getDraw();
        $this->assertInstanceOf('\ImagickDraw', $draw);
        $this->assertEquals(36, $draw->getFontSize());
    }
}
