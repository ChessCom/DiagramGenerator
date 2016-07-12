<?php

namespace DiagramGenerator\Diagram;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;
use ImagickDraw;
use Imagick;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Gd\Decoder;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;
use RuntimeException;

/**
 * Class responsible for drawing the board.
 */
class Board
{
    const HIGHLIGHTED_OPACITY = .5;
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
        // if we have a texture set, we don't draw cells
        if ($this->config->getTexture()) {
            return $this;
        }

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
        $this->drawCellStandard($x, $y, $colorIndex);
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
     * Returns light cell color.
     *
     * @return \ImagickPixel
     */
    protected function getLightCellColor()
    {
        return new \ImagickPixel($this->config->getLight());
    }

    /**
     * Returns dark cell color.
     *
     * @return \ImagickPixel
     */
    protected function getDarkCellColor()
    {
        return new \ImagickPixel($this->config->getDark());
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
        $fillColor = $colorIndex ? $this->config->getDark() : $this->config->getLight();
        list($r, $g, $b) = sscanf($fillColor, "#%02x%02x%02x");

        $cell = imagecreatetruecolor($this->getCellSize(), $this->getCellSize());
        $fillColor = imagecolorallocate($cell, $r, $g, $b);
        imagefill($cell, 0, 0, $fillColor);

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

        list($r, $g, $b) = sscanf($this->config->getHighlightSquaresColor(), "#%02x%02x%02x");

        $cell = imagecreatetruecolor($this->getCellSize(), $this->getCellSize());
        $fillColor = imagecolorallocate($cell, $r, $g, $b);
        imagefill($cell, 0, 0, $fillColor);

        // test highlighting
        $this->image = $this->image->insert(
            $cell,
            self::DRAW_POSITION,
            ($x - 1) * $this->getCellSize(),
            ($y - 1) * $this->getCellSize() + $this->paddingTop
        );
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
        $this->drawBoard()->drawCells()->drawFigures();

//        TODO [lackovic10]: move this to the Board class
//        $this->image->addImage($this->board->getImage());
//        if ($this->config->getCoordinates()) {
//            // Add border to diagram
//            $this->drawBorder();
//
//            // Add vertical coordinates
//            foreach (Coordinate::getVerticalCoordinates() as $index => $x) {
//                $coordinate = $this->createCoordinate(
//                    $this->getBorderThickness(), $this->board->getCellSize(), abs($x - 9)
//                );
//
//                $coordinateY = $this->getBorderThickness() + $this->board->getPaddingTop() +
//                    $this->board->getCellSize() * $index;
//
//                $this->image->compositeImage(
//                    $coordinate->getImage(),
//                    \Imagick::COMPOSITE_DEFAULT,
//                    0,
//                    $coordinateY
//                );
//            }
//
//            // Add horizontal coordinates
//            foreach (Coordinate::getHorizontalCoordinates() as $index => $y) {
//                $coordinate = $this->createCoordinate($this->board->getCellSize(), $this->getBorderThickness(), $y);
//                $this->image->compositeImage(
//                    $coordinate->getImage(),
//                    \Imagick::COMPOSITE_DEFAULT,
//                    $this->getBorderThickness() + $this->board->getCellSize() * $index,
//                    $this->getBorderThickness() + $this->board->getImage()->getImageHeight()
//                );
//            }
//        }
//
//        if ($this->getCaptionText()) {
//            // Add border to diagram
//            $this->drawBorder();
//
//            // Create and add caption to image
//            $caption = $this->createCaption();
//
//            // Additional padding if coordinates were added
//            if ($this->config->getCoordinates()) {
//                $caption->drawBorder($this->getBackgroundColor(), 0, $caption->getImage()->getImageHeight() / 2);
//            }
//
//            $this->image->addImage($caption->getImage());
//
//            // Add bottom padding
//            if (!$this->config->getCoordinates()) {
//                $this->image->newImage(
//                    $this->image->getImageWidth(),
//                    $this->getBorderThickness(),
//                    $this->getBackgroundColor()
//                );
//            }
//            $this->image->resetIterator();
//            $this->image = $this->image->appendImages(true);
//        }
//
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
     * @param int    $width
     * @param int    $height
     * @param string $text
     *
     * @return Coordinate
     */
    protected function createCoordinate($width, $height, $text)
    {
        $coordinate = new Coordinate($this->config);
        $draw = $coordinate->getDraw();

        // Create image
        $coordinate->getImage()->newImage($width, $height, $this->getBackgroundColor());

        // Add text
        $coordinate->getImage()->annotateImage($draw, 0, 0, 0, $text);
        $coordinate->getImage()->setImageFormat($this->board->getImageFormat());

        return $coordinate;
    }

    /**
     * Creates caption.
     *
     * @return Caption
     */
    protected function createCaption()
    {
        $caption = new Caption($this->config);
        $draw = $caption->getDraw();
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
     * Returns caption text.
     *
     * @return string
     */
    protected function getCaptionText()
    {
        return $this->config->getCaption();
    }

    /**
     * Returns font path by font filename.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function getFont($filename)
    {
        return realpath(sprintf('%s/Resources/fonts/%s', __DIR__, $filename));
    }

    /**
     * @return int
     */
    protected function getBorderThickness()
    {
        return $this->board->getCellSize() / 2;
    }
}
