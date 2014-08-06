<?php

namespace DiagramGenerator\Config\Loader;

use Symfony\Component\Yaml\Parser;

class PieceThemeLoader
{
    /** @var \Symfony\Component\Yaml\Parser $parser */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the piece theme for the users input parameter
     *
     * @param string $configFilePath
     * @param int    $pieceThemeIndex
     *
     * @return string
     */
    public function getPieceTheme($configFilePath, $pieceThemeIndex)
    {
        $pieceThemesConfig = $this->loadPieceThemes($configFilePath);

        if (!array_key_exists($pieceThemeIndex, $pieceThemesConfig)) {
            throw new \InvalidArgumentException(sprintf("Theme %s doesn't exist", $pieceThemeIndex));
        }

        return $pieceThemesConfig[$pieceThemeIndex]['name'];
    }

    /**
     * Load piece themes config from the config file
     *
     * @param string $configFilePath
     *
     * @return array
     */
    protected function loadPieceThemes($configFilePath)
    {
        if ($configFile = @file_get_contents($configFilePath)) {
            return $this->parser->parse($configFile);
        } else {
            throw new \RuntimeException('Piece themes config not found');
        }
    }
}
