<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Size;
use DiagramGenerator\ConfigLoader;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Exception\UnsupportedConfigException;
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
            throw new \InvalidArgumentException($errors->__toString());
        }

        $themes   = $this->configLoader->getThemes();
        $textures = $this->configLoader->getTextures();

        if (!array_key_exists($config->getThemeIndex(), $themes)) {
            throw new UnsupportedConfigException(sprintf("Theme %s doesn't exist", $config->getThemeIndex()));
        }

        if (is_numeric($config->getSizeIndex())) {
            $sizes = $this->configLoader->getSizes();

            if (!array_key_exists($config->getSizeIndex(), $sizes)) {
                throw new UnsupportedConfigException(sprintf("Size %s doesn't exist", $config->getSizeIndex()));
            }

            $config->setSize($sizes[$config->getSizeIndex()]);
        } else {
            $cellSize = substr($config->getSizeIndex(), 0, -2);

            if ($cellSize < Size::MIN_CUSTOM_SIZE) {
                throw new UnsupportedConfigException(sprintf('Size should be %spx or more', Size::MIN_CUSTOM_SIZE));
            } elseif ($cellSize > Size::MAX_CUSTOM_SIZE) {
                throw new UnsupportedConfigException(sprintf('Size should be %spx or less', Size::MAX_CUSTOM_SIZE));
            }

            $size = new Size();
            $size->setCell($cellSize)
                ->setBorder(Size::BORDER_COEFFICIENT * $cellSize)
                ->setCaption(Size::CAPTION_COEFFICIENT * $cellSize)
                ->setCoordinates(Size::COORDINATES_COEFFICIENT * $cellSize);

            $config->setSize($size);
        }

        if (is_int($config->getTextureIndex())) {
            if (!array_key_exists($config->getTextureIndex(), $textures)) {
                throw new UnsupportedConfigException(sprintf("Texture %s doesn't exist", $config->getTextureIndex()));
            }

            $config->setTexture($textures[$config->getTextureIndex()]);
        }

        $config->setTheme($themes[$config->getThemeIndex()]);
        $config->setHighlightSquares(
            $this->configLoader->parseHighlightSquaresString($config->getHighlightSquares())
        );

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
