<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;

/**
 * Class to keep texture config
 *
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Texture
{
    /**
     * Boards filename
     * @Type("string")
     * @var string
     */
    protected $board;

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
