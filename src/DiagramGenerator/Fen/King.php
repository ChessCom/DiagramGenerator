<?php

namespace DiagramGenerator\Fen;

class King extends Piece
{
    public function getName()
    {
        return 'king';
    }

    public function getKey()
    {
        return 'k';
    }
}
