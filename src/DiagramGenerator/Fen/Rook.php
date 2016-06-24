<?php

namespace DiagramGenerator\Fen;

class Rook extends Piece
{
    public function getName()
    {
        return 'rook';
    }

    public function getKey()
    {
        return 'r';
    }
}
