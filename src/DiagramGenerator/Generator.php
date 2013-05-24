<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\ConfigLoader;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Exception\InvalidConfigException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator;

/**
 * Generator class
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Generator
{
    /**
     * @var \DiagramGenerator\ConfigLoader;
     */
    protected $configLoader;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->configLoader = new ConfigLoader();
        $this->validator = $validator;
    }

    /**
     * @return string
     */
    public static function getResourcesDir()
    {
        return __DIR__.'/Resources';
    }

    /**
     * @param  Config $config
     * @return \DiagramGenerator\Diagram
     */
    public function buildDiagram(Config $config)
    {
        $errors = $this->validator->validate($config);
        if (count($errors) > 0) {
            throw new InvalidConfigException($errors->__toString());
        }

        $themes = $this->configLoader->getThemes();
        $sizes  = $this->configLoader->getSizes();

        if (!array_key_exists($config->getThemeIndex(), $themes)) {
            throw new InvalidConfigException(sprintf("Theme %s doesn't exist", $config->getTheme()));
        }

        if (!array_key_exists($config->getSizeIndex(), $sizes)) {
            throw new InvalidConfigException(sprintf("Size %s doesn't exist", $config->getSize()));
        }

        $config->setTheme($themes[$config->getThemeIndex()]);
        $config->setSize($sizes[$config->getSizeIndex()]);

        $board = new Board($config);
        $board
            ->drawBoard()
            ->drawCells()
            ->drawFigures()
            ->drawBorder()
            ->draw();

        $diagram = new Diagram($config);
        $diagram
            ->setBoard($board)
            ->draw();

        return $diagram;
    }
}
