<?php

namespace DiagramGenerator\Fen;

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
