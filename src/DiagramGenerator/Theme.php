<?php

namespace DiagramGenerator;

class Theme
{
    /**
     * font filename
     * @var string
     */
    protected $font;

    /**
     * @var array
     */
    protected $size;

    /**
     * @var string
     */
    protected $figures;

    /**
     * @var array
     */
    protected $left;

    /**
     * @var array
     */
    protected $base;

    /**
     * Gets the font filename.
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Sets the font filename.
     *
     * @param string $font the font
     *
     * @return self
     */
    public function setFont($font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Gets the value of size.
     *
     * @return array
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the value of size.
     *
     * @param array $size the size
     *
     * @return self
     */
    public function setSize(array $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Gets the value of figures.
     *
     * @return string
     */
    public function getFigures()
    {
        return $this->figures;
    }

    /**
     * Sets the value of figures.
     *
     * @param string $figures the figures
     *
     * @return self
     */
    public function setFigures($figures)
    {
        $this->figures = $figures;

        return $this;
    }

    /**
     * Gets the value of left.
     *
     * @return array
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Sets the value of left.
     *
     * @param array $left the left
     *
     * @return self
     */
    public function setLeft(array $left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * Gets the value of base.
     *
     * @return array
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Sets the value of base.
     *
     * @param array $base the base
     *
     * @return self
     */
    public function setBase(array $base)
    {
        $this->base = $base;

        return $this;
    }
}