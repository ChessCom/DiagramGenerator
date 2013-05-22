<?php

namespace DiagramGenerator\Config;

use DiagramGenerator\Config\ThemeColor;
use JMS\Serializer\Annotation\Type;

class Theme
{
    /**
     * Font filename
     * @Type("string")
     * @var string
     */
    protected $font;

    /**
     * @Type("DiagramGenerator\Config\ThemeColor")
     * @var \DiagramGenerator\Config\ThemeColor
     */
    protected $color;

    public function __construct()
    {
        $this->color = new ThemeColor();
    }

    /**
     * Gets the font filename.
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Sets the font filename.
     *
     * @param string $font the font
     *
     * @return self
     */
    public function setFont($font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Gets the value of color.
     *
     * @return \DiagramGenerator\Config\ThemeColor
     */
    public function getColor()
    {
        return $this->color ?: new ThemeColor();
    }

    /**
     * Sets the value of color.
     *
     * @param \DiagramGenerator\Config\ThemeColor $color the color
     *
     * @return self
     */
    public function setColor(ThemeColor $color)
    {
        $this->color = $color;

        return $this;
    }
}
