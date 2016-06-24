<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;
use InvalidArgumentException;

/**
 * Class to keep texture config
 */
class Texture
{
    const IMAGE_FORMAT_PNG = 'png';
    const IMAGE_FORMAT_JPG = 'jpg';

    /** @var string $name */
    protected $name;

    /** @var string $imageUrlFolderName */
    protected $imageUrlFolderName;

    /** @var string $imageFormat */
    protected $imageFormat;

    /** @var string $highlightSquaresColor The default highlight squares color for the board texture */
    protected $highlightSquaresColor;

    /**
     * @param string      $name
     * @param string      $imageUrlFolderName
     * @param string      $imageFormat
     * @param string|null $highlightSquaresColor
     */
    public function __construct($name, $imageUrlFolderName, $imageFormat, $highlightSquaresColor = null)
    {
        if (!in_array($imageFormat, array(self::IMAGE_FORMAT_PNG, self::IMAGE_FORMAT_JPG))) {
            throw new InvalidArgumentException(sprintf('Invalid image format: %s', $imageFormat));
        }

        $this->name = $name;
        $this->imageUrlFolderName = $imageUrlFolderName;
        $this->imageFormat = $imageFormat;
        $this->highlightSquaresColor = $highlightSquaresColor;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImageUrlFolderName()
    {
        return $this->imageUrlFolderName;
    }

    /**
     * @return string
     */
    public function getImageFormat()
    {
        return $this->imageFormat;
    }

    /**
     * @return string
     */
    public function getHighlightSquaresColor()
    {
        return $this->highlightSquaresColor;
    }

    /**
     * @param Texture $texture
     *
     * @return boolean
     */
    public function is(Texture $texture)
    {
        return $this->name === $texture->getName() && $this->imageUrlFolderName === $texture->getImageUrlFolderName();
    }
}
