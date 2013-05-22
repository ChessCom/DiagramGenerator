<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\ThemeColor;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Diagram\Caption;

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

        $this->image->newImage(
            $this->board->getImage()->getImageWidth(),
            $this->board->getImage()->getImageHeight(),
            $this->getBackgroundColor()
        );
        $this->image->compositeImage($this->board->getImage(), \Imagick::COMPOSITE_DEFAULT, 0, 0);

        if ($this->getCaptionText()) {
            // Add border to diagram (image padding)
            $this->image->borderImage(
                $this->getBackgroundColor(),
                $this->getBorderThickness(),
                $this->getBorderThickness()
            );

            $caption = $this->createCaption();

            // Add caption to diagram
            $this->image->addImage($caption->getImage());

            // Add bottom padding
            $this->image->newImage(
                $this->image->getImageWidth(),
                $this->getBorderThickness(),
                $this->getBackgroundColor()
            );
            $this->image->resetIterator();
            $this->image = $this->image->appendImages(true);
        }

        // TODO: Add coordinates
        // if ($this->config->getCoordinates()) {
        // }

        $this->image->setImageFormat('jpeg');

        return $this;
    }

    /**
     * Creates caption
     * @return \DiagramGenerator\Diagram\Caption
     */
    public function createCaption()
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
     * @return string
     */
    protected function getCaptionText()
    {
        return $this->config->getCaption();
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
        return new \ImagickPixel($this->config->getTheme()->getColor()->getBackground());
    }

    /**
     * @return integer
     */
    protected function getBorderThickness()
    {
        return $this->board->getCellSize() / 2;
    }
}
