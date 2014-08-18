<?php

namespace DiagramGenerator\Config;

use DiagramGenerator\Config\ThemeColor;
use DiagramGenerator\Config\ThemeTexture;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class to keep all theme config
 *
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Theme
{
    protected $pieceThemes = array(
        '3d_chesskid', '3d_plastic', '3d_staunton', '3d_wood', 'alpha', 'blindfold', 'book', 'bubblegum', 'cases',
        'classic', 'club', 'condal', 'dark', 'game_room', 'glass', 'gothic', 'graffiti', 'light', 'lolz', 'marble',
        'maya', 'metal', 'mini', 'modern', 'nature', 'neon', 'newspaper', 'ocean', 'sky', 'space', 'tigers',
        'tournament', 'vintage', 'wood'
    );

    /**
     * Theme name
     * @Type("string")
     * @var string
     */
    protected $name;

    /**
     * @Type("DiagramGenerator\Config\ThemeColor")
     * @Valid()
     * @var \DiagramGenerator\Config\ThemeColor
     */
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
