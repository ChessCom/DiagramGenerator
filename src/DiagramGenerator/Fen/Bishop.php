<?php

namespace DiagramGenerator\Fen;

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
