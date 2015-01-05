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
        'tournament', 'vintage', 'wood', 'chesskid'
    );

    protected $boardTextures = array('blackwhite', 'blue', 'brown', 'bubblegum', 'burled_wood', 'dark_wood',
        'glass', 'graffiti', 'green', 'light', 'lolz', 'marble', 'marbleblue', 'marblegreen', 'metal', 'neon',
        'newspaper', 'orange', 'parchment', 'purple', 'red', 'sand', 'sky', 'stone', 'tan', 'tournament',
        'translucent', 'woodolive'
    );

    public function __construct(Validator $validator)
    {
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
    public function buildDiagram(Config $config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl)
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

        $board = new Board($config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl);
        $board->drawBoard()
            ->drawCells()
            ->drawFigures()
            ->draw();

        $diagram = new Diagram($config);
        $diagram->setBoard($board)
            ->draw();

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
        $boardTexture = is_numeric($config->getTextureIndex()) ?
            $this->deprecateBoardTextures[$config->getTextureIndex()] : $config->getTextureIndex();

        if ($boardTexture && !in_array($boardTexture, $this->boardTextures)) {
            throw new \InvalidArgumentException(sprintf('Board texture %s does not exist', $boardTexture));
        }

        $texture = new Texture();
        $config->setTexture($texture->setBoard($boardTexture));
    }

    /**
     * Set the config piece theme
     *
     * @param Config $config
     */
    protected function setConfigPieceTheme(Config $config)
    {
        $pieceTheme = is_numeric($config->getThemeIndex()) ?
            $this->deprecatedPieceThemes[$config->getThemeIndex()] : $config->getThemeIndex();

        if (!in_array($pieceTheme, $this->pieceThemes)) {
            throw new \InvalidArgumentException(sprintf('Piece theme %s does not exist', $pieceTheme));
        }

        $theme = new Theme();
        $config->setTheme($theme->setName($pieceTheme));
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
}
