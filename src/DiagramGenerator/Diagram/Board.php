<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Generator;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;

/**
 * Class responsible for drawing the board
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Board
{
    /**
     * @var \Imagick
     */
    protected $image;

    /**
     * @var \DiagramGenerator\Diagram\Config
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
     * Draws board itself
     * @return self
     */
    public function drawBoard()
    {
        $boardSize = $this->getCellSize() * 8;
        $this->image->newImage(
            $boardSize,
            $boardSize,
            new \ImagickPixel($this->config->getTheme()->getColor()->getBackground())
        );

        return $this;
    }

    /**
     * Draws cells on the board
     * @return self
     */
    public function drawCells()
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
     * Add figures to the board
     * @return self
     */
    public function drawFigures()
    {
        $fen = Fen::createFromString($this->config->getFen());
        foreach ($fen->getPieces() as $piece) {
            $pieceImage = new \Imagick($this->getPieceImagePath($piece));
            $pieceImage->scaleImage($this->getCellSize(), $this->getCellSize());
            $this->image->compositeImage(
                $pieceImage,
                \Imagick::COMPOSITE_DEFAULT,
                $this->getCellSize() * $piece->getColumn(),
                $this->getCellSize() * $piece->getRow()
            );
        }

        return $this;
    }

    /**
     * Draws border. Must be called last
     * @return self
     */
    public function drawBorder()
    {
        $borderThickness = $this->config->getSize()->getBorder();
        $this->image->borderImage(
            new \ImagickPixel($this->config->getTheme()->getColor()->getBorder()),
            $borderThickness,
            $borderThickness
        );

        return $this;
    }

    /**
     * Draws the board image
     * @return self
     */
    public function draw()
    {
        $this->image->setImageFormat('jpeg');

        return $this;
    }

    /**
     * Shortcut to get cell size
     * @return integer
     */
    public function getCellSize()
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

    /**
     * Returns piece image path
     * @param  \DiagramGenerator\Fen\Piece $piece
     * @return string
     */
    protected function getPieceImagePath(Piece $piece)
    {
        $filename = sprintf("%s/%s%s.png",
            $this->config->getTheme()->getName(),
            substr($piece->getColor(), 0, 1),
            $piece->getKey()
        );

        return sprintf("%s/%s", Generator::getResourcesDir() . '/pieces', $filename);
    }
}
