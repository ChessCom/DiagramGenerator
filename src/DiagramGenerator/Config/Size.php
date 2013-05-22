<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;

class Size
{
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
}
