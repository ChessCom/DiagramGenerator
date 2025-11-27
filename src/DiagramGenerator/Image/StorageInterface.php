<?php

namespace DiagramGenerator\Image;

use DiagramGenerator\Config;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;
use Intervention\Image\Image;

interface StorageInterface
{
    /**
     * @return Image
     */
    public function getPieceImage(Piece $piece, Config $config);

    /**
     * @return Image|null
     */
    public function getBackgroundTextureImage(Config $config);

    /**
     * @return int
     */
    public function getMaxPieceHeight(Fen $fen, Config $config);
}

