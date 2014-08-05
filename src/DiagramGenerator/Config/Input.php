<?php

namespace DiagramGenerator\Config;

use DiagramGenerator\Fen;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use DiagramGenerator\Validator\Constraints\CustomCellSize;
use DiagramGenerator\Validator\Constraints\SquareList;

/**
 * @ExclusionPolicy("none")
 */
// An instance of the ConfigInput class accepts the parameters input from the user which can be validated
class Input
{
    const DEFAULT_SIZE = 1;
    const DEFAULT_PIECE_THEME_INDEX = 4;
    const DEFAULT_BOARD_TEXTURE_INDEX = null;
    const DEFAULT_CAPTION = '';
    const DEFAULT_COORDINATES = false;
    const DEFAULT_LIGHT_CELL_COLOR = 'eeeed2';
    const DEFAULT_DARK_CELL_COLOR = '769656';
    const DEFAULT_FLIP = false;
    const DEFAULT_HIGHLIGHT_SQUARES = '';
    const DEFAULT_HIGHLIGHT_SQUARES_COLOR = 'ffcccc';

    /**
     * @Type("string")
     * @NotBlank()
     * @var string
     */
    protected $fen = Fen::DEFAULT_FEN;

    /**
     * This value can contain the size index (load data from the config file) or it can define the cell with (50px)
     *
     * @Type("string")
     * @CustomCellSize(min=0, max=3, minPx=20, maxPx=200)
     * @var integer
     */
    protected $size = self::DEFAULT_SIZE;

    /**
     * @Type("integer")
     * @Range(min=0, max=5)
     * @SerializedName("theme")
     * @var integer
     */
    // [lackovic10]: renaming "theme" to "piece theme" (keeping the old field for backwards compatibility)
    protected $themeIndex = self::DEFAULT_PIECE_THEME_INDEX;

    /**
     * @Type("integer")
     * @Range(min=0, max=5)
     * @SerializedName("piece")
     * @var integer
     */
    protected $pieceThemeIndex = self::DEFAULT_PIECE_THEME_INDEX;

    /**
     * @Type("integer")
     * @Range(min=0, max=3)
     * @SerializedName("texture")
     * @var integer
     */
    // [lackovic10]: renaming "texture" to "board texture" (keeping the old field for backwards compatibility)
    protected $textureIndex = self::DEFAULT_BOARD_TEXTURE_INDEX;

    /**
     * @Type("integer")
     * @Range(min=0, max=3)
     * @SerializedName("board")
     * @var integer
     */
    protected $boardTextureIndex = self::DEFAULT_BOARD_TEXTURE_INDEX;

    /**
     * @Type("string")
     * @Length(max=30)
     * @var string
     */
    protected $caption = self::DEFAULT_CAPTION;

    /**
     * @Type("boolean")
     * @SerializedName("coordinates")
     * @var boolean
     */
    protected $coordinates = self::DEFAULT_COORDINATES;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Light cell color should be in hex format")
     * @SerializedName("light")
     * @var string
     */
    protected $lightCellColor = self::DEFAULT_LIGHT_CELL_COLOR;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Dark cell color should be in hex format")
     * @SerializedName("dark")
     * @var string
     */
    protected $darkCellColor = self::DEFAULT_DARK_CELL_COLOR;

    /**
     * @Type("boolean")
     * @var boolean
     */
    protected $flip = self::DEFAULT_FLIP;

    /**
     * @Type("string")
     * @SquareList()
     * @Length(max=128)
     * @var string
     */
    protected $highlightSquares = self::DEFAULT_HIGHLIGHT_SQUARES;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Highlight squares color should be in hex format")
     * @var string
     */
    protected $highlightSquaresColor = self::DEFAULT_HIGHLIGHT_SQUARES_COLOR;

    public function getFen()
    {
        return Fen::sanitizeFenString($this->fen);
    }

    public function setFen($fen)
    {
        $this->fen = Fen::sanitizeFenString($fen);

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Getting the piece theme input parameter value - determine the value being set by the user: piece or theme
     *
     * @return int
     */
    public function getPieceThemeIndex()
    {
        if ($this->pieceThemeIndex != self::DEFAULT_PIECE_THEME_INDEX) {
            return $this->pieceThemeIndex;
        }

        return $this->themeIndex;
    }

    public function setPieceTheme($pieceTheme)
    {
        $this->pieceTheme = $pieceTheme;

        return $this;
    }

    public function setTexture($texture)
    {
        $this->texture = $texture;

        return $this;
    }

    /**
     * Getting the board texture input parameter value - determine the value being set by the user: texture or board
     *
     * @return int
     */
    public function getBoardTextureIndex()
    {
        if ($this->boardTextureIndex != self::DEFAULT_BOARD_TEXTURE_INDEX) {
            return $this->boardTextureIndex;
        }

        return $this->textureIndex;
    }

    public function setBoardTexture($boardTexture)
    {
        $this->boardTexture = $boardTexture;

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

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setCoordinates($coordinates)
    {
        $this->coordinates = (bool) $coordinates;

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
        $this->flip = (bool) $flip;

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
