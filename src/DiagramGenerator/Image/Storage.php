<?php

namespace DiagramGenerator\Image;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Texture;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Piece;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;
use RuntimeException;

class Storage
{
    protected $pieces = [];

    /** @var string */
    protected $cacheDirectory;

    /** @var string */
    protected $pieceThemeUrl;

    /** @var string */
    protected $boardTextureUrl;

    /**
     * @param string $cacheDirectory
     * @param string $pieceThemeUrl
     * @param string $boardTextureUrl
     */
    public function __construct($cacheDirectory, $pieceThemeUrl, $boardTextureUrl)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->pieceThemeUrl = $pieceThemeUrl;
        $this->boardTextureUrl = $boardTextureUrl;
    }

    /**
     * @param Piece  $piece
     * @param Config $config
     *
     * @return Image
     */
    public function getPieceImage(Piece $piece, Config $config)
    {
        $cacheKey = implode('.', [$piece->getColor(), $piece->getKey(), $piece->getColumn(), $piece->getRow()]);

        if (!isset($this->pieces[$cacheKey])) {
            $this->pieces[$cacheKey] = $this->fetchRemotePieceImage($piece, $config);
        }

        return $this->pieces[$cacheKey];
    }

    /**
     * @param Config $config
     *
     * @return Image|null
     */
    public function getBackgroundTextureImage(Config $config)
    {
        if (!$config->getTexture()) {
            return null;
        }

        $boardCachedPath = $this->getCachedTextureFilePath($config);

        try {
            return ImageManagerStatic::make($boardCachedPath);
        } catch (NotReadableException $exception) {
            @mkdir($this->cacheDirectory.'/board/'.$config->getTexture()->getImageUrlFolderName(), 0777, true);

            $boardTextureUrl = str_replace(
                '__BOARD_TEXTURE__', $config->getTexture()->getImageUrlFolderName(), $this->boardTextureUrl
            );
            $boardTextureUrl = str_replace('__SIZE__', $config->getSize()->getCell(), $boardTextureUrl);
            $boardTextureUrl .= '.'.$config->getTexture()->getImageFormat();

            $this->cacheImage($boardTextureUrl, $boardCachedPath);

            return ImageManagerStatic::make($boardCachedPath);
        }
    }

    /**
     * Finds max height of piece image
     *
     * @param Fen $fen
     * @param Config $config
     *
     * @return int
     */
    public function getMaxPieceHeight(Fen $fen, Config $config)
    {
        $maxHeight = $config->getSize()->getCell();
        foreach ($fen->getPieces() as $piece) {
            $pieceImage = $this->getPieceImage($piece, $config);

            if ($pieceImage->getHeight() > $maxHeight) {
                $maxHeight = $pieceImage->getHeight();
            }

            unset($pieceImage);
        }

        return $maxHeight;
    }

    /**
     * In piece image is not found in local storage, passes control to self::cacheImage()
     *
     * @param Piece $piece
     * @param Config $config
     *
     * @return Image
     */
    protected function fetchRemotePieceImage(Piece $piece, Config $config)
    {
        $pieceThemeName = $config->getTheme()->getName();
        $cellSize = $config->getSize()->getCell();
        $pieceCachedPath = $this->getCachedPieceFilePath($pieceThemeName, $cellSize, $piece->getShortName());

        try {
            $image = ImageManagerStatic::make($pieceCachedPath);
        } catch (NotReadableException $exception) {
            $this->downloadPieceImages($config);
            $image = ImageManagerStatic::make($pieceCachedPath);
        }

        return $image;
    }

    protected function getCachedPieceFilePath($pieceThemeName, $cellSize, $piece)
    {
        return sprintf(
            '%s/%s/%d/%s.%s',
            $this->cacheDirectory,
            $pieceThemeName,
            $cellSize,
            $piece,
            Texture::IMAGE_FORMAT_PNG
        );
    }

    protected function getCachedTextureFilePath(Config $config)
    {
        return sprintf(
            '%s/board/%s/%d.%s',
            $this->cacheDirectory,
            $config->getTexture()->getImageUrlFolderName(),
            $config->getSize()->getCell(),
            $config->getTexture()->getImageFormat()
        );
    }

    /**
     * Fetches remove file, and stores it locally
     *
     * @param $remoteImageUrl
     * @param $cachedFilePath
     */
    protected function cacheImage($remoteImageUrl, $cachedFilePath)
    {
        $cachedFilePathTmp = $cachedFilePath.uniqid('', true);
        $ch = curl_init($remoteImageUrl);
        $destinationFileHandle = fopen($cachedFilePathTmp, 'wb');

        if (!$destinationFileHandle) {
            throw new RuntimeException(sprintf('Could not open temporary file: %s', $cachedFilePathTmp));
        }

        curl_setopt($ch, CURLOPT_FILE, $destinationFileHandle);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($destinationFileHandle);

        rename($cachedFilePathTmp, $cachedFilePath);
    }

    private function downloadPieceImages(Config $config)
    {
        $pieces = Piece::generateAllPieces();

        $pieceThemeName = $config->getTheme()->getName();
        $cellSize = $config->getSize()->getCell();
        @mkdir($this->cacheDirectory.'/'.$pieceThemeName.'/'.$cellSize, 0777, true);

        $handles = [];
        $fileHandles = [];
        $multiHandle = curl_multi_init();

        foreach ($pieces as $piece) {
            $pieceUrl = $this->generatePieceUrl($piece, $config);
            $handles[$piece->getShortName()] = curl_init($pieceUrl);
            $filePath = $this->getCachedPieceFilePath($pieceThemeName, $cellSize, $piece->getShortName());
            $uniqid = uniqid();
            $fileHandles[$piece->getShortName()] = [
                'handle' => fopen($filePath . $uniqid, 'wb'),
                'tmpPath' => $filePath . $uniqid,
                'realPath' => $filePath,
            ];
        }

        foreach($handles as $key => $handle) {
            curl_setopt($handle, CURLOPT_FILE, $fileHandles[$key]['handle']);
            curl_setopt($handle, CURLOPT_HEADER, 0);

            curl_multi_add_handle($multiHandle, $handle);
        }

        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        foreach ($fileHandles as $fileHandle) {
            rename($fileHandle['tmpPath'], $fileHandle['realPath']);
        }

        curl_multi_close($multiHandle);
    }

    private function generatePieceUrl(Piece $piece, Config $config)
    {
        $pieceThemeName = $config->getTheme()->getName();
        $cellSize = $config->getSize()->getCell();

        $pieceThemeUrl = strtr(
            $this->pieceThemeUrl,
            [
                '__PIECE_THEME__' => $pieceThemeName,
                '__SIZE__' => $cellSize,
                '__PIECE__' => $piece->getShortName(),
            ]
        );
        $pieceThemeUrl .= '.'.Texture::IMAGE_FORMAT_PNG;

        return $pieceThemeUrl;
    }
}
