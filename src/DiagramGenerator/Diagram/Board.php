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

    /**
     * @var \DiagramGenerator\Diagram\Config
     */
    protected $config;

    /**
     * @var \DiagramGenerator\Fen $fen
     */
    protected $fen;

    /**
     * @var int $paddingTop
     */
    protected $paddingTop = 0;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->image  = new \Imagick();
        $this->fen = Fen::createFromString($this->config->getFen());

        if ($this->config->getFlip()) {
            $this->fen->flip();
        }
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
        $this->paddingTop = $this->getMaxPieceHeight() - $this->getCellSize();
        $this->image->newImage(
            $this->getCellSize() * 8,
            $this->getCellSize() * 8 + $this->paddingTop,
            new \ImagickPixel('none')
        );

        // Add board texture
        if ($this->getBoardTexture()) {
            $background = new \Imagick($this->getBackgroundTexture());
            $textureSize = $this->getCellSize() * 2;

            $this->image->compositeImage(
                $background, \Imagick::COMPOSITE_DEFAULT, 0, $this->paddingTop
            );
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
        for ($x = 1; $x <= 8; $x++) {
            for ($y = 1; $y <= 8; $y++) {
                $this->drawCell($x, $y, ($x + $y) % 2);
            }
        }

        return $this;
    }

    /**
     * Draw a single cell
     *
     * @param int  $x
     * @param int  $y
     * @param bool $colorIndex
     */
    public function drawCell($x, $y, $colorIndex)
    {
        $cell = new \ImagickDraw();

        if (is_array($this->config->getHighlightSquares()) &&
            in_array($this->getSquare($x, $y), $this->config->getHighlightSquares())) {

            $cell->setFillColor($this->config->getHighlightSquaresColor());
            $cell->setFillOpacity(
                $colorIndex ? self::HIGHLIGHTED_DARK_SQUARE_OPACITY : self::HIGHLIGHTED_LIGHT_SQUARE_OPACITY
            );
        } else {
            if ($this->getBoardTexture()) {
                return;
            }

            $cell->setFillColor($colorIndex ? $this->getDarkCellColor() : $this->getLightCellColor());
        }

        $cell->rectangle(
            ($x - 1) * $this->getCellSize(),
            ($y - 1) * $this->getCellSize() + $this->paddingTop,
            $x * $this->getCellSize(),
            $y * $this->getCellSize() + $this->paddingTop
        );

        $this->image->drawImage($cell);
    }

    /**
     * Add figures to the board
     * @return self
     */
    public function drawFigures()
    {
        foreach ($this->fen->getPieces() as $piece) {
            $pieceImage = new \Imagick($this->getPieceImagePath($piece));

            $this->image->compositeImage(
                $pieceImage,
                \Imagick::COMPOSITE_DEFAULT,
                $this->getCellSize() * $piece->getColumn(),
                // some pieces are not the same hight as the cell and they need to be adjusted
                $this->getCellSize() * ($piece->getRow() + 1) - $pieceImage->getImageHeight() + $this->paddingTop
            );
        }

        return $this;
    }

    /**
     * Draws border. Must be called last
     * @deprecated
     * needs to be updated to handle boards with 3d pieces correctly
     */
    public function drawBorder()
    {
        /*
        $this->image->borderImage(
            new \ImagickPixel($this->getBorderColor()),
            $this->getBorderSize(),
            $this->getBorderSize()
        );*/

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
     * @return integer
     */
    public function getCellSize()
    {
        return $this->config->getSize()->getCell();
    }

    /**
     * @return string
     */
    protected function getBorderSize()
    {
        return $this->config->getSize()->getBorder();
    }

    /**
     * @return string
     */
    protected function getBackgroundColor()
    {
        return $this->config->getTheme()->getColor()->getBackground();
    }

    /**
     * @return string
     */
    protected function getBorderColor()
    {
        return $this->config->getTheme()->getColor()->getBorder();
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
     * @return \DiagramGenerator\Config\ThemeTexture
     */
    protected function getBoardTexture()
    {
        return $this->config->getTexture() ? $this->config->getTexture()->getBoard() : null;
    }

    /**
     * Returns piece image path
     * @param  \DiagramGenerator\Fen\Piece $piece
     *
     * @return string
     */
    protected function getPieceImagePath(Piece $piece)
    {

        $filename = sprintf("%s%s.png",
            substr($piece->getColor(), 0, 1),
            $piece->getKey()
        );

        return sprintf("http://d1xrj4tlyewhek.cloudfront.net/pieces/%s/%s/%s",
            $this->config->getTheme()->getName(),
            $this->getCellSize(),
            $filename
        );
    }

    /**
     * Returns board background image path
     *
     * @return string
     */
    protected function getBackgroundTexture()
    {
        return sprintf("http://d1xrj4tlyewhek.cloudfront.net/boards/%s/%s.png",
            $this->getBoardTexture(),
            $this->getCellSize()
        );
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

        return $squares[$x-1] . (8 - $y + 1);
    }

    /**
     * Get the largest piece height
     *
     * @return int
     */
    protected function getMaxPieceHeight()
    {
        $maxHeight = $this->getCellSize();
        foreach ($this->fen->getPieces() as $piece) {
            $pieceImage = new \Imagick($this->getPieceImagePath($piece));

            if ($pieceImage->getImageHeight() > $maxHeight) {
                $maxHeight = $pieceImage->getImageHeight();
            }

            unset($pieceImage);
        }

        return $maxHeight;
    }
}
