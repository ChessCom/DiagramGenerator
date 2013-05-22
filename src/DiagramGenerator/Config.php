<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Theme;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @ExclusionPolicy("none")
 */
class Config
{
    /**
     * @Type("string")
     * @var string
     */
    protected $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    /**
     * @Type("integer")
     * @SerializedName("size")
     * @var integer
     */
    protected $sizeIndex = 0;

    /**
     * @Exclude()
     * @var \DiagramGenerator\Config\Size
     */
    protected $size;

    /**
     * @Type("integer")
     * @SerializedName("theme")
     * @var integer
     */
    protected $themeIndex = 3;

    /**
     * @Exclude()
     * @var \DiagramGenerator\Config\Theme
     */
    protected $theme;

    /**
     * @Type("string")
     * @var string
     */
    protected $caption = '';

    /**
     * @Type("boolean")
     * @var boolean
     */
    protected $coordinates = false;

    /**
     * @Type("string")
     * @var string
     */
    protected $light = 'eeeed2';

    /**
     * @Type("string")
     * @var string
     */
    protected $dark = '769656';

    /**
     * Gets the value of fen.
     *
     * @return string
     */
    public function getFen()
    {
        return $this->sanitizeFen($this->fen);
    }

    /**
     * Sets the value of fen.
     *
     * @param string $fen the fen
     *
     * @return self
     */
    public function setFen($fen)
    {
        $this->fen = $this->sanitizeFen($fen);

        return $this;
    }

    /**
     * Gets the value of sizeIndex.
     *
     * @return integer
     */
    public function getSizeIndex()
    {
        return $this->sizeIndex;
    }

    /**
     * Sets the value of sizeIndex.
     *
     * @param integer $sizeIndex the sizeIndex
     *
     * @return self
     */
    public function setSizeIndex($sizeIndex)
    {
        $this->sizeIndex = $sizeIndex;

        return $this;
    }

    /**
     * Gets the value of size.
     *
     * @return \DiagramGenerator\Config\Size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the value of size.
     *
     * @param \DiagramGenerator\Config\Size $size the size
     *
     * @return self
     */
    public function setSize(Size $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Gets the value of themeIndex.
     *
     * @return integer
     */
    public function getThemeIndex()
    {
        return $this->themeIndex;
    }

    /**
     * Sets the value of themeIndex.
     *
     * @param integer $themeIndex the themeIndex
     *
     * @return self
     */
    public function setThemeIndex($themeIndex)
    {
        $this->themeIndex = $themeIndex;

        return $this;
    }

    /**
     * Gets the value of theme.
     *
     * @return \DiagramGenerator\Config\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Sets the value of theme.
     *
     * @param \DiagramGenerator\Config\Theme $theme the theme
     *
     * @return self
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param string $caption the caption
     *
     * @return self
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Gets the value of coordinates.
     *
     * @return boolean
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Sets the value of coordinates.
     *
     * @param boolean $coordinates the coordinates
     *
     * @return self
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = (bool) $coordinates;

        return $this;
    }

    /**
     * Gets the value of light.
     *
     * @return string
     */
    public function getLight()
    {
        return sprintf("#%s", ltrim($this->light, '#'));
    }

    /**
     * Sets the value of light.
     *
     * @param string $light the light
     *
     * @return self
     */
    public function setLight($light)
    {
        $this->light = $light;

        return $this;
    }

    /**
     * Gets the value of dark.
     *
     * @return string
     */
    public function getDark()
    {
        return sprintf("#%s", ltrim($this->dark, '#'));
    }

    /**
     * Sets the value of dark.
     *
     * @param string $dark the dark
     *
     * @return self
     */
    public function setDark($dark)
    {
        $this->dark = $dark;

        return $this;
    }

    /**
     * Returns all urlencoded config properties in array key => value
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Remove unused part from the fen
     * @param  string $fen
     * @return string
     */
    protected function sanitizeFen($fen)
    {
        return (strpos($fen, ' ') === false) ? $fen : substr($fen, 0, strpos($fen, ' '));
    }
}
