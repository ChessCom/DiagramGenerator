<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Config\Theme;
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
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * Deprecating the size index format, using {cellSize}px format instead. Keeping the size indexes for backwards
     * compatibility, we will not provide new indexes in the future
     */
    protected $deprecatedSizes = array(20, 30, 60, 90);

    /**
     * Deprecating board texture index format, using the board texture name instead. Keeping the board texture
     * indexes for backwards compatibility, we will not provide new indexes in the future
     */
    protected $deprecateBoardTextures = array('neon', 'dark_wood', 'burled_wood', 'metal');

    /**
     * Deprecating piece theme index format, using the piece theme name instead. Keeping the piece theme indexes
     * for backwards compatibility, we will not provide new indexes in the future
     */
    protected $deprecatedPieceThemes = array('classic', 'alpha', 'book', 'club', 'modern', 'vintage');

    protected $pieceThemes = array(
        '3d_chesskid', '3d_plastic', '3d_staunton', '3d_wood', 'alpha', 'blindfold', 'book', 'bubblegum', 'cases',
        'classic', 'club', 'condal', 'dark', 'game_room', 'glass', 'gothic', 'graffiti', 'light', 'lolz', 'marble',
        'maya', 'metal', 'mini', 'modern', 'nature', 'neon', 'newspaper', 'ocean', 'sky', 'space', 'tigers',
        'tournament', 'vintage', 'wood'
    );

    protected $boardTextures = array('blackwhite', 'blue', 'brown', 'bubblegum', 'burled_wood', 'dark_wood',
        'glass', 'graffiti', 'green', 'light', 'lolz', 'marble', 'marbleblue', 'marblegreen', 'metal', 'neon',
        'newspaper', 'orange', 'parchment', 'purple', 'red', 'sand', 'sky', 'stone', 'tan', 'tournament',
        'translucent', 'woodolive'
    );

    public function __construct(Validator $validator)
    {
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
            $this->parseHighlightSquaresString($config->getHighlightSquares())
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
        $cellSize = is_numeric($config->getSizeIndex()) ?
            $this->deprecatedSizes[$config->getSizeIndex()] : substr($config->getSizeIndex(), 0, -2);


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
     * Parse the highlightSquares string into an array of squares
     *
     * @param string $highlightSquares
     */
    protected function parseHighlightSquaresString($highlightSquares)
    {
        $highlightSquaresParsed = array();
        for ($i = 0; $i < strlen($highlightSquares); $i+=2) {
            $highlightSquaresParsed[] = $highlightSquares[$i] . $highlightSquares[$i+1];
        }

        return $highlightSquaresParsed;
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
