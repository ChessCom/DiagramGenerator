<?php

namespace DiagramGenerator\Fen;

abstract class Piece
{
    const WHITE = 'white';
    const BLACK = 'black';

    /**
     * Piece color.
     *
     * @var string
     */
    protected $color;

    /**
     * Row index of piece position.
     *
     * @var int
     */
    protected $row;

    /**
     * Column index of piece position.
     *
     * @var int
     */
    protected $column;

    /**
     * Returns name of the piece.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns piece key (one letter, e.g. "r" or "p").
     *
     * @return string
     */
    abstract public function getKey();

    public function __construct($color)
    {
        $this->setColor($color);
    }

    /**
     * Gets the Piece color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets the Piece color.
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
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Sets the Row index of piece position.
     *
     * @param int $row the row
     *
     * @return self
     */
    public function setRow($row)
    {
        if ($row < 0 || $row > 7) {
            throw new \InvalidArgumentException(sprintf("Invalid row index given: '%u'.", $row));
        }

        $this->row = $row;

        return $this;
    }

    /**
     * Gets the Column index of piece position.
     *
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Sets the Column index of piece position.
     *
     * @param int $column the column
     *
     * @return self
     */
    public function setColumn($column)
    {
        if ($column < 0 || $column > 7) {
            throw new \InvalidArgumentException(sprintf("Invalid column index given: '%u'.", $column));
        }

        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return substr($this->getColor(), 0, 1).$this->getKey();
    }
}
