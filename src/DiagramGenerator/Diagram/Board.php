<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\GeneratorConfig;

class Board
{
    /**
     * @var \Imagick
     */
    protected $image;

    public function __construct(GeneratorConfig $config)
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
     * Draws board itself
     * @return self
     */
    public function drawBoard()
    {
        $boardSize = $this->getCellSize() * 8;
        $this->image->newImage(
            $boardSize,
            $boardSize,
            new \ImagickPixel($this->config->getTheme()->getColor()->getFrame())
        );
        $this->drawCells();
        $this->image->setImageFormat('jpeg');

        return $this;
    }

    public function drawFigures()
    {
        # code...
    }

    /**
     * Draws border. Must be called last
     * @return self
     */
    public function drawBorder()
    {
        $borderThickness = $this->config->getSize()->getFrameThick();
        $this->image->borderImage(
            new \ImagickPixel($this->config->getTheme()->getColor()->getOutline()),
            $borderThickness,
            $borderThickness
        );

        return $this;
    }

    /**
     * Draws cells on the board
     * @return self
     */
    protected function drawCells()
    {
        for ($x = 1; $x <= 8; $x++) {
            for ($y = 1; $y <= 8; $y++) {
                $colorIndex = ($x + $y) % 2;
                $cell = new \ImagickDraw();
                $cell->setFillColor($colorIndex ? $this->getDarkCellColor() : $this->getLightCellColor());
                $cell->rectangle(
                    ($x - 1) * $this->getCellSize(),
                    ($y - 1) * $this->getCellSize(),
                    $x * $this->getCellSize(),
                    $y * $this->getCellSize()
                );
                $this->image->drawImage($cell);
            }
        }

        return $this;
    }

    /**
     * Shortcut to get cell size
     * @return integer
     */
    protected function getCellSize()
    {
        return $this->config->getSize()->getCell();
    }

    /**
     * Returns light cell color
     * @return \ImagickPixel
     */
    protected function getLightCellColor()
    {
        return new \ImagickPixel($this->config->getLight());
    }

    /**
     * Returns dark cell color
     * @return \ImagickPixel
     */
    protected function getDarkCellColor()
    {
        return new \ImagickPixel($this->config->getDark());
    }
}