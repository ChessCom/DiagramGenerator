<?php

namespace DiagramGenerator\Fen;

class Pawn extends Piece
{
    public function getName()
    {
        return 'pawn';
    }

    public function getKey()
    {
        return 'p';
    }
}
