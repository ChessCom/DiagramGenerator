<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Config\Theme;
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

    /**
     * Deprecating the passing an integer that represents the board texture index, which determines the board
     * texture data which is recorded in a config file. Not using the config file anymore
     */
    protected $deprecateBoardTextures = array(0 => 'neon', 1 => 'dark_wood', 2 => 'burled_wood', 3 => 'metal');

    /**
     * Deprecating the passing an integer that represents the piece theme index, which determines the piece theme
     * data which is recorded in a config file. Not using the config file anymore
     */
    protected $deprecatedPieceThemes = array(
        0 => 'classic', 1 => 'alpha', 2 => 'book', 3 => 'club', 4 => 'modern', 5 => 'vintage'
    );

    public function __construct(Validator $validator)
    {
        $this->validator    = $validator;
        $this->configLoader = new ConfigLoader($validator);
        $this->configLoader->loadSizeConfig(self::getResourcesDir());
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
            throw new \Exception($errors->__toString());
        }

        $this->setConfigSize($config);
        $this->setConfigBoardTexture($config);
        $this->setConfigPieceTheme($config);
        
        $config->setHighlightSquares(
            $this->configLoader->parseHighlightSquaresString($config->getHighlightSquares())
        );

        $board = $this->createBoard($config);
        $diagram = $this->createDiagram($config, $board);

        return $diagram;
    }

    /**
     * Set the config size
     *
     * @param Config $config
     */
    protected function setConfigSize(Config $config)
    {
        if (is_numeric($config->getSizeIndex())) {
            // numeric sizes are deprecated, keeping numeric sizes for backwords compatibility (0-3).
            $sizes = $this->configLoader->getSizes();

            if (!array_key_exists($config->getSizeIndex(), $sizes)) {
                throw new \Exception(sprintf("Size %s doesn't exist", $config->getSizeIndex()));
            }

            $config->setSize($sizes[$config->getSizeIndex()]);
        } else {
            $cellSize = substr($config->getSizeIndex(), 0, -2);

            if ($cellSize < Size::MIN_CUSTOM_SIZE) {
                throw new \InvalidArgumentException(
                    sprintf('Size should be %spx or more', Size::MIN_CUSTOM_SIZE)
                );
            } elseif ($cellSize > Size::MAX_CUSTOM_SIZE) {
                throw new \InvalidArgumentException(
                    sprintf('Size should be %spx or less', Size::MAX_CUSTOM_SIZE)
                );
            }

            $size = new Size();
            $size->setCell($cellSize)
                ->setBorder(Size::BORDER_COEFFICIENT * $cellSize)
                ->setCaption(Size::CAPTION_COEFFICIENT * $cellSize)
                ->setCoordinates(Size::COORDINATES_COEFFICIENT * $cellSize);

            $config->setSize($size);
        }
    }

    /**
     * Set the config board texture
     *
     * @param Config $config
     */
    protected function setConfigBoardTexture(Config $config)
    {
        $texture = new Texture();
        if (is_numeric($config->getTextureIndex())) {
            if (!array_key_exists($config->getTextureIndex(), $this->deprecateBoardTextures)) {
                throw new \RuntimeException(sprintf("Texture %s doesn't exist", $config->getTextureIndex()));
            }

            $config->setTexture($texture->setBoard($this->deprecateBoardTextures[$config->getTextureIndex()]));
        } else {
            $config->setTexture($texture->setBoard($config->getTextureIndex()));
        }
    }

    /**
     * Set the config piece theme
     *
     * @param Config $config
     */
    protected function setConfigPieceTheme(Config $config)
    {
        $theme = new Theme();
        if (is_numeric($config->getThemeIndex())) {
            if (!array_key_exists($config->getThemeIndex(), $this->deprecatedPieceThemes)) {
                throw new \Exception(sprintf("Theme %s doesn't exist", $config->getThemeIndex()));
            }

            $config->setTheme($theme->setName($this->deprecatedPieceThemes[$config->getThemeIndex()]));
        } else {
            $config->setTheme($theme->setName($config->getThemeIndex()));
        }
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
