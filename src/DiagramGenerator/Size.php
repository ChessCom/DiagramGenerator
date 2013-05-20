<?php

namespace DiagramGenerator;

use DiagramGenerator\Size\Caption;
use DiagramGenerator\Size\Coordinates;

class Size
{
    /**
     * @var integer
     */
    protected $width;

    /**
     * @var integer
     */
    protected $height;

    /**
     * @var integer
     */
    // protected $boardX;

    /**
     * @var integer
     */
    // protected $boardY;

    /**
     * @var integer
     */
    protected $cell;

    /**
     * @var integer
     */
    // protected $outlineThick;

    /**
     * @var integer
     */
    protected $frameThick;

    /**
     * @var \DiagramGenerator\Size\Coordinates
     */
    protected $coordinates;

    /**
     * @var \DiagramGenerator\Size\Caption
     */
    protected $caption;

    /**
     * Gets the value of width.
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the value of width.
     *
     * @param integer $width the width
     *
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Gets the value of height.
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the value of height.
     *
     * @param integer $height the height
     *
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Gets the value of boardX.
     *
     * @return integer
     */
    // public function getBoardX()
    // {
    //     return $this->boardX;
    // }

    /**
     * Sets the value of boardX.
     *
     * @param integer $boardX the boardX
     *
     * @return self
     */
    // public function setBoardX($boardX)
    // {
    //     $this->boardX = $boardX;

    //     return $this;
    // }

    /**
     * Gets the value of boardY.
     *
     * @return integer
     */
    // public function getBoardY()
    // {
    //     return $this->boardY;
    // }

    /**
     * Sets the value of boardY.
     *
     * @param integer $boardY the boardY
     *
     * @return self
     */
    // public function setBoardY($boardY)
    // {
    //     $this->boardY = $boardY;

    //     return $this;
    // }

    /**
     * Gets the value of cell.
     *
     * @return integer
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * Sets the value of cell.
     *
     * @param integer $cell the cell
     *
     * @return self
     */
    public function setCell($cell)
    {
        $this->cell = $cell;

        return $this;
    }

    /**
     * Gets the value of outlineThick.
     *
     * @return integer
     */
    // public function getOutlineThick()
    // {
    //     return $this->outlineThick;
    // }

    /**
     * Sets the value of outlineThick.
     *
     * @param integer $outlineThick the outlineThick
     *
     * @return self
     */
    // public function setOutlineThick($outlineThick)
    // {
    //     $this->outlineThick = $outlineThick;

    //     return $this;
    // }

    /**
     * Gets the value of frameThick.
     *
     * @return integer
     */
    public function getFrameThick()
    {
        return $this->frameThick;
    }

    /**
     * Sets the value of frameThick.
     *
     * @param integer $frameThick the frameThick
     *
     * @return self
     */
    public function setFrameThick($frameThick)
    {
        $this->frameThick = $frameThick;

        return $this;
    }

    /**
     * Gets the value of coordinates.
     *
     * @return \DiagramGenerator\Size\Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Sets the value of coordinates.
     *
     * @param \DiagramGenerator\Size\Coordinates $coordinates the coordinates
     *
     * @return self
     */
    public function setCoordinates(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return \DiagramGenerator\Size\Caption
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param \DiagramGenerator\Size\Caption $caption the caption
     *
     * @return self
     */
    public function setCaption(Caption $caption)
    {
        $this->caption = $caption;

        return $this;
    }
}