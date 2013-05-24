<?php

namespace DiagramGenerator\Tests;

use DiagramGenerator\Fen;

/**
 * FenTest
 */
class FenTest extends \PHPUnit_Framework_TestCase
{
    protected $defaultFen;

    public function setUp()
    {
        $this->defaultFen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
    }

    public function testGetPieceByKey()
    {
        $piece = Fen::getPieceByKey('r');
        $this->assertInstanceOf('DiagramGenerator\Fen\Rook', $piece);
        $this->assertEquals('black', $piece->getColor());

        $piece = Fen::getPieceByKey('n');
        $this->assertInstanceOf('DiagramGenerator\Fen\Knight', $piece);

        $piece = Fen::getPieceByKey('b');
        $this->assertInstanceOf('DiagramGenerator\Fen\Bishop', $piece);

        $piece = Fen::getPieceByKey('Q');
        $this->assertInstanceOf('DiagramGenerator\Fen\Queen', $piece);
        $this->assertEquals('white', $piece->getColor());

        $piece = Fen::getPieceByKey('k');
        $this->assertInstanceOf('DiagramGenerator\Fen\King', $piece);

        $piece = Fen::getPieceByKey('p');
        $this->assertInstanceOf('DiagramGenerator\Fen\Pawn', $piece);

        $piece = Fen::getPieceByKey(null);
        $this->assertNull($piece);

        $this->setExpectedException('InvalidArgumentException');
        $piece = Fen::getPieceByKey(0);
    }

    public function testSanitizeFenString()
    {
        $fen = Fen::sanitizeFenString($this->defaultFen);
        $this->assertEquals('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR', $fen);
    }

    public function testSetAtPosition()
    {
        $piece = $this
            ->getMockBuilder('DiagramGenerator\Fen\Piece')
            ->disableOriginalConstructor()
            ->setMethods(array('setColumn', 'setRow'))
            ->getMockForAbstractClass();
        $piece
            ->expects($this->once())
            ->method('setRow')
            ->will($this->returnSelf());
        $piece
            ->expects($this->once())
            ->method('setColumn')
            ->will($this->returnSelf());
        $fen = new Fen();
        $fen->setAtPosition(0, 0, $piece);
        $pieces = $fen->getPieces();
        $this->assertCount(1, $pieces);
        $this->assertArrayHasKey('0:0', $pieces);
    }

    /**
     * @depends testSetAtPosition
     * @dataProvider rowProvider
     */
    public function testSetRow(array $row, $index)
    {
        $fen = new Fen();
        $fen->setRow($row, $index);
        $this->assertCount(count($row), $fen->getPieces());
    }

    /**
     * @depends testSanitizeFenString
     * @depends testSetRow
     */
    public function testCreateFromString()
    {
        $fen = Fen::createFromString($this->defaultFen);
        $pieces = $fen->getPieces();
        $this->assertCount(32, $pieces);
        $this->assertArrayHasKey('0:0', $pieces);
        $this->assertArrayHasKey('7:7', $pieces);
        $this->assertInstanceOf('DiagramGenerator\Fen\Rook', $pieces['0:0']);
        $this->assertInstanceOf('DiagramGenerator\Fen\Rook', $pieces['7:7']);
    }

    public function rowProvider()
    {
        return array(
            array(array('0' => 'r', '1' => 'P', '6' => 'q'), 2),
            array(array('3' => 'R', '1' => 'P', '6' => 'k'), 5),
            array(array(), 4),
        );
    }
}
