<?php

namespace DiagramGenerator;

use DiagramGenerator\Config;
use DiagramGenerator\Fen;
use DiagramGenerator\Image\Storage;
use DiagramGenerator\Image\Image;

/**
 * Class responsible for drawing the board.
 */
class Board
{
    const SQUARES_IN_ROW = 8; // of course

    /** @var Image */
    protected $image;

    /** @var Config */
    protected $config;

    /** @var Fen */
    protected $fen;

    /** @var */
    protected $rootCacheDir;

    /** @var */
    protected $cacheDirName = 'diagram_generator';

    /** @var */
    protected $cacheDir;

    /** @var string */
    protected $boardTextureUrl;

    /**
     * @var string
     */
    protected $pieceThemeUrl;

    /**
     * Cached Image pieces.
     *
     * @var array
     */
    protected $pieces = [];

    /**
     * @param Config $config
     * @param string $rootCacheDir
     * @param string $boardTextureUrl ex. /boards/__BOARD_TEXTURE__/__SIZE__
     * @param string $pieceThemeUrl   ex. /pieces/__PIECE_THEME__/__SIZE__/__PIECE__
     */
    public function __construct(Config $config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl)
    {
        $this->config = $config;
        $this->rootCacheDir = $rootCacheDir;
        $this->boardTextureUrl = $boardTextureUrl;
        $this->pieceThemeUrl = $pieceThemeUrl;

        $this->cacheDir = $this->rootCacheDir.'/'.$this->cacheDirName;

        @mkdir($this->rootCacheDir.'/'.$this->cacheDirName, 0777);

        $this->fen = Fen::createFromString($this->config->getFen());

        if ($this->config->getFlip()) {
            $this->fen->flip();
        }

        $this->image = $this->generateImage();
    }

    /**
     * Gets the value of image.
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Controls board image generating flow
     *
     * @return Image
     */
    protected function generateImage()
    {
        $storage = new Storage($this->rootCacheDir, $this->pieceThemeUrl, $this->boardTextureUrl);
        $image = new Image($storage, $this->config);
        $topPadding = $storage->getMaxPieceHeight($this->fen, $this->config) - $this->config->getSize()->getCell();

        $image->drawBoardWithFigures(
            $this->fen,
            $this->config->getSize()->getCell(),
            $topPadding
        );

        if ($this->config->getCoordinates()) {
            $image->addCoordinates($topPadding);
        }

        if ($this->config->getCaption()) {
            $image->addCaption();
        }

        return $image;
    }
}
