<?php

namespace DiagramGenerator\Fen;

use DiagramGenerator\Fen\Piece;

/**
 * TODO: probably should be a part of chess-game library
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Knight extends Piece
{
    public function getName()
    {
        return 'knight';
    }

    public function getKey()
    {
        return 'n';
    }
}
