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
     * Cell size.
     *
     *
     * @var int
     */
    #[Type('integer')]
    protected $cell;

    /**
     * Border thickness.
     *
     *
     * @var int
     */
    #[Type('integer')]
    protected $border;

    /**
     * Caption font size.
     *
     *
     * @var int
     */
    #[Type('integer')]
    protected $caption;

    /**
     * Coordinates fonts size.
     *
     *
     * @var int
     */
    #[Type('integer')]
    protected $coordinates;

    /**
     * Gets the value of cell.
     *
     * @return int
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * Sets the value of cell.
     *
     * @param int $cell the cell
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
     * @return int
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Sets the Border thickness.
     *
     * @param int $border the border
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
     * @return int
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the Caption font size.
     *
     * @param int $caption the caption
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
     * @return int
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Sets the Coordinates fonts size.
     *
     * @param int $coordinates the coordinates
     *
     * @return self
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }
}
