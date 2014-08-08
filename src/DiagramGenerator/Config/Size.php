<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;

class Size
{
    const MIN_CUSTOM_SIZE = 20;
    const MAX_CUSTOM_SIZE = 200;
    const CAPTION_COEFFICIENT = .4;
    const BORDER_COEFFICIENT = 0;
    const COORDINATES_COEFFICIENT = .25;

    /**
     * Cell size
     * @Type("integer")
     * @var integer
     */
    protected $cell;

    /**
     * Border thickness
     * @Type("integer")
     * @var integer
     */
    protected $border;

    /**
     * Caption font size
     * @Type("integer")
     * @var integer
     */
    protected $caption;

    /**
     * Coordinates fonts size
     * @Type("integer")
     * @var integer
     */
    protected $coordinates;

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
     * Gets the Border thickness.
     *
     * @return integer
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Sets the Border thickness.
     *
     * @param integer $border the border
     *
     * @return self
     */
    public function setBorder($border)
    {
        $this->border = $border;

        return $this;
    }

    /**
     * Gets the Caption font size.
     *
     * @return integer
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the Caption font size.
     *
     * @param integer $caption the caption
     *
     * @return self
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Gets the Coordinates fonts size.
     *
     * @return integer
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Sets the Coordinates fonts size.
     *
     * @param integer $coordinates the coordinates
     *
     * @return self
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }
}
