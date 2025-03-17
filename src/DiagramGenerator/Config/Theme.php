<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class to keep all theme config.
 */
class Theme
{
    /**
     * Theme name.
     *
     *
     * @var string
     */
    #[Type('string')]
    protected $name;

    /**
     * @var ThemeColor
     */
    #[Type('DiagramGenerator\Config\ThemeColor')]
    #[Valid]
    protected $color;

    public function __construct()
    {
        $this->color = new ThemeColor();
    }

    /**
     * Gets the Font filename.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the Font filename.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of color.
     *
     * @return ThemeColor
     */
    public function getColor()
    {
        return $this->color ?: new ThemeColor();
    }

    /**
     * Sets the value of color.
     *
     * @param ThemeColor $color the color
     *
     * @return self
     */
    public function setColor(ThemeColor $color)
    {
        $this->color = $color;

        return $this;
    }
}
