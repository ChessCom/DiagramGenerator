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
    protected $themes = array();

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
     * Load and parse theme config file
     * @param  string $resourcesDir
     * @return null
     */
    public function loadThemeConfig($resourcesDir)
    {
        $this->themes = array();
        if ($configFile = @file_get_contents(sprintf("%s/config/theme.yml", $resourcesDir))) {
            $this->parseThemeConfig($this->parser->parse($configFile));
        } else {
            throw new \RuntimeException('Theme config not found');
        }
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

    /**
     * Method to convert array config to array of Theme objects
     * @param  array  $config
     * @return null
     */
    protected function parseThemeConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $theme = $this->serializer->deserialize(json_encode($value), 'DiagramGenerator\Config\Theme', 'json');
            $themeErrors = $this->validator->validate($theme);
            if (count($themeErrors) > 0) {
                throw new InvalidConfigException(sprintf("Theme %u has invalid config: %s", $key, $themeErrors->__toString()));
            }

            // FIXME: figure out why `valid` constraint doesn't work for color property
            $colorErrors = $this->validator->validate($theme->getColor());
            if (count($colorErrors) > 0) {
                throw new InvalidConfigException(sprintf("Theme %u has invalid config: %s", $key, $colorErrors->__toString()));
            }

            $this->themes[] = $theme;
        }
    }
}
