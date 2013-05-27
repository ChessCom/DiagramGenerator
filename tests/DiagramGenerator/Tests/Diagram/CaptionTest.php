<?php

namespace DiagramGenerator\Tests\Diagram;

/**
 * CaptionTest
 */
class CaptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDraw()
    {
        $configMock = $this->getMock('DiagramGenerator\Config');
        $configMock
            ->expects($this->once())
            ->method('getCaption')
            ->will($this->returnValue('caption text'));
        $caption = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Caption')
            ->setConstructorArgs(array($configMock))
            ->setMethods(array('getFont', 'getCaptionSize'))
            ->getMock();
        $caption
            ->expects($this->once())
            ->method('getFont')
            ->will($this->returnValue(__DIR__.'/../Fixtures/Resources/fonts/default.ttf'));
        $caption
            ->expects($this->any())
            ->method('getCaptionSize')
            ->will($this->returnValue(36));
        $draw = $caption->getDraw();
        $this->assertInstanceOf('\ImagickDraw', $draw);
        $this->assertEquals(36, $draw->getFontSize());
    }

    public function testDrawBorder()
    {
        $image = new \Imagick(__DIR__.'/../Fixtures/Resources/pieces/default.png');
        $caption = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Caption')
            ->disableOriginalConstructor()
            ->setMethods(array('getImage'))
            ->getMock();
        $caption
            ->expects($this->any())
            ->method('getImage')
            ->will($this->returnValue($image));
        $imagickPixel = $this->getMock('ImagickPixel');
        $caption->drawBorder($imagickPixel, 2, 2);
        $this->assertEquals(94, $caption->getImage()->getImageWidth());
        $this->assertEquals(94, $caption->getImage()->getImageHeight());
    }
}
