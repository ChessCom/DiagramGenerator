<?php

namespace DiagramGenerator\Config;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Loader\SizeLoader;
use DiagramGenerator\Config\Loader\BoardLoader;
use DiagramGenerator\Config\Loader\PieceThemeLoader;
use DiagramGenerator\Generator;

class Parser
{
    protected $sizeConfigFilename = 'size.yml';
    protected $boardTextureConfigFilename = 'board_texture.yml';
    protected $pieceThemeConfigFilename = 'piece_theme.yml';

    /** @var \DiagramGenerator\Config\Loader\SizeLoader $sizeLoader */
    protected $sizeLoader;

    /** @var \DiagramGenerator\Config\Loader\BoardLoader $boardLoader */
    protected $boardLoader;

    /** @var \DiagramGenerator\Config\Loader\PieceThemeLoader $pieceThemeLoader */
    protected $pieceThemeLoader;

    public function __construct(
        SizeLoader $sizeLoader, BoardLoader $boardLoader, PieceThemeLoader $pieceThemeLoader
    ) {
        $this->sizeLoader       = $sizeLoader;
        $this->boardLoader      = $boardLoader;
        $this->pieceThemeLoader = $pieceThemeLoader;
    }

    /**
     * @return Config
     */
    public function parseConfig(Input $configInput)
    {
        $size = $this->sizeLoader->getSize(
            Generator::getResourcesDir() . '/config//' . $this->sizeConfigFilename,
            $configInput->getSizeIndex()
        );

        $board = $this->boardLoader->getBoard(
            $configInput,
            Generator::getResourcesDir() . '/config//' . $this->boardTextureConfigFilename,
            $configInput->getBoardTextureIndex()
        );

        $pieceTheme = $this->pieceThemeLoader->getPieceTheme(
            Generator::getResourcesDir() . '/config//' . $this->pieceThemeConfigFilename,
            $configInput->getPieceThemeIndex()
        );

        $config = new Config();
        $config->setFen($configInput->getFen())
            ->setSize($size)
            ->setBoard($board)
            ->setPieceTheme($pieceTheme);

        var_dump($config);
        die();

        return $parsedConfig;
    }
}
