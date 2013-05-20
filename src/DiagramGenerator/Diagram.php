<?php

namespace DiagramGenerator;

use DiagramGenerator\GeneratorConfig;
use DiagramGenerator\Theme\ThemeColor;

/**
 * Class which represents diagram image
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Diagram
{
    /**
     * @var \DiagramGenerator\GeneratorConfig
     */
    protected $config;

    /**
     * @var \Imagick
     */
    protected $diagram;

    /**
     * @var \Imagick
     */
    protected $board;

    /**
     * @var \Imagick
     */
    protected $caption;

    public function __construct(GeneratorConfig $config, \Imagick $board)
    {
        $this->board   = $board;
        $this->config  = $config;
        $this->diagram = new \Imagick();
    }

    /**
     * Gets the value of config.
     *
     * @return \DiagramGenerator\GeneratorConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the value of config.
     *
     * @param \DiagramGenerator\GeneratorConfig $config the config
     *
     * @return self
     */
    public function setConfig(GeneratorConfig $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Gets the value of board.
     *
     * @return \Imagick
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Sets the value of board.
     *
     * @param \Imagick $board the board
     *
     * @return self
     */
    public function setBoard(\Imagick $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return \Imagick
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param \Imagick $caption the caption
     *
     * @return self
     */
    public function setCaption(\Imagick $caption)
    {
        $this->caption = $caption;

        return $this;
    }

    public function draw()
    {
        if ($this->caption) {
            // Attach caption
        }

        if ($this->config->getCoordinates()) {
            // Add coordinates
        }

        $this->diagram->setImageFormat('jpeg');

        return $this->diagram;
    }

    /**
     * Adds caption to image
     * @param  GeneratorConfig $config
     * @param  Size            $size
     * @param  Theme           $theme
     * @return null
     */
    protected function createCaption(GeneratorConfig $config, Size $size, Theme $theme)
    {
        $textSize = $size->getCaption()->getSize();
        $textBox  = imagettfbbox($textSize, 0, $this->getFont($theme->getFont()), $config->getCaption());
        $pointX   = $this->dimensions['width'] - ($textBox[2] - $textBox[0]);
        $pointY   = $dimensions['height'] -
        imagettftext($image, $textSize, 0, $x, $caption_base, $caption_color, $CAPTION_FONT, $caption);


        header('Content-Type: image/png');
        imagepng($image);
        exit;

    }

    protected function getFont($filename)
    {
        return realpath(sprintf("%s/Resources/fonts/%s", __DIR__, $filename));
    }
}
