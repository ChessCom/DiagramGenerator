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

class StorageNew
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
     *
     * @return Image
     */
    public function getPieceImage(Piece $piece, Config $config)
    {
        return $this->getPieceImageFromTheme($piece, $config);
    }

    /**
     * Gets piece image from theme URLs.
     *
     * @return Image
     */
    protected function getPieceImageFromTheme(Piece $piece, Config $config)
    {
        $themeUrls = $config->getThemeUrls();
        $pieceShortName = $piece->getShortName();

        if (!isset($themeUrls[$pieceShortName])) {
            throw new RuntimeException(sprintf('Piece URL not found in theme for piece: %s', $pieceShortName));
        }

        $pieceUrl = $themeUrls[$pieceShortName];
        $cacheKey = $pieceUrl;

        if (!isset($this->pieces[$cacheKey])) {
            $this->pieces[$cacheKey] = $this->fetchRemotePieceImageFromTheme($piece, $config);
        }

        return $this->pieces[$cacheKey];
    }

    /**
     * @return Image|null
     */
    public function getBackgroundTextureImage(Config $config)
    {
        return $this->getBackgroundTextureImageFromTheme($config);
    }

    /**
     * Gets background texture image from theme URL.
     *
     * @return Image|null
     */
    protected function getBackgroundTextureImageFromTheme(Config $config)
    {
        $themeUrls = $config->getThemeUrls();

        if (!isset($themeUrls['board'])) {
            return null;
        }

        $boardUrl = $themeUrls['board'];
        $boardCachedPath = $this->getCachedTextureFilePathFromTheme($boardUrl);

        try {
            return ImageManagerStatic::make($boardCachedPath);
        } catch (NotReadableException $exception) {
            @mkdir(dirname($boardCachedPath), 0777, true);
            $this->cacheImage($boardUrl, $boardCachedPath);
            return ImageManagerStatic::make($boardCachedPath);
        }
    }

    /**
     * Finds max height of piece image
     *
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
     * Fetches piece image from theme URL.
     *
     * @return Image
     */
    protected function fetchRemotePieceImageFromTheme(Piece $piece, Config $config)
    {
        $themeUrls = $config->getThemeUrls();
        $pieceShortName = $piece->getShortName();

        if (!isset($themeUrls[$pieceShortName])) {
            throw new RuntimeException(sprintf('Piece URL not found in theme for piece: %s', $pieceShortName));
        }

        $pieceUrl = $themeUrls[$pieceShortName];
        $pieceCachedPath = $this->getCachedPieceFilePathFromTheme($pieceUrl, $pieceShortName);

        try {
            $image = ImageManagerStatic::make($pieceCachedPath);
        } catch (NotReadableException $exception) {
            $this->downloadPieceImagesFromTheme($config);
            $image = ImageManagerStatic::make($pieceCachedPath);
        }

        return $image;
    }

    /**
     * Downloads all piece images from theme URLs.
     */
    private function downloadPieceImagesFromTheme(Config $config)
    {
        $themeUrls = $config->getThemeUrls();
        $pieces = Piece::generateAllPieces();

        $handles = [];
        $fileHandles = [];
        $multiHandle = curl_multi_init();

        foreach ($pieces as $piece) {
            $pieceShortName = $piece->getShortName();

            if (!isset($themeUrls[$pieceShortName])) {
                continue; // Skip pieces without URLs
            }

            $pieceUrl = $themeUrls[$pieceShortName];
            $filePath = $this->getCachedPieceFilePathFromTheme($pieceUrl, $pieceShortName);
            @mkdir(dirname($filePath), 0777, true);

            $uniqid = uniqid();
            $handles[$pieceShortName] = curl_init($pieceUrl);
            $fileHandles[$pieceShortName] = [
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
            if (isset($fileHandle['tmpPath']) && file_exists($fileHandle['tmpPath'])) {
                rename($fileHandle['tmpPath'], $fileHandle['realPath']);
            }
        }

        curl_multi_close($multiHandle);
    }

    /**
     * Gets cached piece file path for theme URLs.
     * Uses the URL to generate a unique cache key.
     *
     * @param string $pieceUrl The URL of the piece image
     * @param string $piece The piece short name (e.g., 'wp', 'bk')
     * @return string
     */
    protected function getCachedPieceFilePathFromTheme($pieceUrl, $piece)
    {
        $urlHash = md5($pieceUrl);
        $extension = pathinfo(parse_url($pieceUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: Texture::IMAGE_FORMAT_PNG;

        return sprintf(
            '%s/theme/%s/%s.%s',
            $this->cacheDirectory,
            $urlHash,
            $piece,
            $extension
        );
    }

    /**
     * Gets cached texture file path for theme URLs.
     * Uses the URL to generate a unique cache key.
     *
     * @param string $boardUrl The URL of the board image
     * @return string
     */
    protected function getCachedTextureFilePathFromTheme($boardUrl)
    {
        $urlHash = md5($boardUrl);
        $extension = pathinfo(parse_url($boardUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: Texture::IMAGE_FORMAT_PNG;

        return sprintf(
            '%s/board/theme/%s.%s',
            $this->cacheDirectory,
            $urlHash,
            $extension
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
}
