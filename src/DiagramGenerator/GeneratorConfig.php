<?php

namespace DiagramGenerator;

use JMS\Serializer\Annotation\Type;

class GeneratorConfig
{
    // const COORDINATES = array(false, false);

    const DEFAULT_BOTTOM = 0;

    /**
     * @Type("string")
     * @var string
     */
    protected $fen = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    /**
     * @Type("integer")
     * @var integer
     */
    protected $size = 0;

    /**
     * @Type("string")
     * @var string
     */
    protected $caption = '';

    /**
     * @Type("string")
     * @var string
     */
    protected $filename = '';

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
     * @Type("integer")
     * @var integer
     */
    // protected $coordinates = 0;

    /**
     * @Type("boolean")
     * @var boolean
     */
    protected $bottom = false;

    /**
     * @Type("integer")
     * @var integer
     */
    protected $theme = 2;

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
     * Gets the value of size.
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the value of size.
     *
     * @param integer $size the size
     *
     * @return self
     */
    public function setSize($size)
    {
        $this->size = $size;

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
     * Gets the value of filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the value of filename.
     *
     * @param string $filename the filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Gets the value of light.
     *
     * @return string
     */
    public function getLight()
    {
        $decimalLight = hexdec($this->light);

        return array(
            ($decimalLight >> 16) & 0xFF,
            ($decimalLight >> 8) & 0xFF,
            $decimalLight & 0xFF
        );
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
        $decimalDark = hexdec($this->dark);

        return array(
            ($decimalDark >> 16) & 0xFF,
            ($decimalDark >> 8) & 0xFF,
            $decimalDark & 0xFF
        );
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
     * Gets the value of coordinates.
     *
     * @return integer
     */
    // public function getCoordinates()
    // {
    //     if ($this->coordinates < 0 || $this->coordinates >= count(self::COORDINATES)) {
    //         return self::DEFAULT_COORDINATES;
    //     };

    //     return $this->coordinates;
    // }

    /**
     * Sets the value of coordinates.
     *
     * @param integer $coordinates the coordinates
     *
     * @return self
     */
    // public function setCoordinates($coordinates)
    // {
    //     $this->coordinates = $coordinates;

    //     return $this;
    // }

    /**
     * Gets the value of bottom.
     *
     * @return boolean
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * Sets the value of bottom.
     *
     * @param boolean $bottom the bottom
     *
     * @return self
     */
    public function setBottom($bottom)
    {
        $this->bottom = (bool) $bottom;

        return $this;
    }

    /**
     * Gets the value of theme.
     *
     * @return integer
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Sets the value of theme.
     *
     * @param integer $theme the theme
     *
     * @return self
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Returns all config properties in array key => value
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
        return substr($fen, 0, strpos($fen, ' '));
    }
}
