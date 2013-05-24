<?php

namespace DiagramGenerator\Fen;

/**
 * Interface all pieces should implement
 * TODO: probably should be a part of chess-game library
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
abstract class Piece
{
    const WHITE = 'white';
    const BLACK = 'black';

    /**
     * Piece color
     * @var string
     */
    protected $color;

    /**
     * Row index of piece position
     * @var integer
     */
    protected $row;

    /**
     * Column index of piece position
     * @var integer
     */
    protected $column;

    /**
     * Returns name of the piece
     * @return string
     */
    abstract public function getName();

    /**
     * Returns piece key (one letter, e.g. "r" or "p")
     * @return string
     */
    abstract public function getKey();

    public function __construct($color)
    {
        $this->setColor($color);
    }

    /**
     * Gets the Piece color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets the Piece color
     *
     * @param string $color the color
     *
     * @return self
     */
    public function setColor($color)
    {
        if ($color != self::WHITE && $color != self::BLACK) {
            throw new \InvalidArgumentException(sprintf('Invalid color given: %s', $color));
        }

        $this->color = $color;

        return $this;
    }

    /**
     * Gets the Row index of piece position.
     *
     * @return integer
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Sets the Row index of piece position.
     *
     * @param integer $row the row
     *
     * @return self
     */
    public function setRow($row)
    {
        if ($row < 0 || $row > 7) {
            throw new \InvalidArgumentException("Invalid row index given: '%u'.", $row);
        }

        $this->row = $row;

        return $this;
    }

    /**
     * Gets the Column index of piece position.
     *
     * @return integer
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Sets the Column index of piece position.
     *
     * @param integer $column the column
     *
     * @return self
     */
    public function setColumn($column)
    {
        if ($column < 0 || $column > 7) {
            throw new \InvalidArgumentException("Invalid column index given: '%u'.", $column);
        }

        $this->column = $column;

        return $this;
    }
}
