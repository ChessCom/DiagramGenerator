<?php

namespace DiagramGenerator\Theme;

class ThemeColor
{
    /**
     * @var array
     */
    protected $background = '#FFFFFF';

    /**
     * @var array
     */
    protected $coordinates = '#777777';

    /**
     * @var array
     */
    protected $caption = '#000000';

    /**
     * @var array
     */
    protected $outline = '#333333';

    /**
     * @var array
     */
    protected $frame = '#777777';

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
     * Gets the value of coordinates.
     *
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Sets the value of coordinates.
     *
     * @param array $coordinates the coordinates
     *
     * @return self
     */
    public function setCoordinates(array $coordinates)
    {
        $this->coordinates = $coordinates;

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
     * Gets the value of outline.
     *
     * @return array
     */
    public function getOutline()
    {
        return $this->outline;
    }

    /**
     * Sets the value of outline.
     *
     * @param array $outline the outline
     *
     * @return self
     */
    public function setOutline(array $outline)
    {
        $this->outline = $outline;

        return $this;
    }

    /**
     * Gets the value of frame.
     *
     * @return array
     */
    public function getFrame()
    {
        return $this->frame;
    }

    /**
     * Sets the value of frame.
     *
     * @param array $frame the frame
     *
     * @return self
     */
    public function setFrame(array $frame)
    {
        $this->frame = $frame;

        return $this;
    }
}
