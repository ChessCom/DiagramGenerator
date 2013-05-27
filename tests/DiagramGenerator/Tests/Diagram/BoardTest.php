<?php

namespace DiagramGenerator\Tests\Diagram;

use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Fen;

/**
 * BoardTest
 */
class BoardTest extends \PHPUnit_Framework_TestCase
{
    protected $board;

    public function setUp()
    {
        $configMock = $this->getMock('DiagramGenerator\Config');
        $configMock
            ->expects($this->any())
            ->method('getFen')
            ->will($this->returnValue(FEN::DEFAULT_FEN));
        $board = $this
            ->getMockBuilder('DiagramGenerator\Diagram\Board')
            ->setConstructorArgs(array($configMock))
            ->setMethods(array(
                'getBackgroundColor',
                'getLightColor',
                'getDarkColor',
                'getCellSize',
                'getPieceImagePath',
                'getBorderColor',
                'getBorderSize'
            ))
            ->getMock();
        $board
            ->expects($this->any())
            ->method('getCellSize')
            ->will($this->returnValue(90));
        $board
            ->expects($this->any())
            ->method('getBorderSize')
            ->will($this->returnValue(2));
        $board
            ->expects($this->once())
            ->method('getBackgroundColor')
            ->will($this->returnValue('#000000'));
        $board
            ->expects($this->any())
            ->method('getBorderColor')
            ->will($this->returnValue('#000000'));
        $board
            ->expects($this->any())
            ->method('getLightColor')
            ->will($this->returnValue(new \ImagickPixel('#FFFFFF')));
        $board
            ->expects($this->any())
            ->method('getDarkColor')
            ->will($this->returnValue(new \ImagickPixel('#000000')));
        $board
            ->expects($this->any())
            ->method('getPieceImagePath')
            ->will($this->returnValue(__DIR__.'/../Fixtures/Resources/pieces/default.png'));
        $this->board = $board;
    }

    public function testDrawBoard()
    {
        $this->board->drawBoard();
        $this->assertEquals(90 * 8, $this->board->getImage()->getImageWidth());
        $this->assertEquals(90 * 8, $this->board->getImage()->getImageHeight());
        $this->assertEquals(
            'srgb(255,255,255)',
            $this->board->getImage()->getImageBackgroundColor()->getColorAsString()
        );
    }

    /**
     * @depends testDrawBoard
     */
    public function testDrawCells()
    {
        $this->board
            ->drawBoard()
            ->drawCells();
        // FIXME: figure out a better way to test cells were correctly drawn
        $this->assertEquals(90 * 8, $this->board->getImage()->getImageWidth());
        $this->assertEquals(90 * 8, $this->board->getImage()->getImageHeight());
    }

    /**
     * @depends testDrawBoard
     * @depends testDrawCells
     */
    public function testDrawFigures()
    {
        $this->board
            ->drawBoard()
            ->drawFigures();
        // FIXME: figure out a better way to test cells were correctly drawn
        $this->assertEquals(90 * 8, $this->board->getImage()->getImageWidth());
        $this->assertEquals(90 * 8, $this->board->getImage()->getImageHeight());
    }

    /**
     * @depends testDrawBoard
     * @depends testDrawFigures
     */
    public function testDrawBorder()
    {
        $this->board
            ->drawBoard()
            ->drawBorder();
        $this->assertEquals(90 * 8 + 2 * 2, $this->board->getImage()->getImageWidth());
        $this->assertEquals(90 * 8 + 2 * 2, $this->board->getImage()->getImageHeight());
    }

    /**
     * @depends testDrawBoard
     */
    public function testDraw()
    {
        $this->board
            ->drawBoard()
            ->draw();
        $this->assertEquals('jpeg', $this->board->getImage()->getImageFormat());
    }
}
