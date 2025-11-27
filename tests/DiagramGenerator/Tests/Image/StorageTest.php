<?php

namespace DiagramGenerator\Tests\Image;

use DiagramGenerator\Config;
use DiagramGenerator\Config\Size;
use DiagramGenerator\Config\Theme;
use DiagramGenerator\Fen;
use DiagramGenerator\Fen\Pawn;
use DiagramGenerator\Image\Storage;
use DiagramGenerator\Image\StorageNew;
use PHPUnit\Framework\TestCase;

/**
 * StorageTest
 */
class StorageTest extends TestCase
{
    /** @var string */
    protected $cacheDirectory;

    /** @var string */
    protected $pieceThemeUrl;

    /** @var string */
    protected $boardTextureUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheDirectory = sys_get_temp_dir() . '/diagram_generator_test_' . uniqid();
        $this->pieceThemeUrl = '/pieces/__PIECE_THEME__/__SIZE__/__PIECE__';
        $this->boardTextureUrl = '/boards/__BOARD_TEXTURE__/__SIZE__';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_dir($this->cacheDirectory)) {
            $this->removeDirectory($this->cacheDirectory);
        }
    }

    public function testStorageNewGetPieceImageWithThemeUrls()
    {
        $config = $this->createConfigWithThemeUrls();
        $storage = $this->createStorageNew($config);
        $piece = new Pawn('white');
        $piece->setRow(1)->setColumn(0);

        // Storage should use theme URLs
        // It will fail when trying to download/load the image, but that's expected
        $this->expectException(\Intervention\Image\Exception\NotReadableException::class);

        $storage->getPieceImage($piece, $config);
    }

    public function testStorageNewGetPieceImageThrowsExceptionWhenPieceUrlMissing()
    {
        $config = $this->createConfigWithPartialThemeUrls();
        $storage = $this->createStorageNew($config);
        
        // Create a piece that doesn't have a URL in themeUrls
        $pieceWithoutUrl = new \DiagramGenerator\Fen\King('white');
        $pieceWithoutUrl->setRow(0)->setColumn(4);

        // Storage should throw exception when piece URL is missing
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Piece URL not found in theme for piece: wk');
        
        $storage->getPieceImage($pieceWithoutUrl, $config);
    }

    public function testStorageGetPieceImage()
    {
        $config = $this->createConfigWithoutThemeUrls();
        
        // Need to set theme for legacy method to work
        $theme = new Theme();
        $theme->setName('test');
        $config->setTheme($theme);
        
        $storage = $this->createStorage();
        $piece = new Pawn('white');
        $piece->setRow(1)->setColumn(0);

        // This should call the legacy method
        // It will fail when trying to load from cache, but that's expected
        $this->expectException(\Intervention\Image\Exception\NotReadableException::class);

        $storage->getPieceImage($piece, $config);
    }

    public function testStorageNewGetBackgroundTextureImageWithThemeUrls()
    {
        $config = $this->createConfigWithThemeUrls();
        $storage = $this->createStorageNew($config);

        // Should try to load from theme URLs
        // It will fail when trying to download/load the image, but that's expected
        $this->expectException(\Intervention\Image\Exception\NotReadableException::class);

        $storage->getBackgroundTextureImage($config);
    }

    public function testStorageNewGetBackgroundTextureImageReturnsNullWhenBoardUrlMissing()
    {
        $config = $this->createConfigWithPartialThemeUrls();
        // Remove board URL
        $config->setThemeUrls(['wp' => 'https://example.com/wp.png']);
        $storage = $this->createStorageNew($config);

        // Should return null when board URL is not in themeUrls
        $result = $storage->getBackgroundTextureImage($config);
        $this->assertNull($result);
    }

    public function testStorageGetBackgroundTextureImageReturnsNullWhenNoTexture()
    {
        $config = $this->createConfigWithoutThemeUrls();
        $storage = $this->createStorage();

        // Should return null when no texture is set
        $result = $storage->getBackgroundTextureImage($config);
        $this->assertNull($result);
    }

    public function testGetCachedPieceFilePathFromTheme()
    {
        $config = $this->createConfigWithThemeUrls();
        $storage = $this->createStorageNew($config);
        $pieceUrl = 'https://example.com/pieces/wp.png';
        $piece = 'wp';

        $reflection = new \ReflectionClass($storage);
        $method = $reflection->getMethod('getCachedPieceFilePathFromTheme');
        $method->setAccessible(true);

        $path = $method->invoke($storage, $pieceUrl, $piece);
        
        $urlHash = md5($pieceUrl);
        $expectedPath = sprintf(
            '%s/theme/%s/%s.png',
            $this->cacheDirectory,
            $urlHash,
            $piece
        );

        $this->assertEquals($expectedPath, $path);
    }

    public function testGetCachedPieceFilePathFromThemeWithCustomExtension()
    {
        $config = $this->createConfigWithThemeUrls();
        $storage = $this->createStorageNew($config);
        $pieceUrl = 'https://example.com/pieces/wp.jpg';
        $piece = 'wp';

        $reflection = new \ReflectionClass($storage);
        $method = $reflection->getMethod('getCachedPieceFilePathFromTheme');
        $method->setAccessible(true);

        $path = $method->invoke($storage, $pieceUrl, $piece);
        
        $urlHash = md5($pieceUrl);
        $expectedPath = sprintf(
            '%s/theme/%s/%s.jpg',
            $this->cacheDirectory,
            $urlHash,
            $piece
        );

        $this->assertEquals($expectedPath, $path);
    }

    public function testGetCachedTextureFilePathFromTheme()
    {
        $config = $this->createConfigWithThemeUrls();
        $storage = $this->createStorageNew($config);
        $boardUrl = 'https://example.com/boards/board.png';

        $reflection = new \ReflectionClass($storage);
        $method = $reflection->getMethod('getCachedTextureFilePathFromTheme');
        $method->setAccessible(true);

        $path = $method->invoke($storage, $boardUrl);
        
        $urlHash = md5($boardUrl);
        $expectedPath = sprintf(
            '%s/board/theme/%s.png',
            $this->cacheDirectory,
            $urlHash
        );

        $this->assertEquals($expectedPath, $path);
    }

    public function testGetCachedTextureFilePathFromThemeWithCustomExtension()
    {
        $config = $this->createConfigWithThemeUrls();
        $storage = $this->createStorageNew($config);
        $boardUrl = 'https://example.com/boards/board.jpg';

        $reflection = new \ReflectionClass($storage);
        $method = $reflection->getMethod('getCachedTextureFilePathFromTheme');
        $method->setAccessible(true);

        $path = $method->invoke($storage, $boardUrl);
        
        $urlHash = md5($boardUrl);
        $expectedPath = sprintf(
            '%s/board/theme/%s.jpg',
            $this->cacheDirectory,
            $urlHash
        );

        $this->assertEquals($expectedPath, $path);
    }

    protected function createStorage()
    {
        return new Storage($this->cacheDirectory, $this->pieceThemeUrl, $this->boardTextureUrl);
    }

    protected function createStorageNew(Config $config)
    {
        return new StorageNew($this->cacheDirectory);
    }

    protected function createConfigWithThemeUrls()
    {
        $config = new Config();
        $config->setFen('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR');
        $config->setSizeIndex('100px');
        
        $size = new Size();
        $size->setCell(100);
        $config->setSize($size);

        $theme = new Theme();
        $theme->setName('test');
        $config->setTheme($theme);

        $config->setThemeUrls([
            'board' => 'https://example.com/board.png',
            'wp' => 'https://example.com/wp.png',
        ]);

        return $config;
    }

    protected function createConfigWithoutThemeUrls()
    {
        $config = new Config();
        $config->setFen('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR');
        $config->setSizeIndex('100px');
        
        $size = new Size();
        $size->setCell(100);
        $config->setSize($size);

        return $config;
    }

    protected function createConfigWithPartialThemeUrls()
    {
        $config = new Config();
        $config->setFen('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR');
        $config->setSizeIndex('100px');
        
        $size = new Size();
        $size->setCell(100);
        $config->setSize($size);

        $theme = new Theme();
        $theme->setName('test');
        $config->setTheme($theme);

        // Only provide URLs for some pieces, not all
        $config->setThemeUrls([
            'board' => 'https://example.com/board.png',
            'wp' => 'https://example.com/wp.png',
            'bp' => 'https://example.com/bp.png',
            // Intentionally missing 'wk', 'bk', etc.
        ]);

        return $config;
    }

    protected function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }
}

