<?php

namespace DiagramGenerator\Config\Loader;

use Symfony\Component\Yaml\Parser;
use DiagramGenerator\Config\Board;
use DiagramGenerator\Config\Input;

class BoardLoader
{
    /** @var \Symfony\Component\Yaml\Parser $parser */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the board texture for the users input parameter
     *
     * @param Input  $configInput
     * @param string $configFilePath
     * @param int    $boardTextureFilename
     *
     * @return Board
     */
    public function getBoard(Input $configInput, $configFilePath, $boardTextureIndex)
    {
        $board = $this->createBoard($configInput);

        if (!$boardTextureIndex) {
            return $board;
        }

        $boardTexturesConfig = $this->loadBoardTextures($configFilePath);

        if (!array_key_exists($boardTextureIndex, $boardTexturesConfig)) {
            throw new \InvalidArgumentException(
                sprintf("Board texture %s doesn't exist", $boardTextureIndex)
            );
        }

        return $board->setTextureFilename($boardTexturesConfig[$boardTextureIndex]['filename']);
    }

    /**
     * Create a Config\Board instance and populate with values from config input
     *
     * @param Config\Input
     *
     * @return Board
     */
    protected function createBoard(Input $configInput)
    {
        $board = new Board();

        return $board->setCaption($configInput->getCaption())
            ->setCoordinates($configInput->getCoordinates())
            ->setLightCellColor($configInput->getLightCellColor())
            ->setDarkCellColor($configInput->getDarkCellColor())
            ->setFlip($configInput->getFlip())
            ->setHighlightSquares($this->parseHighlightSquaresString($configInput->getHighlightSquares()))
            ->setHighlightSquaresColor($configInput->getHighlightSquaresColor());
    }

    /**
     * Load and parse board textures from the config file
     *
     * @param string $configFilePath
     *
     * @return array
     */
    protected function loadBoardTextures($configFilePath)
    {
        if ($configFile = @file_get_contents($configFilePath)) {
            return $this->parser->parse($configFile);
        } else {
            throw new \RuntimeException('Texture config not found');
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
}
