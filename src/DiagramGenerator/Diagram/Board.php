<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;
use DiagramGenerator\Generator;
use ImagickDraw;
use Imagick;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Gd\Decoder;
use Intervention\Image\Gd\Font;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;
use RuntimeException;

/**
 * Class responsible for drawing the board.
 */
class Board
{
    const HIGHLIGHTED_OPACITY = 63; // alpha, semi transparent
    const DRAW_POSITION = 'top-left';

    protected $squares = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');

    protected $flippedSquares = array('h', 'g', 'f', 'e', 'd', 'c', 'b', 'a');

    /** @var Image */
    protected $image;

    /** @var Config */
    protected $config;

    /** @var Fen */
    protected $fen;

    /** @var int */
    protected $paddingTop = 0;

    /** @var */
    protected $rootCacheDir;

    /** @var */
    protected $cacheDirName = 'diagram_generator';

    /** @var */
    protected $cacheDir;

    /** @var string */
    protected $boardTextureUrl;

    /**
     * @var string
     */
    protected $pieceThemeUrl;

    /**
     * Cached Image pieces.
     *
     * @var array
     */
    protected $pieces = array();

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

        $this->cacheDir = $this->rootCacheDir.'/'.$this->cacheDirName;

        @mkdir($this->rootCacheDir.'/'.$this->cacheDirName, 0777);

        $this->fen = Fen::createFromString($this->config->getFen());

        if ($this->config->getFlip()) {
            $this->fen->flip();
        }

