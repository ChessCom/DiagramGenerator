<?php

namespace DiagramGenerator\Image;

use DiagramGenerator\Config;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Fen;
use Intervention\Image\Gd\Decoder;
use Intervention\Image\Image as BaseImage;

class Image
{
    const HIGHLIGHTED_OPACITY = 63; // alpha, semi transparent

    /** @var BaseImage */
    protected $image;

    /** @var Storage */
    protected $storage;

    public function __construct(Storage $storage)
    {
        $this->image = (new Decoder())->initFromGdResource(imagecreatetruecolor(1, 1));
        $this->storage = $storage;
    }

    public function addCaption($text, $fontSize)
    {
        $preResizeImageHeight = $this->image->height();

        $this->image->resizeCanvas(
            0,
            $this->image->height() + $this->getBorderThickness() * 2,
            'top',
            false,
            $this->getBackgroundColor()
        );

        $this->image->text(
            $this->getCaptionText(),
            $this->image->width() / 2,
            $preResizeImageHeight + $this->getBorderThickness() / 2,
            function (Font $font) {
                $font->file(sprintf('%s/fonts/%s', Generator::getResourcesDir(), 'arialbd.ttf'));
                $font->size($this->config->getSize()->getCaption());
                $font->align('center');
                $font->valign('top');
            }
        );
    }

    public function addCoordinates($fontSize)
    {

    }

    public function drawBoardWithFigures(Config $config, Fen $fen, $cellSize, $topPaddingOfCell)
    {
        $this->image = $this->drawBoard($this->storage->getBackgroundTextureImage($config), $cellSize, $topPaddingOfCell);

        $this->drawCells(
            $config->getSize()->getCell(),
            $config->getDark(),
            $config->getLight(),
            $config->getHighlightSquaresColor(),
            $topPaddingOfCell,
            empty($config->getTexture()),
            $config->getHighlightSquares()
        );

        $this->drawFigures($fen, $config, $topPaddingOfCell);
    }

    protected function drawBoard(BaseImage $backgroundTexture, $cellSize, $topPaddingOfCell)
    {
        $baseBoard = $this->getBaseBoard($backgroundTexture, $cellSize, $topPaddingOfCell);
        return (new Decoder())->initFromGdResource($baseBoard);
    }

    protected function getBaseBoard(BaseImage $backgroundTexture, $cellSize, $topPaddingOfCell)
    {
        $board = imagecreatetruecolor(
            $cellSize * Board::SQUARES_IN_ROW,
            $cellSize * Board::SQUARES_IN_ROW + $topPaddingOfCell
        );

        imagecopyresampled(
            $board, $backgroundTexture->getCore(),
            0, 0, 0, 0,
            $cellSize * Board::SQUARES_IN_ROW,
            $cellSize * Board::SQUARES_IN_ROW + $topPaddingOfCell,
            $cellSize * Board::SQUARES_IN_ROW,
            $cellSize * Board::SQUARES_IN_ROW + $topPaddingOfCell
        );

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

        for ($x = 1; $x <= Board::SQUARES_IN_ROW; $x++) {
            for ($y = 1; $y <= Board::SQUARES_IN_ROW; $y++) {
                if (!$boardHasTexture) {
                    list($red, $green, $blue) = $this->colorHexToRedGreenBlue(($x + $y) % 2 ? $darkColor : $lightColor);
                    $this->drawCellRectangle($x, $y, $cellSize, $red, $green, $blue, $topPaddingOfCell);
                }

                if (!empty($highlightedSquares) /* && in array this cell */) {
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

        $this->image = $this->image->insert($cell, 'top-left', ($x - 1) * $cellSize, ($y - 1) * $cellSize + $topPaddingOfCell);
    }

    protected function colorHexToRedGreenBlue($hex)
    {
        list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");

        return [$red, $green, $blue];
    }
}
