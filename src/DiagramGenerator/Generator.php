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
        $this->validator    = $validator;
        $this->configLoader = new ConfigLoader($validator);
        $this->configLoader->loadSizeConfig(self::getResourcesDir());
        $this->configLoader->loadThemeConfig(self::getResourcesDir());
        $this->configLoader->loadTextureConfig(self::getResourcesDir());
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

        $themes   = $this->configLoader->getThemes();
        $sizes    = $this->configLoader->getSizes();
        $textures = $this->configLoader->getTextures();

        if (!array_key_exists($config->getThemeIndex(), $themes)) {
            throw new InvalidConfigException(sprintf("Theme %s doesn't exist", $config->getThemeIndex()));
        }

        if (!array_key_exists($config->getSizeIndex(), $sizes)) {
            throw new InvalidConfigException(sprintf("Size %s doesn't exist", $config->getSizeIndex()));
        }

        if (!array_key_exists($config->getTextureIndex(), $textures)) {
            throw new InvalidConfigException(sprintf("Texture %s doesn't exist", $config->getTextureIndex()));
        }

        $config->setTheme($themes[$config->getThemeIndex()]);
        $config->setSize($sizes[$config->getSizeIndex()]);
        $config->setTexture($textures[$config->getTextureIndex()]);

        $board = $this->createBoard($config);
        $diagram = $this->createDiagram($config, $board);

        return $diagram;
    }

    /**
     * Creates board image
     * @param  Config $config
     * @return \DiagramGenerator\Diagram\Board
     */
    protected function createBoard(Config $config)
    {
        $board = new Board($config);
        $board
            ->drawBoard()
            ->drawCells()
            ->drawFigures()
            ->drawBorder()
            ->draw();

        return $board;
    }

    /**
     * Creates diagram
     * @param  Config $config
     * @param  Board  $board
     * @return \DiagramGenerator\Diagram
     */
    protected function createDiagram(Config $config, Board $board)
    {
        $diagram = new Diagram($config);
        $diagram
            ->setBoard($board)
            ->draw();

        return $diagram;
    }
}
