<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Board;

class Config
{
    /** @var string $fen */
    protected $fen;

    /** @var \DiagramGenerator\Config\Size $size */
    protected $size;

    /** @var \DiagramGenerator\Config\Board $board */
    protected $board;

    /** @var string $pieceTheme */
    protected $pieceTheme;

    public function getFen()
    {
        return $this->fen;
    }

    public function setFen($fen)
    {
        $this->fen = $fen;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize(Size $size)
    {
        $this->size = $size;

        return $this;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function setBoard(Board $board)
    {
        $this->board = $board;

        return $this;
    }

    public function getPieceTheme()
    {
        return $this->pieceTheme;
    }

    public function setPieceTheme($pieceTheme)
    {
        $this->pieceTheme = $pieceTheme;
    }
}
