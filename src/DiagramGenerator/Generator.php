<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Input;
use DiagramGenerator\Config\Parser;
use Symfony\Component\Validator\ValidatorInterface;

use DiagramGenerator\ConfigLoader;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Exception\InvalidConfigException;


/**
 * Generator class
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Generator
{
    /** @var \DiagramGenerator\Config\Parser $configParser */
    protected $configParser;

    /** @var \Symfony\Component\Validator\ValidatorInterface $validator */
    protected $validator;

    public function __construct(Parser $configParser, ValidatorInterface $validator)
    {
        $this->configParser = $configParser;
        $this->validator    = $validator;
    }

    /**
     * @return string
     */
    public static function getResourcesDir()
    {
        return __DIR__.'/Resources';
    }

    /**
     * Build the chess diagram
     *
     * @param  Config\Input $configInput
     *
     * @return \DiagramGenerator\Diagram
     */
    public function buildDiagram(Input $configInput)
    {
        $errors = $this->validator->validate($configInput);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException($errors->__toString());
        }

        $config = $this->configParser->parseConfig($configInput);

        $board = $this->createBoard($config);
        $diagram = $this->createDiagram($config, $board);

        return $diagram;



        $themes   = $this->configLoader->getThemes();
        $sizes    = $this->configLoader->getSizes();
        $textures = $this->configLoader->getTextures();

        if (!array_key_exists($config->getThemeIndex(), $themes)) {
            throw new InvalidConfigException(sprintf("Theme %s doesn't exist", $config->getThemeIndex()));
        }

        if (!array_key_exists($config->getSizeIndex(), $sizes)) {
            throw new InvalidConfigException(sprintf("Size %s doesn't exist", $config->getSizeIndex()));
        }

        if (is_int($config->getTextureIndex())) {
            if (!array_key_exists($config->getTextureIndex(), $textures)) {
                throw new InvalidConfigException(sprintf("Texture %s doesn't exist", $config->getTextureIndex()));
            }

            $config->setTexture($textures[$config->getTextureIndex()]);
        }

        $config->setTheme($themes[$config->getThemeIndex()]);
        $config->setSize($sizes[$config->getSizeIndex()]);

        $board = $this->createBoard($config);
        $diagram = $this->createDiagram($config, $board);

        return $diagram;
    }

    /**
     * Creates board image
     *
     * @param Config $config
     *
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
