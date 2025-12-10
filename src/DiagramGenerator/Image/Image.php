<?php

namespace DiagramGenerator\Image;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Board;
use DiagramGenerator\Fen;
use DiagramGenerator\Generator;
use DiagramGenerator\Image\StorageInterface;
use Intervention\Image\Gd\Decoder;
use Intervention\Image\Gd\Font;
use Intervention\Image\Image as BaseImage;

class Image
{
    const HIGHLIGHTED_OPACITY = 63; // alpha, semi transparent

    /** @var BaseImage */
    protected $image;

    /** @var StorageInterface */
    protected $storage;

    /** @var Config */
    protected $config;

    /**
     * @param StorageInterface $storage
     * @param Config $config
     */
    public function __construct($storage, Config $config)
    {
        $this->image = (new Decoder())->initFromGdResource(imagecreatetruecolor(1, 1));
        $this->storage = $storage;
        $this->config = $config;
    }

    /**
     * Get image format
     *
     * @return string
     */
    public function getFormat()
    {
        if ($this->config->getTexture() && $this->config->getTexture()->isJpg()) {
            return Texture::IMAGE_FORMAT_JPG;
        }

        return Texture::IMAGE_FORMAT_PNG;
    }

    /**
     * Adds caption to board image (based on config passed)
     */
    public function addCaption()
    {
        $preResizeImageHeight = $this->image->getHeight();

        $this->image->resizeCanvas(
            0,
            $this->image->getHeight() + $this->config->getBorderThickness() * 2,
            'top',
            false,
            $this->config->getBackgroundColor()
        );

        $this->image->text(
            $this->config->getCaption(),
            $this->image->getWidth() / 2,
            $preResizeImageHeight + $this->config->getBorderThickness() / 2,
            function (Font $font) {
                $font->file(sprintf('%s/fonts/%s', Generator::getResourcesDir(), 'arialbd.ttf'));
                $font->size($this->config->getSize()->getCaption());
                $font->align('center');
                $font->valign('top');
            }
        );
    }

    /**
     * Adds coordinates to board image (based on config passed)
     *
     * @param int $topPaddingOfCell
     * @param bool $isCoordinatesInside
     */
    public function addCoordinates(int $topPaddingOfCell, bool $isCoordinatesInside = false): void
    {
        if (!$isCoordinatesInside) {
            $this->drawBorder();

            $x1 = (int) ($this->config->getBorderThickness() / 2);
            $y1 = $this->config->getBorderThickness() + $topPaddingOfCell;
            $x2 = $this->config->getSize()->getCell();
            $y2 = (int) ($this->image->getHeight() - $this->config->getBorderThickness() / 2);
        } else {
            $x1 = (int) ($this->config->getBorderThickness() * 0.2);
            $y1 = (int) ($this->config->getBorderThickness() * 0.3);
            $x2 = (int) ($this->config->getSize()->getCell() * 0.9);
            $y2 = (int) ($this->image->getHeight() - $this->config->getBorderThickness() * 0.25);
        }

        $fontSetup = function (Font $font) {
            $font->file(sprintf('%s/fonts/%s', Generator::getResourcesDir(), 'tahoma.ttf'));
            $font->size($this->config->getSize()->getCoordinates());
            $font->align('center');
            $font->valign('middle');
        };

        // Add vertical coordinates
        foreach ($this->getVerticalCoordinates($this->config->getFlip()) as $index => $x) {
            $this->image->text(
                abs($x - 9),
                $x1,
                $y1 + $this->config->getSize()->getCell() * $index,
                $fontSetup
            );
        }

        // Add horizontal coordinates
        foreach ($this->getHorizontalCoordinates($this->config->getFlip()) as $index => $y) {
            $this->image->text(
                $y,
                $x2 + $this->config->getSize()->getCell() * $index,
                $y2,
                $fontSetup
            );
        }
    }

    /**
     * Draws basic "usable" board, with pieces
     *
     * @param $cellSize
     * @param $topPaddingOfCell
     */
    public function drawBoardWithFigures(Fen $fen, $cellSize, $topPaddingOfCell)
    {
        $this->image = $this->drawBoard($this->storage->getBackgroundTextureImage($this->config), $cellSize, $topPaddingOfCell, $this->config->hasThemeUrls());

        $boardHasTexture = $this->config->hasThemeUrls() 
            ? isset($this->config->getThemeUrls()['board']) && !empty($this->config->getThemeUrls()['board'])
            : !empty($this->config->getTexture());                  

        $this->drawCells(
            $this->config->getSize()->getCell(),
            $this->config->getDark(),
            $this->config->getLight(),
            $this->config->getHighlightSquaresColor(),
            $topPaddingOfCell,
            $boardHasTexture,
            $this->config->getHighlightSquares()
        );

        $this->drawFigures($fen, $this->config, $topPaddingOfCell);
    }

    /**
     * @return string
     */
    public function getImageStream()
    {
        ob_start();

        if ($this->config->getTexture() && $this->config->getTexture()->isJpg()) {
            imagejpeg($this->image->getCore(), null);
        } else {
            imagepng($this->image->getCore(), null);
        }

        $stream = ob_get_contents();
        ob_end_clean();

        return $stream;
    }

    /**
     * Adds border for coordinates around image
     */
    protected function drawBorder()
    {
        $this->image->resizeCanvas(
            $this->image->getWidth() + $this->config->getBorderThickness(),
            $this->image->getHeight() + $this->config->getBorderThickness(),
            'top-right',
            false,
            $this->config->getBackgroundColor()
        );
    }

