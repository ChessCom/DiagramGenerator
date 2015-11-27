<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Exception\CachedFileInvalidException;
use DiagramGenerator\Generator;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;
use ImagickDraw;
use RuntimeException;

/**
 * Class responsible for drawing the board
 *
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Board
{
    const HIGHLIGHTED_DARK_SQUARE_OPACITY = .5;
    const HIGHLIGHTED_LIGHT_SQUARE_OPACITY = .5;

    protected $squares = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');

    protected $flippedSquares = array('h', 'g', 'f', 'e', 'd', 'c', 'b', 'a');

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

    /**
     * @var
     */
    protected $rootCacheDir;

    /**
     * @var
     */
    protected $cacheDirName = 'diagram_generator';

    /**
     * @var
     */
    protected $cacheDir;

    /**
     * @var string $boardTextureUrl
     */
    protected $boardTextureUrl;

    /**
     * @var string $piece
     */
    protected $pieceThemeUrl;

    /**
     * @param Config $config
     * @param string $rootCacheDir
     * @param string $boardTextureUrl ex. /boards/__BOARD_TEXTURE__/__SIZE__
     * @param string $pieceThemeUrl   ex. /pieces/__PIECE_THEME__/__SIZE__/__PIECE__
     */
    public function __construct(Config $config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl)
    {
        $this->config = $config;
        $this->rootCacheDir = $rootCacheDir;
        $this->boardTextureUrl = $boardTextureUrl;
        $this->pieceThemeUrl = $pieceThemeUrl;

        $this->cacheDir = $this->rootCacheDir . '/' . $this->cacheDirName;

        @mkdir($this->rootCacheDir . '/' . $this->cacheDirName, 0777);

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
     *
     * @throws \ImagickException When cache file is invalid even after fetching from remote host
     */
    public function drawBoard()
    {
        $this->paddingTop = $this->getMaxPieceHeight() - $this->getCellSize();
        $this->image->newImage(
            $this->getCellSize() * count($this->squares),
            $this->getCellSize() * count($this->squares) + $this->paddingTop,
            new \ImagickPixel('none')
        );

        // Add board texture
        if ($this->config->getTexture()) {
            // leaving exception uncaught, because that's unexpected behavior, and should be logged by rollbar
            $background = $this->getBackgroundTexture();

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
        for ($x = 1; $x <= count($this->squares); $x++) {
            for ($y = 1; $y <= count($this->squares); $y++) {
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
        $cell = new ImagickDraw();

        $this->drawCellStandard($cell, $x, $y, $colorIndex);
        $this->drawCellHighlighted($cell, $x, $y, $colorIndex);
    }

    /**
     * Add figures to the board
     * @return self
     */
    public function drawFigures()
    {
        foreach ($this->fen->getPieces() as $piece) {
            $pieceImage = $this->getPieceImage($piece);

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
        $this->image->setImageFormat(Texture::IMAGE_FORMAT_PNG);

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

    public function getPaddingTop()
    {
        return $this->paddingTop;
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
     * Returns piece image path
     * @param  \DiagramGenerator\Fen\Piece $piece
     *
     * @return \Imagick
     */
    protected function getPieceImage(Piece $piece)
    {
        $pieceThemeName = $this->config->getTheme()->getName();
        $cellSize = $this->getCellSize();
        $piece = substr($piece->getColor(), 0, 1) . $piece->getKey();
        $pieceCachedPath = $this->getCachedPieceFilePath($pieceThemeName, $cellSize, $piece);

        try {
            return new \Imagick($pieceCachedPath);
        } catch (\ImagickException $exception) {
            @mkdir($this->cacheDir . '/' . $pieceThemeName . '/' . $cellSize, 0777, true);

            $pieceThemeUrl = str_replace('__PIECE_THEME__', $pieceThemeName, $this->pieceThemeUrl);
            $pieceThemeUrl = str_replace('__SIZE__', $cellSize, $pieceThemeUrl);
            $pieceThemeUrl = str_replace('__PIECE__', $piece, $pieceThemeUrl);
            $pieceThemeUrl .= '.' . Texture::IMAGE_FORMAT_PNG;

            $this->cacheImage($pieceThemeUrl, $pieceCachedPath);

            return new \Imagick($pieceCachedPath);
        }
    }

    /**
     * Returns board background image path
     *
     * @return \Imagick
     *
     * @throws \ImagickException
     */
    protected function getBackgroundTexture()
    {
        $boardCachedPath = $this->getCachedTextureFilePath();

        try {
            return new \Imagick($boardCachedPath);
        } catch (\ImagickException $exception) {
            @mkdir($this->cacheDir . '/board/' . $this->config->getTexture()->getImageUrlFolderName(), 0777, true);

            $boardTextureUrl = str_replace(
                '__BOARD_TEXTURE__', $this->config->getTexture()->getImageUrlFolderName(), $this->boardTextureUrl
            );
            $boardTextureUrl = str_replace('__SIZE__', $this->getCellSize(), $boardTextureUrl);
            $boardTextureUrl .= '.' . $this->config->getTexture()->getImageFormat();

            $this->cacheImage($boardTextureUrl, $boardCachedPath);

            return new \Imagick($boardCachedPath);
        }
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
        return $this->squares[$x-1] . (count($this->squares) - $y + 1);
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
            $pieceImage = $this->getPieceImage($piece);

            if ($pieceImage->getImageHeight() > $maxHeight) {
                $maxHeight = $pieceImage->getImageHeight();
            }

            unset($pieceImage);
        }

        return $maxHeight;
    }

    /**
     * Cache an image from a remote url to a local cache file
     *
     * @param string $remoteImageUrl
     * @param string $cachedFilePath
     */
    protected function cacheImage($remoteImageUrl, $cachedFilePath)
    {
        $cachedFilePathTmp = $cachedFilePath . uniqid('', true);
        $ch = curl_init($remoteImageUrl);
        $destinationFileHandle = fopen($cachedFilePathTmp, 'wb');

        if (!$destinationFileHandle) {
            throw new RuntimeException(sprintf('Could not open temporary file: %s', $cachedFilePathTmp));
        }

        curl_setopt($ch, CURLOPT_FILE, $destinationFileHandle);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($destinationFileHandle);

        rename($cachedFilePathTmp, $cachedFilePath);
    }

    /**
     * Get the highlighted squares for the board (considering the flip parameter)
     *
     * @param Config $config
     *
     * @return array
     */
    protected function getHighlightSquares(Config $config)
    {
        if (!$config->getFlip()) {
            return $config->getHighlightSquares();
        }

        $flippedSquares = array();
        foreach ($config->getHighlightSquares() as $square) {
            $flippedSquares[] = $this->flipSquare($square[0], $square[1]);
        }

        return $flippedSquares;
    }

    /**
     * Return the flipped representation of a square
     *
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    protected function flipSquare($x, $y)
    {
        foreach ($this->squares as $index => $square) {
            if ($square == $x) {
                return $this->flippedSquares[$index] . (count($this->squares) - $y + 1);
            }
        }
    }

    /**
     * Draw a non-highlighted square
     *
     * @param ImagickDraw $cell
     * @param int         $x
     * @param int         $y
     * @param int         $colorIndex
     */
    protected function drawCellStandard(ImagickDraw $cell, $x, $y, $colorIndex)
    {
        if ($this->config->getTexture()) {
            return;
        }

        $cell->setFillColor($colorIndex ? $this->getDarkCellColor() : $this->getLightCellColor());

        $this->drawCellRectangle($cell, $x, $y);
    }

    /**
     * Draw a highlighted cell
     *
     * @param ImagickDraw $cell
     * @param int         $x
     * @param int         $y
     * @param int         $colorIndex
     */
    protected function drawCellHighlighted(ImagickDraw $cell, $x, $y, $colorIndex)
    {
        if (!is_array($this->getHighlightSquares($this->config)) ||
            !in_array($this->getSquare($x, $y), $this->getHighlightSquares($this->config))) {
            return;
        }

        $cell->setFillColor($this->config->getHighlightSquaresColor());
        $cell->setFillOpacity(
            $colorIndex ? self::HIGHLIGHTED_DARK_SQUARE_OPACITY : self::HIGHLIGHTED_LIGHT_SQUARE_OPACITY
        );

        $this->drawCellRectangle($cell, $x, $y);
    }

    /**
     * Draw a cell rectangle
     *
     * @param ImagickDraw $cell
     * @param int         $x
     * @param int         $y
     */
    protected function drawCellRectangle(ImagickDraw $cell, $x, $y)
    {
        $cell->rectangle(
            ($x - 1) * $this->getCellSize(),
            ($y - 1) * $this->getCellSize() + $this->paddingTop,
            $x * $this->getCellSize() - 1,
            $y * $this->getCellSize() + $this->paddingTop - 1
        );

        $this->image->drawImage($cell);
    }

    /**
     * Cached texture file path
     *
     * @return string
     */
    protected function getCachedTextureFilePath()
    {
        return sprintf(
            '%s/board/%s/%d.%s',
            $this->cacheDir,
            $this->config->getTexture()->getImageUrlFolderName(),
            $this->getCellSize(),
            $this->config->getTexture()->getImageFormat()
        );
    }

    /**
     * Cached piece file path
     *
     * @return string
     */
    protected function getCachedPieceFilePath($pieceThemeName, $cellSize, $piece)
    {
        return sprintf(
            '%s/%s/%d/%s.%s',
            $this->cacheDir,
            $pieceThemeName,
            $cellSize,
            $piece,
            Texture::IMAGE_FORMAT_PNG
        );
    }
}
