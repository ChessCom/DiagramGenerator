<?php

namespace DiagramGenerator\Config\Loader;

use Symfony\Component\Yaml\Parser;
use DiagramGenerator\Config\Size;

class SizeLoader
{
    /** @var \Symfony\Component\Yaml\Parser $parser */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the size object for the users input parameter
     *
     * @param string $configFilePath
     * @param int    $sizeIndex
     *
     * @return Size
     */
    public function getSize($configFilePath, $sizeIndex)
    {
        if (is_numeric($sizeIndex)) {
            $sizes = $this->loadSizes($configFilePath);

            if (!array_key_exists($sizeIndex, $sizes)) {
                throw new \InvalidArgumentException(sprintf("Size %s doesn't exist", $sizeIndex));
            }

            return $sizes[$sizeIndex];
        }

        ///// TODO [lackovic10] custom sell size 20px
    }

    /**
     * Load and parse sizes from the config file
     *
     * @param string $configFilePath
     *
     * @return array
     */
    protected function loadSizes($configFilePath)
    {
        if ($configFile = @file_get_contents($configFilePath)) {
            return $this->parseSizeConfig($this->parser->parse($configFile));
        } else {
            throw new \RuntimeException('Size config not found');
        }
    }

    /**
     * The method converts a config array to an array of Size objects
     *
     * @param array $config
     *
     * @return array
     */
    protected function parseSizeConfig(array $config)
    {
        $sizes = array();
        foreach ($config as $key => $value) {
            $size = new Size();
            $size->setCell($value['cell'])
                ->setBorder($value['border'])
                ->setCaption($value['caption'])
                ->setCoordinates($value['coordinates']);

            $sizes[] = $size;
        }

        return $sizes;
    }
}
