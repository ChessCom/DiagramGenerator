<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Generator;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;

/**
 * Class responsible for drawing the board
 *
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Board
{
    const HIGHLIGHTED_DARK_SQUARE_OPACITY = 1;
    const HIGHLIGHTED_LIGHT_SQUARE_OPACITY = .5;

    /**
     * @var \Imagick
     */
    protected $image;

    /** @var \DiagramGenerator\Config $config */
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
     *
     * @return self
     */
    public function drawBoard()
    {
        $boardSize = $this->getCellSize() * 8;
        $this->image->newImage(
            $boardSize,
            $boardSize,
            new \ImagickPixel($this->config->getBoard()->getBackgroundColor())
        );

        // Add board texture
        if ($this->config->getBoard()->getTextureFilename()) {
            $background = new \Imagick($this->getBackgroundTextureImagePath());
            $textureSize = $this->getCellSize() * 2;
            $background->scaleImage($textureSize, $textureSize);
            for ($x = 0; $x < 4; $x++) {
                for ($y = 0; $y < 4; $y++) {
                    $this->image->compositeImage(
                        $background,
                        \Imagick::COMPOSITE_DEFAULT,
                        $x * $textureSize,
                        $y * $textureSize
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Draws cells on the board
     *
     * @return self
     */
    public function drawCells()
    {
        for ($x = 0; $x < 8; $x++) {
            for ($y = 0; $y < 8; $y++) {
                $this->drawCell($x, $y, ($x + $y) % 2);
            }
        }

        return $this;
    }

    /**
     * Draw a single cell on the board image (coordinates x,y starting from 0)
     *
     * @param int $x
     * @param int $y
     */
    protected function drawCell($x, $y, $colorIndex)
    {
        $cell = new \ImagickDraw();

        if (in_array($this->getSquare($x, $y), $this->config->getBoard()->getHighlightSquares())) {
            $cell->setFillColor($this->config->getBoard()->getHighlightSquaresColor());
            $cell->setFillOpacity(
                $colorIndex ? self::HIGHLIGHTED_DARK_SQUARE_OPACITY : self::HIGHLIGHTED_LIGHT_SQUARE_OPACITY
            );
        } else {
            if ($this->config->getBoard()->getTextureFilename()) {
                return;
            }

            if ($colorIndex) {
                $cell->setFillColor(new \ImagickPixel($this->config->getBoard()->getDarkCellColor()));
            } else {
                $cell->setFillColor(new \ImagickPixel($this->config->getBoard()->getLightCellColor()));
            }
        }

        $cell->rectangle(
            $x * $this->getCellSize(),
            $y * $this->getCellSize(),
            ($x + 1) * $this->getCellSize(),
            ($y + 1) * $this->getCellSize()
        );

        $this->image->drawImage($cell);
    }

    /**
     * Add figures to the board
     * @return self
     */
    public function drawFigures()
    {
        $fen = Fen::createFromString($this->config->getFen());
        if ($this->config->getBoard()->getFlip()) {
            $fen->flip();
        }

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
     * Draws the border. Must be called last
     *
     * @return Board
     */
    public function drawBorder()
    {
        $this->image->borderImage(
            new \ImagickPixel($this->config->getBoard()->getBorderColor()),
            $this->config->getSize()->getBorder(),
            $this->config->getSize()->getBorder()
        );

        return $this;
    }

    /**
     * Draws the board image
     * @return self
     */
    public function draw()
    {
        $this->image->setImageFormat('png');

        return $this;
    }

    /**
     * Shortcut to get cell size
     *
     * @return integer
     */
    public function getCellSize()
    {
        return $this->config->getSize()->getCell();
    }

    /**
     * Return the square for the coordinates passed (starting from 0)
     *
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    protected function getSquare($x, $y)
    {
        $squares = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');

        return $squares[$x] . (8 - $y);
    }

    /**
     * Returns piece image path
     * @param  \DiagramGenerator\Fen\Piece $piece
     *
     * @return string
     */
    protected function getPieceImagePath(Piece $piece)
    {
        $filename = sprintf("%s/%s%s.png",
            $this->config->getPieceTheme(),
            substr($piece->getColor(), 0, 1),
            $piece->getKey()
        );

        return sprintf("%s/pieces/%s", Generator::getResourcesDir(), $filename);
    }

    /**
     * Returns board background image path
     *
     * @return string
     */
    protected function getBackgroundTextureImagePath()
    {
        return sprintf(
            "%s/boards/%s.jpg", Generator::getResourcesDir(), $this->config->getBoard()->getTextureFilename()
        );
    }
}