        $this->drawBoardImage();
    }

    /**
     * Gets the value of image.
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getImageStream()
    {
        ob_start();

        imagepng($this->getImage()->getCore(), null);
        $stream = ob_get_contents();

        ob_end_clean();

        return $stream;
    }

    /**
     * Get the board's image format. The whole dynboard image format is dependent on the board image format.
     *
     * @return string
     */
    public function getImageFormat()
    {
        if ($this->config->getTexture()) {
            return $this->config->getTexture()->getImageFormat();
        }

        return Texture::IMAGE_FORMAT_PNG;
    }

    /**
     * Draws board itself.
     *
     * @return self
     */
    protected function drawBoard()
    {
        $this->paddingTop = $this->getMaxPieceHeight() - $this->getCellSize();

        $image = new Decoder();
        $this->image = $image->initFromGdResource($this->getBaseBoard());

        return $this;
    }

    /**
     * Draws cells on the board.
     *
     * @return self
     */
    protected function drawCells()
    {
        for ($x = 1; $x <= count($this->squares); $x++) {
            for ($y = 1; $y <= count($this->squares); $y++) {
                $this->drawCell($x, $y, ($x + $y) % 2);
            }
        }

        return $this;
    }

    /**
     * Draw a single cell.
     *
     * @param int  $x
     * @param int  $y
     * @param bool $colorIndex
     */
    protected function drawCell($x, $y, $colorIndex)
    {
        // if we have a texture set, we don't draw cells
        if (!$this->config->getTexture()) {
            $this->drawCellStandard($x, $y, $colorIndex);
        }

        $this->drawCellHighlighted($x, $y);
    }

    /**
     * Add figures to the board.
     *
     * @return self
     */
    protected function drawFigures()
    {
        foreach ($this->fen->getPieces() as $piece) {
            $pieceImage = $this->getPieceImage($piece);

            $this->image = $this->image->insert(
                $pieceImage,
                self::DRAW_POSITION,
                $this->getCellSize() * $piece->getColumn(),
                $this->getCellSize() * ($piece->getRow() + 1) - $pieceImage->getHeight() + $this->paddingTop
            );
        }

        return $this;
    }

    /**
     * Shortcut to get cell size.
     *
     * @return int
     */
    protected function getCellSize()
    {
        return $this->config->getSize()->getCell();
    }

    protected function getPaddingTop()
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
     * Returns piece image path.
     *
     * @param Piece $piece
     *
     * @return Image
     */
    protected function getPieceImage(Piece $piece)
    {
        $key = $piece->getColor().'.'.$piece->getKey().'.'.$piece->getColumn().'.'.$piece->getRow();

        if (!isset($this->pieces[$key])) {
            $pieceThemeName = $this->config->getTheme()->getName();
            $cellSize = $this->getCellSize();
            $piece = substr($piece->getColor(), 0, 1).$piece->getKey();
            $pieceCachedPath = $this->getCachedPieceFilePath($pieceThemeName, $cellSize, $piece);

            try {
                $image = ImageManagerStatic::make($pieceCachedPath);
            } catch (\Exception $exception) {
                @mkdir($this->cacheDir.'/'.$pieceThemeName.'/'.$cellSize, 0777, true);

                $pieceThemeUrl = str_replace('__PIECE_THEME__', $pieceThemeName, $this->pieceThemeUrl);
                $pieceThemeUrl = str_replace('__SIZE__', $cellSize, $pieceThemeUrl);
                $pieceThemeUrl = str_replace('__PIECE__', $piece, $pieceThemeUrl);
                $pieceThemeUrl .= '.'.Texture::IMAGE_FORMAT_PNG;

                $this->cacheImage($pieceThemeUrl, $pieceCachedPath);

                $image = ImageManagerStatic::make($pieceCachedPath);
            }

            $this->pieces[$key] = $image;
        }

        return $this->pieces[$key];
    }

    /**
     * Returns board background image path.
     *
     * @return Image
     *
     * @throws NotReadableException
     */
    protected function getBackgroundTexture()
    {
        $boardCachedPath = $this->getCachedTextureFilePath();

        try {
            return ImageManagerStatic::make($boardCachedPath);
        } catch (NotReadableException $exception) {
            @mkdir($this->cacheDir.'/board/'.$this->config->getTexture()->getImageUrlFolderName(), 0777, true);

            $boardTextureUrl = str_replace(
                '__BOARD_TEXTURE__', $this->config->getTexture()->getImageUrlFolderName(), $this->boardTextureUrl
            );
            $boardTextureUrl = str_replace('__SIZE__', $this->getCellSize(), $boardTextureUrl);
            $boardTextureUrl .= '.'.$this->config->getTexture()->getImageFormat();

            $this->cacheImage($boardTextureUrl, $boardCachedPath);

            return ImageManagerStatic::make($boardCachedPath);
        }
    }

    /**
     * Return the square for the coordinates passed (starting from 0).
     *
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    protected function getSquare($x, $y)
    {
        return $this->squares[$x - 1].(count($this->squares) - $y + 1);
    }

    /**
     * Get the largest piece height.
     *
     * @return int
     */
    protected function getMaxPieceHeight()
    {
        $maxHeight = $this->getCellSize();
        foreach ($this->fen->getPieces() as $piece) {
            $pieceImage = $this->getPieceImage($piece);

            if ($pieceImage->getHeight() > $maxHeight) {
                $maxHeight = $pieceImage->getHeight();
            }

            unset($pieceImage);
        }

        return $maxHeight;
    }

    /**
     * Cache an image from a remote url to a local cache file.
     *
     * @param string $remoteImageUrl
     * @param string $cachedFilePath
     */
    protected function cacheImage($remoteImageUrl, $cachedFilePath)
    {
        $cachedFilePathTmp = $cachedFilePath.uniqid('', true);
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
     * Get the highlighted squares for the board (considering the flip parameter).
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
     * Return the flipped representation of a square.
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
                return $this->flippedSquares[$index].(count($this->squares) - $y + 1);
            }
        }
    }

    /**
     * Draw a non-highlighted square.
     *
     * @param int $x
     * @param int $y
     * @param int $colorIndex
     */
    protected function drawCellStandard($x, $y, $colorIndex)
    {
        list($red, $green, $blue) = $this->colorHexToRedGreenBlue($colorIndex ? $this->config->getDark() : $this->config->getLight());

        $cell = $this->drawCellRectangle($red, $green, $blue);

        $this->image = $this->image->insert(
            $cell,
            self::DRAW_POSITION,
            ($x - 1) * $this->getCellSize(),
            ($y - 1) * $this->getCellSize() + $this->paddingTop
        );
    }

    /**
     * Draw a highlighted cell.
     *
     * @param int $x
     * @param int $y
     */
    protected function drawCellHighlighted($x, $y)
    {
        if (!is_array($this->getHighlightSquares($this->config)) ||
            !in_array($this->getSquare($x, $y), $this->getHighlightSquares($this->config))) {
            return;
        }

        list($red, $green, $blue) = $this->colorHexToRedGreenBlue($this->config->getHighlightSquaresColor());
        $cell = $this->drawCellRectangle($red, $green, $blue, self::HIGHLIGHTED_OPACITY);

        // test highlighting
        $this->image = $this->image->insert(
            $cell,
            self::DRAW_POSITION,
            ($x - 1) * $this->getCellSize(),
            ($y - 1) * $this->getCellSize() + $this->paddingTop
        );
    }

    protected function drawCellRectangle($red, $green, $blue, $opacity = 0)
    {
        $cell = imagecreatetruecolor($this->getCellSize(), $this->getCellSize());
        $fillColor = imagecolorallocatealpha($cell, $red, $green, $blue, $opacity);
        imagefill($cell, 0, 0, $fillColor);

        return $cell;
    }

    protected function colorHexToRedGreenBlue($hex)
    {
        list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");

        return [$red, $green, $blue];
    }

    /**
     * Cached texture file path.
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
     * Cached piece file path.
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

    protected function getBaseBoard()
    {
        $board = imagecreatetruecolor(
            $this->getCellSize() * count($this->squares),
            $this->getCellSize() * count($this->squares) + $this->paddingTop
        );

        $background = $this->getBackgroundTexture();

        imagecopyresampled(
            $board, $background->getCore(),
            0, 0, 0, 0,
            $this->getCellSize() * count($this->squares), $this->getCellSize() * count($this->squares) + $this->paddingTop,
            $this->getCellSize() * count($this->squares), $this->getCellSize() * count($this->squares) + $this->paddingTop
        );

        return $board;
    }

    /**
     * Draw all the things!
     */
    protected function drawBoardImage()
    {
        // the standard stuff
        $this->drawBoard()->drawCells()->drawFigures();

        if ($this->config->getCoordinates()) {
            $this->drawCoordinates();
        }
//
        if ($this->getCaptionText()) {
            $this->drawCaption();
        }

//        $this->image->setImageFormat($this->board->getImageFormat());
//        if ($this->image->getImageFormat() === Texture::IMAGE_FORMAT_JPG) {
//            $compressionQualityJpg = is_null($this->config->getCompressionQualityJpg()) ?
//                self::COMPRESSION_QUALITY_DEFAULT_JPG : $this->config->getCompressionQualityJpg();
//
//            $this->image->setImageCompressionQuality($compressionQualityJpg);
//        }
    }

    /**
     * Draws the image border.
     */
    protected function drawBorder()
    {
        $this->image->resizeCanvas(
            $this->getImage()->width() + $this->getBorderThickness(),
            $this->getImage()->height() + $this->getBorderThickness(),
            'top-right',
            false,
            $this->getBackgroundColor()
        );
    }

    protected function drawCaption()
    {
        $preResizeImageHeight = $this->image->height();

        $this->image->resizeCanvas(
            0,
            $this->getImage()->height() + $this->getBorderThickness() * 2,
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

    protected function drawCoordinates()
    {
        // coordinates need a border
        $this->drawBorder();

        $fontSetup = function (Font $font) {
            $font->file(sprintf('%s/fonts/%s', Generator::getResourcesDir(), 'tahoma.ttf'));
            $font->size($this->config->getSize()->getCoordinates());
            $font->align('center');
            $font->valign('middle');
        };

        // Add vertical coordinates
        foreach (Coordinate::getVerticalCoordinates() as $index => $x) {
            $this->image->text(
                abs($x - 9),
                $this->getBorderThickness() / 2, // cell size is split to both sides of the board. we need to split that half into 2, to get the center
                $this->getBorderThickness() + $this->getPaddingTop() + $this->getCellSize() * $index,
                $fontSetup
            );
        }

        // Add horizontal coordinates
        foreach (Coordinate::getHorizontalCoordinates() as $index => $y) {
            $this->image->text(
                $y,
                $this->getCellSize() + $this->getCellSize() * $index,
                $this->getImage()->height() - $this->getBorderThickness() / 2,
                $fontSetup
            );
        }
    }

//    /**
//     * @param int    $width
//     * @param int    $height
//     * @param string $text
//     *
//     * @return Coordinate
//     */
//    protected function createCoordinate($width, $height, $text)
//    {
//        $coordinate = new Coordinate($this->config);
//        $draw = $coordinate->getDraw();
//
//        // Create image
//        $coordinate->getImage()->newImage($width, $height, $this->getBackgroundColor());
//
//        // Add text
//        $coordinate->getImage()->annotateImage($draw, 0, 0, 0, $text);
//        $coordinate->getImage()->setImageFormat($this->board->getImageFormat());
//
//        return $coordinate;
//    }
//
//    /**
//     * Creates caption.
//     *
//     * @return Caption
//     */
//    protected function createCaption()
//    {
//        $caption = new Caption($this->config);
//        $draw = $caption->getDraw();
//        $metrics = $caption->getMetrics($draw);
//
//        // Create image
//        $caption->getImage()->newImage(
//            $this->image->getImageWidth(),
//            $metrics['textHeight'],
//            $this->getBackgroundColor()
//        );
//
//        // Add text
//        $caption->getImage()->annotateImage($draw, 0, 0, 0, $this->getCaptionText());
//        $caption->getImage()->setImageFormat('png');
//
//        return $caption;
//    }
//
//    /**
//     * Returns caption text.
//     *
//     * @return string
//     */
    protected function getCaptionText()
    {
        return $this->config->getCaption();
    }

    /**
     * @return int
     */
    protected function getBorderThickness()
    {
        return $this->getCellSize() / 2;
    }
}
