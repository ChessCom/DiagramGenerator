<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class to keep theme color config.
 */
class ThemeColor
{
    /**
     * @var string
     */
    #[Type('string')]
    #[Regex(pattern: '/^[a-fA-F0-9]{6}$/', message: 'Background color should be in hex format')]
    protected $background = 'FFFFFF';

    /**
     * @var string
     */
    #[Type('string')]
    #[Regex(pattern: '/^[a-fA-F0-9]{6}$/', message: 'Caption color should be in hex format')]
    protected $caption = '000000';

    /**
     * @var string
     */
    #[Type('string')]
    #[Regex(pattern: '/^[a-fA-F0-9]{6}$/', message: 'Border color should be in hex format')]
    protected $border = '777777';

    /**
     * Gets the value of background.
     *
     * @return string
     */
    public function getBackground()
    {
        return sprintf('#%s', $this->background);
    }

    /**
     * Sets the value of background.
     *
     * @param string $background the background
     *
     * @return self
     */
    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return string
     */
    public function getCaption()
    {
        return sprintf('#%s', $this->caption);
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
     * Gets the value of border.
     *
     * @return string
     */
    public function getBorder()
    {
        return sprintf('#%s', $this->border);
    }

    /**
     * Sets the value of border.
     *
     * @param string $border the border
     *
     * @return self
     */
    public function setBorder($border)
    {
        $this->border = $border;

        return $this;
    }
}
