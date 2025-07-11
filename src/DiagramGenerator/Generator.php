<?php

namespace DiagramGenerator;

use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Config\Theme;
use DiagramGenerator\Exception\InvalidConfigException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use InvalidArgumentException;

/**
 * Generator class.
 */
class Generator
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var array $boardTextures */
    protected $boardTextures = [];

    /** @var array $pieceThemes */
    protected $pieceThemes = [];

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
     * @param $rootCacheDir
     * @param $boardTextureUrl
     * @param $pieceThemeUrl
     *
     * @return Board
     * @throws InvalidConfigException
     */
    public function buildBoard(Config $config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl)
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

        return new Board($config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl);
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
     * Set the config size.
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
     * Set the config piece theme.
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
     * Parse the highlightSquares string into an array of squares.
     *
     * @param string $highlightSquares
     *
     * @return array
     */
    protected function parseHighlightSquaresString($highlightSquares)
    {
        $highlightSquaresParsed = [];
        for ($i = 0; $i < strlen($highlightSquares); $i += 2) {
            $highlightSquaresParsed[] = $highlightSquares[$i].$highlightSquares[$i + 1];
        }

        return $highlightSquaresParsed;
    }
}
