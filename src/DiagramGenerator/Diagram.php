<?php

namespace DiagramGenerator;

use DiagramGenerator\GeneratorConfig;
// use DiagramGenerator\Size;
// use DiagramGenerator\Theme;
use DiagramGenerator\Dimensions;
use DiagramGenerator\Theme\ThemeColor;

/**
 * Class which represents diagram image
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Diagram
{
    /**
     * @var \DiagramGenerator\GeneratorConfig
     */
    // protected $config;

    /**
     * @var \DiagramGenerator\Size
     */
    // protected $size;

    /**
     * @var \DiagramGenerator\Theme
     */
    // protected $theme;

    /**
     * @var \Imagick
     */
    protected $diagram;

    /**
     * @var \Imagick
     */
    protected $board;

    /**
     * @var \Imagick
     */
    protected $caption;

    /**
     * @var \DiagramGenerator\Dimensions
     */
    // protected $dimensions;

    /**
     * @var \DiagramGenerator\Dimensions
     */
    // protected $boardDimensions;

    /**
     * @var \DiagramGenerator\Dimensions
     */
    // protected $frameDimensions;

    public function __construct()
    {
        // $this->dimensions      = $dimensions;
        // $this->boardDimensions = $boardDimensions;
        // $this->frameDimensions = $frameDimensions;
        $this->diagram = new \Imagick(); //imagecreatetruecolor($dimensions->getWidth(), $dimensions->getHeight());
    }

    public function draw(GeneratorConfig $config, Size $size, Theme $theme)
    {
        $this->config = $config;
        $this->size = $size;
        $this->theme = $theme;

        $this->createDimensions();
        $this->createBoardDimensions();
        $this->createFrameDimensions();

        $this->drawBackgroud();
        $this->drawCells($size);
        $this->drawCaption();
    }

    protected function drawBackgroud()
    {
        $boardLength = $this->size->getCell() * 8;
        $borderThick = $this->size->getFrameThick();
        $board = new \Imagick();
        $board->newImage(
            $boardLength,
            $boardLength,
            new \ImagickPixel($this->theme->getColor()->getFrame())
        );
        $board->borderImage(new \ImagickPixel($this->theme->getColor()->getOutline()), $borderThick, $borderThick);
        $board->setImageFormat('png');

        $diagramWidth = $diagramHeight = $boardLength + $borderThick + $this->size->getCell();
        $diagram = new \Imagick();
        $diagram->newImage(
            $diagramWidth,
            $diagramHeight,
            new \ImagickPixel($this->theme->getColor()->getBackground())
        );
        $diagram->compositeImage($board, \Imagick::COMPOSITE_OVER, $this->size->getCell() / 2, $this->size->getCell() / 2);
        $diagram->setImageFormat('png');

        header('Content-Type: image/png');
        echo $diagram;
        exit;
    }

    /**
     * Fills image with backgrounds
     * @param  Theme  $theme
     * @return null
     */
    // protected function drawBackgroud(Theme $theme)
    // {
    //     imagefilledrectangle(
    //         $this->image,
    //         0,
    //         0,
    //         $this->dimensions->getWidth(),
    //         $this->dimensions->getHeight(),
    //         ThemeColor::allocateColor($image, $theme->getColor()->getBackground())
    //     );

    //     $boardDimensions = $this->getBoardDimensions();
    //     $outlineHeight = $outlineWidth  = ($this->dimensions->getWidth() - $this->boardDimensions->getWidth()) / 2;
    //     imagefilledrectangle(
    //         $this->image,
    //         $outlineWidth,
    //         $outlineHeight,
    //         $outlineWidth + $this->boardDimensions->getWidth(),
    //         $outlineHeight + $this->boardDimensions->getHeight(),
    //         ThemeColor::allocateColor($image, $theme->getColor()->getOutline())
    //     );

    //     $frameHeight = $frameWidth = ($this->dimensions->getWidth() - $this->frameDimensions->getWidth()) / 2;
    //     imagefilledrectangle(
    //         $this->image,
    //         $frameWidth,
    //         $frameHeight,
    //         $frameWidth + $this->frameDimensions->getWidth(),
    //         $frameWidth + $this->frameDimensions->getHeight(),
    //         ThemeColor::allocateColor($image, $theme->getColor()->getFrame())
    //     );
    // }

    /**
     * Fills board with cells
     * @param  Size            $size
     * @param  GeneratorConfig $config
     * @return null
     */
    protected function drawCells(Size $size, GeneratorConfig $config)
    {
        $startX = $startY = ($this->dimensions->getWidth() - $this->frameDimensions->getWidth()) / 2 + $size->getFrameThick();
        $colors = array($config->getLight(), $config->getDark());

        for ($x = 1; $x <= 8; $x++) {
            for ($y = 1; $y <= 8; $y++) {
                $colorIndex = ($x + $y) % 2;
                imagefilledrectangle(
                    $this->image,
                    $startX + ($x - 1) * $size->getCell(),
                    $startY + ($y - 1) * $size->getCell(),
                    $startX + $x * $size->getCell(),
                    $startY + $y * $size->getCell(),
                    ThemeColor::allocateColor($this->image, $colors[$colorIndex])
                );
            }
        }
    }

    /**
     * Adds caption to image
     * @param  GeneratorConfig $config
     * @param  Size            $size
     * @param  Theme           $theme
     * @return null
     */
    protected function createCaption(GeneratorConfig $config, Size $size, Theme $theme)
    {
        $textSize = $size->getCaption()->getSize();
        $textBox  = imagettfbbox($textSize, 0, $this->getFont($theme->getFont()), $config->getCaption());
        $pointX   = $this->dimensions['width'] - ($textBox[2] - $textBox[0]);
        $pointY   = $dimensions['height'] -
        imagettftext($image, $textSize, 0, $x, $caption_base, $caption_color, $CAPTION_FONT, $caption);


        header('Content-Type: image/png');
        imagepng($image);
        exit;

    }

    /**
     * Method to create diagram dimensions
     * @return null
     */
    protected function createDimensions()
    {
        if (!$this->config->getCaption()) {
            return $this->getBoardDimensions();
        }

        $this->dimensions = new Dimensions($this->size->getWidth(), $this->size->getHeight());
    }

    /**
     * Method to create chess board dimensions
     * @return null
     */
    protected function createBoardDimensions()
    {
        $borderThickness = $this->size->getFrameThick() + $this->size->getOutlineThick();
        $width = $height = $this->size->getCell() * 8 + $borderThickness * 2;
        $this->boardDimensions = new Dimensions($width, $height);
    }

    /**
     * Method to create frame dimensions
     * @return null
     */
    protected function createFrameDimensions()
    {
        $width = $height = $this->size->getCell() * 8 + $this->size->getFrameThick() * 2;
        $this->frameDimensions = new Dimensions($width, $height);
    }

    protected function getFont($filename)
    {
        return realpath(sprintf("%s/Resources/fonts/%s", __DIR__, $filename));
    }

    /**
     * Gets the value of diagram.
     *
     * @return \Imagick
     */
    public function getDiagram()
    {
        return $this->diagram;
    }

    /**
     * Sets the value of diagram.
     *
     * @param \Imagick $diagram the diagram
     *
     * @return self
     */
    public function setDiagram(\Imagick $diagram)
    {
        $this->diagram = $diagram;

        return $this;
    }

    /**
     * Gets the value of board.
     *
     * @return \Imagick
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Sets the value of board.
     *
     * @param \Imagick $board the board
     *
     * @return self
     */
    public function setBoard(\Imagick $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return \Imagick
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param \Imagick $caption the caption
     *
     * @return self
     */
    public function setCaption(\Imagick $caption)
    {
        $this->caption = $caption;

        return $this;
    }
}
