<?php

namespace DiagramGenerator;


use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Diagram\Caption;
use DiagramGenerator\Diagram\Coordinate;
use DiagramGenerator\Config\Texture;

/**
 * Class which represents diagram image.
 */
class Diagram
{
    const COMPRESSION_QUALITY_DEFAULT_JPG = 70;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Imagick
     */
    protected $image;

    /**
     * @var Board
     */
    protected $board;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->image = new \Imagick();
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
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Gets the value of board.
     *
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Sets the value of board.
     *
     * @param Board $board the board
     *
     * @return self
     */
    public function setBoard(Board $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Draw diagram.
     * TODO [lackovic10]: move this to the Board class
     *
     * @return self
     */
    public function draw()
    {
        if (!$this->board) {
            throw new \InvalidArgumentException('Board must be set');
        }

        $this->image->addImage($this->board->getImage());
        if ($this->config->getCoordinates()) {
            // Add border to diagram
            $this->drawBorder();

            // Add vertical coordinates
            foreach (Coordinate::getVerticalCoordinates() as $index => $x) {
                $coordinate = $this->createCoordinate(
                    $this->getBorderThickness(), $this->board->getCellSize(), abs($x - 9)
                );

                $coordinateY = $this->getBorderThickness() + $this->board->getPaddingTop() +
                    $this->board->getCellSize() * $index;

                $this->image->compositeImage(
                    $coordinate->getImage(),
                    \Imagick::COMPOSITE_DEFAULT,
                    0,
                    $coordinateY
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
            if ($this->config->getCoordinates()) {
                $caption->drawBorder($this->getBackgroundColor(), 0, $caption->getImage()->getImageHeight() / 2);
            }

            $this->image->addImage($caption->getImage());

            // Add bottom padding
            if (!$this->config->getCoordinates()) {
                $this->image->newImage(
                    $this->image->getImageWidth(),
                    $this->getBorderThickness(),
                    $this->getBackgroundColor()
                );
            }
            $this->image->resetIterator();
            $this->image = $this->image->appendImages(true);
        }

        $this->image->setImageFormat($this->board->getImageFormat());
        if ($this->image->getImageFormat() === Texture::IMAGE_FORMAT_JPG) {
            $compressionQualityJpg = is_null($this->config->getCompressionQualityJpg()) ?
                self::COMPRESSION_QUALITY_DEFAULT_JPG : $this->config->getCompressionQualityJpg();

            $this->image->setImageCompressionQuality($compressionQualityJpg);
        }

        return $this;
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
     * @return \ImagickPixel
     */
    protected function getBackgroundColor()
    {
        return new \ImagickPixel($this->config->getTheme()->getColor()->getBackground());
    }

    /**
     * @return int
     */
    protected function getBorderThickness()
    {
        return $this->board->getCellSize() / 2;
    }
}
