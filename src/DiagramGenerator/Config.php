<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Config\Theme;
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
use DiagramGenerator\Validator\Constraints\Integer;
use DiagramGenerator\Validator\Constraints\StringOrInteger;

/**
 * @ExclusionPolicy("none")
 */
// [lackovic10] Type("string")for int values of theme, piece, texture and board need to be to string and not int,
// so that they can be validated correctly. Invalid int type values are converted to 0, which are valid indexes for
// piece/theme and board/texture and cause unwanted results
class Config
{
    /**
     * @Type("string")
     * @NotBlank()
     * @var string
     */
    protected $fen;

    /**
     * @Type("string")
     * @CustomCellSize(min=0, max=3)
     * @SerializedName("size")
     * @var string
     */
    // keeping deprecated int values (0-3) for backwards compatibility
    // TODO [lackovic10]: rename to size, once the library is refactored and the size field is removed from this class
    // the same action with boardTexture and pieceTheme
    protected $sizeIndex;

    /**
     * @Exclude()
     * @var \DiagramGenerator\Config\Size
     */
    protected $size;

    /**
     * @Type("string")
     * @StringOrInteger(min=0, max=5)
     * @SerializedName("piece")
     * @var integer
     */
    // [lackovic10]: these are piece theme folder names from the image url
    // TODO: future idea - pass the whole urls to the library instead of generating the url inside the library based
    // on parameters
    protected $pieceIndex;

    /**
     * @Type("string")
     * @StringOrInteger(min=0, max=3)
     * @SerializedName("board")
     * @var integer
     */
    // [lackovic10]: these are board texture folder names from the image url
    protected $boardIndex;

    /**
     * @Exclude()
     * @var \DiagramGenerator\Config\Texture
     */
    protected $texture;

    /**
     * @Exclude()
     * @var \DiagramGenerator\Config\Theme
     */
    protected $theme;

    /**
     * @Type("string")
     * @Length(max=30)
     * @var string
     */
    protected $caption;

    /**
     * @Type("boolean")
     * @var boolean
     */
    protected $coordinates;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Light color should be in hex format")
     * @var string
     */
    protected $light;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Dark color should be in hex format")
     * @var string
     */
    protected $dark;

    /**
     * @Type("boolean")
     * @var boolean
     */
    protected $flip;

    /**
     * @Type("string")
     * @SquareList()
     * @Length(max=128)
     * @var string
     */
    protected $highlightSquares;

    /**
     * @Type("string")
     * @Regex(pattern="/^[a-fA-F0-9]{6}$/", message="Highlight squares color should be in hex format")
     * @var string
     */
    protected $highlightSquaresColor;

    /**
     * @Type("string")
     * @Regex(pattern="/^png|jpeg$/", message="Invalid image format. Please pass jpeg or png.")
     * @var string
     */
    protected $imageFormat = 'jpeg';

    /**
     * @Type("integer")
     * @Range(min=0, max=100)
     * @var int
     */
    protected $imageCompressionQuality = 90;

    /**
     * Gets the value of fen.
     *
     * @return string
     */
    public function getFen()
    {
        return Fen::sanitizeFenString($this->fen);
    }

    /**
     * Sets the value of fen.
     *
     * @param string $fen the fen
     *
     * @return self
     */
    public function setFen($fen)
    {
        $this->fen = Fen::sanitizeFenString($fen);

        return $this;
    }

    /**
     * Gets the value of sizeIndex.
     *
     * @return integer
     */
    public function getSizeIndex()
    {
        return $this->sizeIndex;
    }

    /**
     * Sets the value of sizeIndex.
     *
     * @param integer $sizeIndex the sizeIndex
     *
     * @return self
     */
    public function setSizeIndex($sizeIndex)
    {
        $this->sizeIndex = $sizeIndex;

        return $this;
    }

    /**
     * Gets the value of size.
     *
     * @return \DiagramGenerator\Config\Size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the value of size.
     *
     * @param \DiagramGenerator\Config\Size $size the size
     *
     * @return self
     */
    public function setSize(Size $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Gets the value of pieceIndex.
     *
     * @return integer
     */
    public function getPieceIndex()
    {
        return $this->pieceIndex;
    }

    /**
     * Sets the value of the piece index field. Preserving old values (0-6) for backwards compatibility
     *
     * @param string|int $piece
     *
     * @return Config
     */
    public function setPieceIndex($pieceIndex)
    {
        $this->pieceIndex = $pieceIndex;

        return $this;
    }

    /**
     * Gets the value of theme.
     *
     * @return \DiagramGenerator\Config\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Sets the value of theme.
     *
     * @param \DiagramGenerator\Config\Theme $theme the theme
     *
     * @return self
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param string $caption the caption
     *
     * @return self
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Gets the value of coordinates.
     *
     * @return boolean
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Sets the value of coordinates.
     *
     * @param boolean $coordinates the coordinates
     *
     * @return self
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = (bool) $coordinates;

        return $this;
    }

    /**
     * Gets the value of light.
     *
     * @return string
     */
    public function getLight()
    {
        return sprintf("#%s", ltrim($this->light, '#'));
    }

    /**
     * Sets the value of light.
     *
     * @param string $light the light
     *
     * @return self
     */
    public function setLight($light)
    {
        $this->light = $light;

        return $this;
    }

    /**
     * Gets the value of dark.
     *
     * @return string
     */
    public function getDark()
    {
        return sprintf("#%s", ltrim($this->dark, '#'));
    }

    /**
     * Sets the value of dark.
     *
     * @param string $dark the dark
     *
     * @return self
     */
    public function setDark($dark)
    {
        $this->dark = $dark;

        return $this;
    }

    /**
     * Gets the value of boardIndex.
     *
     * @return integer
     */
    public function getBoardIndex()
    {
        return $this->boardIndex;
    }

    /**
     * Sets the value of the $boardIndex field. Keeping old values (0-3) for backwards compatibility
     *
     * @param string|int $boardIndex
     *
     * @return Config
     */
    public function setBoardIndex($boardIndex)
    {
        $this->boardIndex = $boardIndex;

        return $this;
    }

    /**
     * Gets the value of texture.
     *
     * @return \DiagramGenerator\Config\Texture
     */
    public function getTexture()
    {
        return $this->texture;
    }

    /**
     * Sets the value of texture.
     *
     * @param \DiagramGenerator\Config\Texture $texture the texture
     *
     * @return self
     */
    public function setTexture(Texture $texture)
    {
        $this->texture = $texture;

        return $this;
    }

    /**
     * Gets the value of flip.
     *
     * @return boolean
     */
    public function getFlip()
    {
        return $this->flip;
    }

    /**
     * Sets the value of flip.
     *
     * @param boolean $flip the flip
     *
     * @return self
     */
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
        return sprintf("#%s", ltrim($this->highlightSquaresColor, '#'));
    }

    public function setHighlightSquaresColor($highlightSquaresColor)
    {
        $this->highlightSquaresColor = $highlightSquaresColor;

        return $this;
    }

    public function getImageFormat()
    {
        return $this->imageFormat;
    }

    public function setImageFormat($imageFormat)
    {
        $this->imageFormat = $imageFormat;

        return $this;
    }

    public function getImageCompressionQuality()
    {
        return $this->imageCompressionQuality;
    }

    public function setImageCompressionQuality($imageCompressionQuality)
    {
        $this->imageCompressionQuality = $imageCompressionQuality;

        return $this;
    }
}