    protected function drawBoard(BaseImage $backgroundTexture = null, $cellSize, $topPaddingOfCell, $hasThemeUrls = false)
    {
        $baseBoard = $this->getBaseBoard($backgroundTexture, $cellSize, $topPaddingOfCell, $hasThemeUrls);

        return (new Decoder())->initFromGdResource($baseBoard);
    }

    protected function getBaseBoard(BaseImage $backgroundTexture = null, $cellSize, $topPaddingOfCell, $hasThemeUrls = false)
    {
        $board = imagecreatetruecolor(
            $cellSize * Board::SQUARES_IN_ROW,
            $cellSize * Board::SQUARES_IN_ROW + $topPaddingOfCell
        );

        if ($backgroundTexture) {
            $this->addTransparencyIfNeeded($board, $backgroundTexture->getCore());

            $destWidth = $cellSize * Board::SQUARES_IN_ROW;
            $destHeight = $cellSize * Board::SQUARES_IN_ROW + $topPaddingOfCell;
            $srcWidth = $hasThemeUrls ? $backgroundTexture->getWidth() : $cellSize * Board::SQUARES_IN_ROW;
            $srcHeight = ($hasThemeUrls ? $backgroundTexture->getHeight() : $cellSize * Board::SQUARES_IN_ROW) + $topPaddingOfCell;

            imagecopyresampled(
                $board, $backgroundTexture->getCore(),
                0, 0, 0, 0,
                $destWidth,
                $destHeight,
                $srcWidth,
                $srcHeight
            );
        }

        return $board;
    }

    protected function drawFigures(Fen $fen, Config $config, $topPaddingOfCell)
    {
        $cellSize = $config->getSize()->getCell();

        foreach ($fen->getPieces() as $piece) {
            $pieceImage = $this->storage->getPieceImage($piece, $config);

            $this->image = $this->image->insert(
                $pieceImage,
                'top-left',
                $cellSize * $piece->getColumn(),
                $cellSize * ($piece->getRow() + 1) - $pieceImage->getHeight() + $topPaddingOfCell
            );
        }
    }

    protected function drawCells(
        $cellSize,
        $darkColor,
        $lightColor,
        $highlightColor,
        $topPaddingOfCell,
        $boardHasTexture = false,
        $highlightedSquares = []
    ) {
        if ($boardHasTexture && empty($highlightedSquares)) {
            return; // nothing to do here
        }

        for ($x = 1; $x <= Board::SQUARES_IN_ROW; ++$x) {
            for ($y = 1; $y <= Board::SQUARES_IN_ROW; ++$y) {
                if (!$boardHasTexture) {
                    list($red, $green, $blue) = $this->colorHexToRedGreenBlue(($x + $y) % 2 ? $darkColor : $lightColor);
                    $this->drawCellRectangle($x, $y, $cellSize, $red, $green, $blue, $topPaddingOfCell);
                }

                if (!empty($highlightedSquares) && $this->isSquareHighlighted($x, $y, $highlightedSquares)) {
                    list($red, $green, $blue) = $this->colorHexToRedGreenBlue($highlightColor);
                    $this->drawCellRectangle($x, $y, $cellSize, $red, $green, $blue, $topPaddingOfCell, self::HIGHLIGHTED_OPACITY);
                }
            }
        }
    }

    protected function drawCellRectangle($x, $y, $cellSize, $red, $green, $blue, $topPaddingOfCell, $opacity = 0)
    {
        $cell = imagecreatetruecolor($cellSize, $cellSize);
        $fillColor = imagecolorallocatealpha($cell, $red, $green, $blue, $opacity);
        imagefill($cell, 0, 0, $fillColor);

        $this->image = $this->image->insert(
            $cell,
            $this->config->getFlip() ? 'top-right' : 'bottom-left',
            ($x - 1) * $cellSize, ($y - 1) * $cellSize + $topPaddingOfCell
        );
    }

    protected function colorHexToRedGreenBlue($hex)
    {
        list($red, $green, $blue) = sscanf($hex, '#%02x%02x%02x');

        return [$red, $green, $blue];
    }

    protected function isSquareHighlighted($x, $y, $highlightedSquares)
    {
        $rows = range('a', 'h');
        $columns = range(1, 8);

        $square = $rows[$x - 1].$columns[$y - 1];

        return in_array($square, $highlightedSquares);
    }

    protected function getVerticalCoordinates($flip = false)
    {
        $coordinates = range(1, 8);

        if ($flip) {
            $coordinates = array_reverse($coordinates);
        }

        return $coordinates;
    }

    protected function getHorizontalCoordinates($flip = false)
    {
        $coordinates = range('a', 'h');

        if ($flip) {
            $coordinates = array_reverse($coordinates);
        }

        return $coordinates;
    }

    protected function addTransparencyIfNeeded($board, $textureCore)
    {
        $rgba = imagecolorat($textureCore, 1, 1);
        $alpha = ($rgba & 0x7F000000) >> 24;
        $isTransparent = $alpha > 0;

        if ($isTransparent) {
            imagealphablending($board, false);
            imagesavealpha($board, true);
            $color = imagecolorallocatealpha($board, 255, 255, 255, 127);
            imagefill($board, 0, 0, $color);
        }
    }
}
