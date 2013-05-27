<?php

namespace DiagramGenerator\Tests;

use DiagramGenerator\Diagram;

/**
 * DiagramTest
 */
class DiagramTest extends \PHPUnit_Framework_TestCase
{
    public function testDrawFailed()
    {
        $diagram = $this
            ->getMockBuilder('DiagramGenerator\Diagram')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $this->setExpectedException('InvalidArgumentException');
        $diagram->draw();
    }

    public function testDraw()
    {
        $config = $this->getConfig();
        $board = $this->getBoard($config);
        $diagram = $this->getDiagram($config, array('getCaptionText'));
        $diagram
            ->expects($this->once())
            ->method('getCaptionText')
            ->will($this->returnValue(null));
        $diagram->setBoard($board);
        $diagram->draw();
        $image = $diagram->getImage();
        $this->assertEquals('jpeg', $image->getImageFormat());
        $this->assertEquals(10, $image->getImageWidth());
        $this->assertEquals(10, $image->getImageHeight());
    }

    public function testDrawWithCoordinates()
    {
        $config = $this->getConfig();
        $config
            ->expects($this->any())
            ->method('getCoordinates')
            ->will($this->returnValue(true));
        $board = $this->getBoard($config, array('getCellSize'));
        $board
            ->expects($this->any())
            ->method('getCellSize')
            ->will($this->returnValue(90));
        $diagram = $this->getDiagram($config, array('getBackgroundColor', 'getBoardThickness', 'createCoordinate'));
        $diagram
            ->expects($this->any())
            ->method('getBoardThickness')
            ->will($this->returnValue(2));
        $diagram
            ->expects($this->any())
            ->method('getBackgroundColor')
            ->will($this->returnValue(new \ImagickPixel('#000000')));
        $coordinate = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Coordinate')
            ->setConstructorArgs(array($config))
            ->setMethods(array('getImage'))
            ->getMock();
        $coordinate
            ->expects($this->any())
            ->method('getImage')
            ->will($this->returnValue(new \Imagick(__DIR__.'/Fixtures/Resources/pieces/default.png')));
        $diagram
            ->expects($this->any())
            ->method('createCoordinate')
            ->will($this->returnValue($coordinate));
        $diagram->setBoard($board);
        $diagram->draw();
        $this->assertEquals(100, $diagram->getImage()->getImageWidth());
        $this->assertEquals(100, $diagram->getImage()->getImageHeight());
        $this->assertEquals('jpeg', $diagram->getImage()->getImageFormat());
    }

    public function testDrawWithCaption()
    {
        $config = $this->getConfig();
        $board = $this->getBoard($config, array('getCellSize'));
        $board
            ->expects($this->any())
            ->method('getCellSize')
            ->will($this->returnValue(90));
        $diagram = $this->getDiagram($config, array('getBackgroundColor', 'getBoardThickness', 'getCaptionText', 'createCaption'));
        $diagram
            ->expects($this->any())
            ->method('getBoardThickness')
            ->will($this->returnValue(2));
        $diagram
            ->expects($this->any())
            ->method('getBackgroundColor')
            ->will($this->returnValue(new \ImagickPixel('#000000')));
        $diagram
            ->expects($this->any())
            ->method('getCaptionText')
            ->will($this->returnValue('caption text'));
        $caption = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Caption')
            ->disableOriginalConstructor()
            ->getMock();
        $caption
            ->expects($this->once())
            ->method('getImage')
            ->will($this->returnValue(new \Imagick(__DIR__.'/Fixtures/Resources/pieces/default.png')));
        $diagram
            ->expects($this->once())
            ->method('createCaption')
            ->will($this->returnValue($caption));
        $diagram->setBoard($board);
        $diagram->draw();
        $this->assertEquals(100, $diagram->getImage()->getImageWidth());
        $this->assertEquals(235, $diagram->getImage()->getImageHeight());
        $this->assertEquals('jpeg', $diagram->getImage()->getImageFormat());
    }

    protected function getBoard($config, array $methods = array())
    {
        $board = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Board')
            ->setConstructorArgs(array($config))
            ->setMethods(array_merge(array('getImage'), $methods))
            ->getMock();
        $boardImage = new \Imagick();
        $boardImage->newImage(10, 10, new \ImagickPixel('#000000'));
        $board
            ->expects($this->any())
            ->method('getImage')
            ->will($this->returnValue($boardImage));

        return $board;
    }

    protected function getDiagram($config, array $methods)
    {
        return $this
            ->getMockBuilder('DiagramGenerator\Diagram')
            ->setConstructorArgs(array($config))
            ->setMethods($methods)
            ->getMock();
    }

    protected function getConfig()
    {
        return $this->getMock('DiagramGenerator\Config');
    }
}
