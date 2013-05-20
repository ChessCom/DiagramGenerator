<?php

namespace DiagramGenerator;

use DiagramGenerator\Theme;
use DiagramGenerator\Size;
use DiagramGenerator\Size\Caption;
use DiagramGenerator\Size\Coordinates;
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

    public function getThemes()
    {
        return $this->themes;
    }

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

        $themeConfigFile = file_get_contents(__DIR__.'/Resources/config/theme.yml');
        $this->loadThemeConfig($this->parser->parse($themeConfigFile));

        $sizeConfigFile = file_get_contents(__DIR__.'/Resources/config/size.yml');
        $this->loadSizeConfig($this->parser->parse($sizeConfigFile));
    }

    /**
     * Method to convert array config to array of Theme objects
     * @param  array  $config
     * @return null
     */
    protected function loadThemeConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $theme = new Theme();
            $theme
                ->setFont($value['font'])
                ->setSize($value['size'])
                ->setFigures($value['figures'])
                // ->setLeft($value['left'])
                // ->setBase($value['base'])
            ;
            $this->themes[] = $theme;
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
            $coordinates = new Coordinates();
            $coordinates
                ->setSize($value['coordinates']['size'])
                ->setBase($value['coordinates']['base'])
                ->setLeft($value['coordinates']['left'])
            ;
            $caption = new Caption();
            $caption
                ->setSize($value['caption']['size'])
                ->setBase($value['caption']['base'])
                ->setLeft($value['caption']['left'])
            ;
            $size = new Size();
            $size
                ->setWidth($value['width'])
                ->setHeight($value['height'])
                // ->setBoardX($value['board_x'])
                // ->setBoardY($value['board_y'])
                ->setCell($value['cell'])
                // ->setOutlineThick($value['outline_thick'])
                ->setFrameThick($value['frame_thick'])
                ->setCoordinates($coordinates)
                ->setCaption($caption)
            ;
            $this->sizes[] = $size;
        }
    }
}