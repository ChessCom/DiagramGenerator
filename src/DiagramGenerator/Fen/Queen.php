<?php

namespace DiagramGenerator\Fen;

use DiagramGenerator\Fen\Piece;

/**
 * TODO: probably should be a part of chess-game library
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Queen extends Piece
{
    public function getName()
    {
        return 'queen';
    }

    public function getKey()
    {
        return 'q';
    }
}
