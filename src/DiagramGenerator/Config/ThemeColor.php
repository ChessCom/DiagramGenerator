<?php

namespace DiagramGenerator\Config;

use JMS\Serializer\Annotation\Type;

class ThemeColor
{
    /**
     * @Type("string")
     * @var array
     */
    protected $background = '#FFFFFF';

    /**
     * @Type("string")
     * @var array
     */
    protected $caption = '#000000';

    /**
     * @Type("string")
     * @var array
     */
    protected $border = '#777777';

    /**
     * Gets the value of background.
     *
     * @return array
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * Sets the value of background.
     *
     * @param array $background the background
     *
     * @return self
     */
    public function setBackground(array $background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return array
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param array $caption the caption
     *
     * @return self
     */
    public function setCaption(array $caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Gets the value of border.
     *
     * @return array
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Sets the value of border.
     *
     * @param array $border the border
     *
     * @return self
     */
    public function setBorder(array $border)
    {
        $this->border = $border;

        return $this;
    }
}
