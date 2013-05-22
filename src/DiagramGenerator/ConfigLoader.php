<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Theme;
use DiagramGenerator\Config\Size;
use JMS\Serializer\SerializerBuilder;
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
    protected $themes = array();

    /**
     * @var array
     */
    protected $sizes = array();

    public function __construct()
    {
        $this->parser = new Parser();
        $this->load();
    }

    /**
     * Gets the value of themes.
     *
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
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
     * Method loads theme config file and converts it to array of Theme objects
     * @return null
     */
    public function load()
    {
        $this->themes = $this->sizes = array();
        $this->serializer = SerializerBuilder::create()->build();

        foreach (array(
            'theme' => 'theme.yml',
            'size'  => 'size.yml'
        ) as $type => $file) {
            $configFile = file_get_contents(sprintf("%s/Resources/config/%s", __DIR__, $file));
            $this->{sprintf("load%sConfig", $type)}($this->parser->parse($configFile));
        }
    }

    /**
     * Method to convert array config to array of Theme objects
     * @param  array  $config
     * @return null
     */
    protected function loadThemeConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $this->themes[] = $this->serializer->deserialize(json_encode($value), 'DiagramGenerator\Config\Theme', 'json');
        }
    }

    /**
     * Method to convert array config to array of Size objects
     * @param  array  $config Parsed yaml config
     * @return null
     */
    protected function loadSizeConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $this->sizes[] = $this->serializer->deserialize(json_encode($value), 'DiagramGenerator\Config\Size', 'json');
        }
    }
}
