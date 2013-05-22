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
        $draw->setFontSize($this->config->getSize()->getCoordinates());

        return $draw;
    }

    /**
     * Path to the font
     * TODO: move coordinate font to config
     * @return string
     */
    protected function getFont()
    {
        return sprintf("%s/fonts/%s", Generator::getResourcesDir(), 'tahoma.ttf');
    }
}
