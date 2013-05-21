<?php

namespace DiagramGenerator\Fen;

use DiagramGenerator\Fen\Piece;

/**
 * TODO: probably should be a part of chess-game library
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Bishop extends Piece
{
    public function getName()
    {
        return 'bishop';
    }

    public function getKey()
    {
        return 'b';
    }
}
