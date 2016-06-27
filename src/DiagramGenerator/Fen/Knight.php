<?php

namespace DiagramGenerator\Fen;

/**
 * TODO: probably should be a part of chess-game library.
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
