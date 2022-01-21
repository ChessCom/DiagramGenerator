<?php

namespace DiagramGenerator\Tests\Fen;

use DiagramGenerator\Fen\Piece;
use PHPUnit\Framework\TestCase;

/**
 * PieceTest
 */
class PieceTest extends TestCase
{
    public function testSetColor()
    {
        $piece = $this->getPiece();
        $piece->setColor(Piece::WHITE);
        $this->assertEquals(Piece::WHITE, $piece->getColor());

        $piece->setColor(Piece::BLACK);
        $this->assertEquals(Piece::BLACK, $piece->getColor());

        $this->setExpectedException('InvalidArgumentException');
        $piece->setColor('invalid');
    }

    public function testSetRow()
    {
        $piece = $this->getPiece();
        $piece->setRow(2);
        $this->assertEquals(2, $piece->getRow());

        $this->setExpectedException('InvalidArgumentException');
        $piece->setRow(-1);
    }

    public function testSetColumn()
    {
        $piece = $this->getPiece();
        $piece->setColumn(5);
        $this->assertEquals(5, $piece->getColumn());

        $this->setExpectedException('InvalidArgumentException');
        $piece->setColumn(20);
    }

    protected function getPiece()
    {
        return $this
            ->getMockBuilder('DiagramGenerator\Fen\Piece')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }
}
