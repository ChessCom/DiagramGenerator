<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Generator;

class Coordinate
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
     * @return array
     */
    public static function getVerticalCoordinates()
    {
        return range(1, 8);
    }

    /**
     * @return array
     */
    public static function getHorizontalCoordinates()
    {
        return range('a', 'h');
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
     * Draw object which with caption text ready
     * to be added into the image
     * @return \ImagickDraw
     */
    public function getDraw()
    {
        $draw = new \ImagickDraw();
        $draw->setFont($this->getFont());
        $draw->setGravity(\Imagick::GRAVITY_CENTER);
        $draw->setFontSize($this->getCoordinatesSize());

        return $draw;
    }

    /**
     * @return integer
     */
    protected function getCoordinatesSize()
    {
        return $this->config->getSize()->getCoordinates();
    }

    /**
     * Path to the font
     *
     * @return string
     */
    protected function getFont()
    {
        return sprintf("%s/fonts/%s", Generator::getResourcesDir(), $this->config->getBoard()->getCoordinatesFont());
    }
}
