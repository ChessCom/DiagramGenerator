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
    protected $cacheDir;

    /** @var string */
    protected $boardTextureUrl;

    /**
     * @var string
     */
    protected $pieceThemeUrl;

    /**
     * @param string $rootCacheDir
     * @param string $boardTextureUrl ex. /boards/__BOARD_TEXTURE__/__SIZE__
     * @param string $pieceThemeUrl   ex. /pieces/__PIECE_THEME__/__SIZE__/__PIECE__
     */
    public function __construct(Config $config, $rootCacheDir, $boardTextureUrl, $pieceThemeUrl)
    {
        $this->config = $config;
        $this->boardTextureUrl = $boardTextureUrl;
        $this->pieceThemeUrl = $pieceThemeUrl;

        $this->cacheDir = $rootCacheDir.'/diagram_generator';
        @mkdir($this->cacheDir, 0777);

        $this->fen = Fen::createFromString($this->config->getFen());

        if ($this->config->getFlip()) {
            $this->fen->flip();
        }
    }

    /**
     * Gets the value of image.
     *
     * @return Image
     */
    public function getImage()
    {
        if ($this->image === null) {
            $this->image = $this->generateImage();
        }

        return $this->image;
    }

    /**
     * Controls board image generating flow
     *
     * @return Image
     */
    protected function generateImage()
    {
        $storage = new Storage($this->cacheDir, $this->pieceThemeUrl, $this->boardTextureUrl);
        $image = new Image($storage, $this->config);
        $topPadding = $storage->getMaxPieceHeight($this->fen, $this->config) - $this->config->getSize()->getCell();

        $image->drawBoardWithFigures(
            $this->fen,
            $this->config->getSize()->getCell(),
            $topPadding
        );

        if ($this->config->getCoordinates()) {
            $image->addCoordinates($topPadding, $this->config->getCoordinatesInside());
        }

        if ($this->config->getCaption()) {
            $image->addCaption();
        }

        return $image;
    }
}
