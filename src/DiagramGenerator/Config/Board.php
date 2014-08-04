<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class that contains the board texture config
 *
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Board
{
    /**
     * Board texture filename
     *
     * @Type("string")
     * @var string
     */
    protected $textureFilename;

    /**
     * Board background color
     *
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Background color should be in hex format")
     * @var string
     */
    protected $backgroundColor = 'FFFFFF';

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Border color should be in hex format")
     * @var string
     */
    protected $borderColor = '777777';

    /**
     * Caption text
     *
     * @Type("string")
     * @var string $caption
     */
    protected $caption;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Caption color should be in hex format")
     * @var string
     */
    protected $captionColor = '000000';

    /**
     * Show/hide coordinates
     *
     * @Type("boolean")
     * @var bool $coordinates
     */
    protected $coordinates;

    /**
     * Light cell color
     *
     * @Type("string")
     * @var string $lightCellColor
     */
    protected $lightCellColor;

    /**
     * Dark cell color
     *
     * @Type("string")
     * @var string $darkCellColor
     */
    protected $darkCellColor;

    /**
     * Flip board
     *
     * @Type("boolean")
     * @var bool $flip
     */
    protected $flip;

    /**
     * Highlighted squares list
     *
     * @Type("array<string>")
     * @var array $highlightSquares
     */
    protected $highlightSquares;

    /**
     * Highlighted squares color
     *
     * @Type("String")
     * @var string $highlightSquaresColor
     */
    protected $highlightSquaresColor;



    public function getTextureFilename()
    {
        return $this->textureFilename;
    }

    public function setTextureFilename($textureFilename)
    {
        $this->textureFilename = $textureFilename;

        return $this;
    }

    public function getBackgroundColor()
    {
        return sprintf("#%s", $this->backgroundColor);
    }

    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getBorderColor()
    {
        return sprintf("#%s", $this->borderColor);
    }

    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;

        return $this;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaptionColor()
    {
        return sprintf("#%s", $this->captionColor);
    }

    public function setCaptionColor($captionColor)
    {
        $this->captionColor = $captionColor;

        return $this;
    }

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function getLightCellColor()
    {
        return $this->lightCellColor;
    }

    public function setLightCellColor($lightCellColor)
    {
        $this->lightCellColor = $lightCellColor;

        return $this;
    }

    public function getDarkCellColor()
    {
        return $this->darkCellColor;
    }

    public function setDarkCellColor($darkCellColor)
    {
        $this->darkCellColor = $darkCellColor;

        return $this;
    }

    public function getFlip()
    {
        return $this->flip;
    }

    public function setFlip($flip)
    {
        $this->flip = $flip;

        return $this;
    }

    public function getHighlightSquares()
    {
        return $this->highlightSquares;
    }

    public function setHighlightSquares($highlightSquares)
    {
        $this->highlightSquares = $highlightSquares;

        return $this;
    }

    public function getHighlightSquaresColor()
    {
        return $this->highlightSquaresColor;
    }

    public function setHighlightSquaresColor($highlightSquaresColor)
    {
        $this->highlightSquaresColor = $highlightSquaresColor;

        return $this;
    }
}
