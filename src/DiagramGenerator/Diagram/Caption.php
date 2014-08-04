<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Generator;

class Caption
{
    /**
     * @var \DiagramGenerator\Config;
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->image  = new \Imagick();
    }

    /**
     * Gets the value of image.
     *
     * @return \Imagick
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Draw an object with the caption text ready to be added onto the image
     *
     * @return \ImagickDraw
     */
    public function getDraw()
    {
        $caption = $this->config->getBoard()->getCaption();
        $draw = new \ImagickDraw();
        $draw->setFont($this->getFont());
        $draw->setGravity(\Imagick::GRAVITY_CENTER);
        $draw->setFontSize($this->getCaptionSize());

        return $draw;
    }

    /**
     * @param  \ImagickPixel $color
     * @param  integer       $width
     * @param  integer       $height
     * @return self
     */
    public function drawBorder(\ImagickPixel $color, $width, $height)
    {
        $this->getImage()->borderImage($color, $width, $height);

        return $this;
    }

    /**
     * @param  \ImagickDraw $draw
     *
     * @return array
     */
    public function getMetrics(\ImagickDraw $draw)
    {
        return $this->image->queryFontMetrics($draw, $this->config->getBoard()->getCaption());
    }

    /**
     * @return integer
     */
    protected function getCaptionSize()
    {
        return $this->config->getSize()->getCaption();
    }

    /**
     * Path to the font
     * TODO: move caption font to config
     * @return string
     */
    protected function getFont()
    {
        return sprintf("%s/fonts/%s", Generator::getResourcesDir(), 'arialbd.ttf');
    }
}
