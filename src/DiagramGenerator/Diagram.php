<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\ThemeColor;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Diagram\Caption;
use DiagramGenerator\Diagram\Coordinate;

/**
 * Class which represents diagram image
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Diagram
{
    /**
     * @var \DiagramGenerator\Config
     */
    protected $config;

    /**
     * @var \Imagick
     */
    protected $image;

    /**
     * @var \DiagramGenerator\Diagram\Board
     */
    protected $board;

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
     * Gets the value of config.
     *
     * @return \DiagramGenerator\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Gets the value of board.
     *
     * @return \DiagramGenerator\Diagram\Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Sets the value of board.
     *
     * @param \DiagramGenerator\Diagram\Board $board the board
     *
     * @return self
     */
    public function setBoard(Board $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Draw diagram
     * @return self
     */
    public function draw()
    {
        if (!$this->board) {
            throw new \InvalidArgumentException('Board must be set');
        }

        $this->image->addImage($this->board->getImage());
        if ($this->config->getBoard()->getCoordinates()) {
            // Add border to diagram
            $this->drawBorder();

            // Add vertical coordinates
            foreach (Coordinate::getVerticalCoordinates() as $index => $x) {
                $coordinate = $this->createCoordinate($this->getBorderThickness(), $this->board->getCellSize(), abs($x - 9));
                $this->image->compositeImage(
                    $coordinate->getImage(),
                    \Imagick::COMPOSITE_DEFAULT,
                    0,
                    $this->getBorderThickness() + $this->board->getCellSize() * $index
                );
            }

            // Add horizontal coordinates
            foreach (Coordinate::getHorizontalCoordinates() as $index => $y) {
                $coordinate = $this->createCoordinate($this->board->getCellSize(), $this->getBorderThickness(), $y);
                $this->image->compositeImage(
                    $coordinate->getImage(),
                    \Imagick::COMPOSITE_DEFAULT,
                    $this->getBorderThickness() + $this->board->getCellSize() * $index,
                    $this->getBorderThickness() + $this->board->getImage()->getImageHeight()
                );
            }
        }

        if ($this->getCaptionText()) {
            // Add border to diagram
            $this->drawBorder();

            // Create and add caption to image
            $caption = $this->createCaption();

            // Additional padding if coordinates were added
            if ($this->config->getBoard()->getCoordinates()) {
                $caption->drawBorder($this->getBackgroundColor(), 0, $caption->getImage()->getImageHeight() / 2);
            }

            $this->image->addImage($caption->getImage());

            // Add bottom padding
            if (!$this->config->getBoard()->getCoordinates()) {
                $this->image->newImage(
                    $this->image->getImageWidth(),
                    $this->getBorderThickness(),
                    $this->getBackgroundColor()
                );
            }
            $this->image->resetIterator();
            $this->image = $this->image->appendImages(true);
        }

        $this->image->setImageFormat('png');

        return $this;
    }

    /**
     * Draws the image border
     * @return null
     */
    protected function drawBorder()
    {
        // Check if border has been already drawn
        if ($this->image->getImageWidth() > $this->board->getImage()->getImageWidth()) {
            return;
        }

        $this->image->borderImage(
            $this->getBackgroundColor(),
            $this->getBorderThickness(),
            $this->getBorderThickness()
        );
    }

    /**
     * @param  integer $width
     * @param  integer $height
     * @param  string  $text
     * @return \DiagramGenerator\Diagram\Coordinate
     */
    protected function createCoordinate($width, $height, $text)
    {
        $coordinate = new Coordinate($this->config);
        $draw       = $coordinate->getDraw();

        // Create image
        $coordinate->getImage()->newImage($width, $height, $this->getBackgroundColor());

        // Add text
        $coordinate->getImage()->annotateImage($draw, 0, 0, 0, $text);
        $coordinate->getImage()->setImageFormat('png');

        return $coordinate;
    }

    /**
     * Creates caption
     * @return \DiagramGenerator\Diagram\Caption
     */
    protected function createCaption()
    {
        $caption = new Caption($this->config);
        $draw    = $caption->getDraw();
        $metrics = $caption->getMetrics($draw);

        // Create image
        $caption->getImage()->newImage(
            $this->image->getImageWidth(),
            $metrics['textHeight'],
            $this->getBackgroundColor()
        );

        // Add text
        $caption->getImage()->annotateImage($draw, 0, 0, 0, $this->getCaptionText());
        $caption->getImage()->setImageFormat('png');

        return $caption;
    }

    /**
     * Returns caption text
     *
     * @return string
     */
    protected function getCaptionText()
    {
        return $this->config->getBoard()->getCaption();
    }

    /**
     * Returns font path by font filename
     * @param  string $filename
     * @return string
     */
    protected function getFont($filename)
    {
        return realpath(sprintf("%s/Resources/fonts/%s", __DIR__, $filename));
    }

    /**
     * @return \ImagickPixel
     */
    protected function getBackgroundColor()
    {
        return new \ImagickPixel($this->config->getBoard()->getBackgroundColor());
    }

    /**
     * @return integer
     */
    protected function getBorderThickness()
    {
        return $this->board->getCellSize() / 2;
    }
}
