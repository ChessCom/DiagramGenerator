<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;

/**
 * Class to keep theme texture config
 *
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class ThemeTexture
{
    /**
     * Pieces directory name
     * @Type("string")
     * @var string
     */
    protected $piece;

    /**
     * Boards filename
     * @Type("string")
     * @var string
     */
    protected $board;

    /**
     * Gets the Pieces directory name.
     *
     * @return string
     */
    public function getPiece()
    {
        return $this->piece;
    }

    /**
     * Sets the Pieces directory name.
     *
     * @param string $piece the piece
     *
     * @return self
     */
    public function setPiece($piece)
    {
        $this->piece = $piece;

        return $this;
    }

    /**
     * Gets the Boards filename.
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Sets the Boards filename.
     *
     * @param string $board the board
     *
     * @return self
     */
    public function setBoard($board)
    {
        $this->board = $board;

        return $this;
    }
}
