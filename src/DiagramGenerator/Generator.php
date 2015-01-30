<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Config\Theme;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Exception\UnsupportedConfigException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator as Validator;

/**
 * Generator class
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Generator
{
    /** @var \Symfony\Component\Validator\Validator */
    protected $validator;

    /** @var array $boardTextures */
    protected $boardTextures = array();

    /** @var array $pieceThemes */
    protected $pieceThemes = array();

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

    public function setBoardTextures(array $boardTextures)
    {
        $this->boardTextures = $boardTextures;

        return $this;
    }

    public function setPieceThemes(array $pieceThemes)
    {
        $this->pieceThemes = $pieceThemes;

        return $this;
    }

    /**
     * Set the config size
     *
     * @param Config $config
     */
    protected function setConfigSize(Config $config)
    {
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

    /**
     * Set the config board texture
     *
     * @param Config $config
     */
    protected function setConfigBoardTexture(Config $config)
    {
        $boardTexture = $config->getBoardIndex();

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
        $pieceTheme = $config->getPieceIndex();

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
