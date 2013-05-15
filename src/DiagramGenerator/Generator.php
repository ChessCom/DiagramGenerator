<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Loader\ThemeLoader;

/**
 * Generator class
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Generator
{
    public function __construct()
    {
        $themeLoader = new ThemeLoader();
    }

    public function buildDiagram(Config $config)
    {
        $themes = $this->themeLoader->getThemes();
    }
}