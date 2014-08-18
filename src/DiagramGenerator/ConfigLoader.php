<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Theme;
use DiagramGenerator\Config\Size;
use DiagramGenerator\Exception\InvalidConfigException;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Yaml\Parser;

/**
 * Class to load themes config
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class ConfigLoader
{
    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    protected $parser;

    /**
     * @var \JMS\Serializer\SerializerBuilder
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $sizes = array();

    public function __construct(Validator $validator)
    {
        $this->parser     = new Parser();
        $this->serializer = SerializerBuilder::create()->build();
        $this->validator  = $validator;
    }

    /**
     * Gets the value of sizes.
     *
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Load and parse size config file
     * @param  string $resourcesDir
     * @return null
     */
    public function loadSizeConfig($resourcesDir)
    {
        $this->sizes = array();
        if ($configFile = @file_get_contents(sprintf("%s/config/size.yml", $resourcesDir))) {
            $this->parseSizeConfig($this->parser->parse($configFile));
        } else {
            throw new \RuntimeException('Size config not found');
        }
    }

    /**
     * Parse the highlightSquares string into an array of squares
     *
     * @param string $highlightSquares
     */
    public function parseHighlightSquaresString($highlightSquares)
    {
        $highlightSquaresParsed = array();
        for ($i = 0; $i < strlen($highlightSquares); $i+=2) {
            $highlightSquaresParsed[] = $highlightSquares[$i] . $highlightSquares[$i+1];
        }

        return $highlightSquaresParsed;
    }

    /**
     * Method to convert array config to array of Size objects
     * @param  array  $config Parsed yaml config
     * @return null
     */
    protected function parseSizeConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $this->sizes[] = $this->serializer->deserialize(json_encode($value), 'DiagramGenerator\Config\Size', 'json');
        }
    }
}
