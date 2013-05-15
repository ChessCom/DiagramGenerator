<?php

namespace DiagramGenerator\Loader;

use DiagramGenerator\Theme;
use Symfony\Component\Yaml\Parser;

/**
 * Class to load themes config
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class ThemeLoader
{
    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    protected $parser;

    /**
     * @var array
     */
    protected $themes = array();

    public function __construct()
    {
        $this->parser = new Parser();
        $this->load();
    }

    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Method loads theme config file and converts it to array of Theme objects
     * @return null
     */
    public function load()
    {
        $this->themes = array();
        $configFile = file_get_contents(__DIR__.'/../Resources/config/themes.yml');
        $config = $this->parser->parse($configFile);

        foreach ($config as $key => $value) {
            $theme = new Theme();
            $theme
                ->setFont($value['font'])
                ->setSize($value['size'])
                ->setFigures($value['figures'])
                ->setLeft($value['left'])
                ->setBase($value['base'])
            ;
            $this->themes[] = $theme;
        }
    }
}