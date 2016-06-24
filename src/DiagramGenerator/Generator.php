<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Config\Theme;
use DiagramGenerator\Diagram\Board;
use DiagramGenerator\Exception\InvalidConfigException;
use DiagramGenerator\Exception\UnsupportedConfigException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ValidatorInterface;
use InvalidArgumentException;

/**
 * Generator class
 */
class Generator
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var array $boardTextures */
    protected $boardTextures = array();

    /** @var array $pieceThemes */
    protected $pieceThemes = array();

    public function __construct(ValidatorInterface $validator)
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
     * @param Config $config
     * @param $rootCacheDir
     * @param $boardTextureUrl
     * @param $pieceThemeUrl
     *
     * @return Diagram
     *
     * @throws InvalidConfigException
     */
    public function buildDiagram(Config $config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl)
    {
        $errors = $this->validator->validate($config);
        if (count($errors) > 0) {
            throw new InvalidConfigException($errors->__toString());
        }

        if ($config->getTexture()) {
            $this->validateBoardTexture($config->getTexture());
        }

        $this->setConfigSize($config);
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
     * @param Texture $textureForValidation
     *
     * @throws InvalidArgumentException
     */
    protected function validateBoardTexture(Texture $textureForValidation)
    {
        foreach ($this->boardTextures as $boardTexture) {
            if ($boardTexture->is($textureForValidation)) {
                return;
            }
        }

        throw new InvalidArgumentException(
            sprintf('Board texture %s does not exist', $textureForValidation->getName())
        );
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
            throw new InvalidArgumentException(sprintf('Piece theme %s does not exist', $pieceTheme));
        }

        $theme = new Theme();
        $config->setTheme($theme->setName($pieceTheme));
    }

    /**
     * Parse the highlightSquares string into an array of squares
     *
     * @param string $highlightSquares
     *
     * @return array
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
