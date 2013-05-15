<?php

namespace DiagramGenerator\Size;

class Caption
{
    /**
     * @var integer
     */
    protected $size;

    /**
     * @var integer
     */
    protected $base;

    /**
     * @var integer
     */
    protected $left;

    /**
     * Gets the value of size.
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the value of size.
     *
     * @param integer $size the size
     *
     * @return self
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Gets the value of base.
     *
     * @return integer
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Sets the value of base.
     *
     * @param integer $base the base
     *
     * @return self
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Gets the value of left.
     *
     * @return integer
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Sets the value of left.
     *
     * @param integer $left the left
     *
     * @return self
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }
}