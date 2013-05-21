<?php

namespace DiagramGenerator;

use DiagramGenerator\GeneratorConfig as Config;
use DiagramGenerator\ConfigLoader;
use DiagramGenerator\Diagram\Board;
// use DiagramGenerator\Dimensions;

/**
 * Generator class
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Generator
{
    public function __construct()
    {
        $this->configLoader = new ConfigLoader();
    }

    public static function getResourcesDir()
    {
        return __DIR__.'/Resources';
    }

    public function buildDiagram(Config $config)
    {
        $themes = $this->configLoader->getThemes();
        $sizes  = $this->configLoader->getSizes();

        if (!array_key_exists($config->getThemeIndex(), $themes)) {
            throw new InvalidConfigException(sprintf("Theme %s doesn't exist", $config->getTheme()));
        }

        if (!array_key_exists($config->getSizeIndex(), $sizes)) {
            throw new InvalidConfigException(sprintf("Size %s doesn't exist", $config->getSize()));
        }

        $config->setTheme($themes[$config->getThemeIndex()]);
        $config->setSize($sizes[$config->getSizeIndex()]);

        $diagram = new Diagram($config);
        $board   = new Board($config);
        $board
            ->drawBoard()
            ->drawCells()
            ->drawFigures()
            ->drawBorder()
            ->draw()
        ;
        $diagram->setBoard($board);
        $diagram->draw();

        header('Content-Type: image/jpeg');
        echo $diagram->getImage();
        exit;

        // $image = imagecreatetruecolor($dimensions['width'], $dimensions['height']);

        // $caption = str_replace("\'", "'", $caption);
        // $caption = str_replace('\"', '"', $caption);

        # TODO: create caption
        // $caption = $config->getCaption();
        // $box = imagettfbbox($caption_size, 0, $CAPTION_FONT, $caption);
        // $w = $box[2] - $box[0];
        // if ($w > $board) {
        //     $cpl = strlen($caption) / $w * $board;
        //     $new_caption = wordwrap($caption, $cpl, "\n", true);
        //     $box = imagettfbbox($caption_size, 0, $CAPTION_FONT, $new_caption);
        //     $w = $box[2] - $box[0];
        //     if ($w > $board) {
        //         $cpl = $cpl / $w * $board;
        //         $new_caption = wordwrap($caption, $cpl, "\n", true);
        //         $box = imagettfbbox($caption_size, 0, $CAPTION_FONT, $new_caption);
        //         $w = $box[2] - $box[0];
        //     }

        //     $caption = $new_caption;
        //     $left = $board_x + $board / 2 - $w / 2;
        //     $box = imagettftext($image, $caption_size, 0, $left, $caption_base, $caption_color, $CAPTION_FONT, $caption);
        //     if ($box[1] >= $height) {
        //         imagedestroy($image);
        //         $height = $box[1] + $caption_size / 2;
        //         $image = imagecreatetruecolor($width, $height);
        //     }
        // }

        // $background_color   = imagecolorallocate($image, $BACKGROUND_COLOR[0],  $BACKGROUND_COLOR[1],   $BACKGROUND_COLOR[2]);
        // $light_color        = imagecolorallocate($image, $light[0],             $light[1],              $light[2]);
        // $dark_color         = imagecolorallocate($image, $dark[0],              $dark[1],               $dark[2]);
        // $coordinates_color  = imagecolorallocate($image, $COORDINATES_COLOR[0], $COORDINATES_COLOR[1],  $COORDINATES_COLOR[2]);
        // $caption_color      = imagecolorallocate($image, $CAPTION_COLOR[0],     $CAPTION_COLOR[1],      $CAPTION_COLOR[2]);
        // $outline_color      = imagecolorallocate($image, $OUTLINE_COLOR[0],     $OUTLINE_COLOR[1],      $OUTLINE_COLOR[2]);
        // $frame_color        = imagecolorallocate($image, $FRAME_COLOR[0],       $FRAME_COLOR[1],        $FRAME_COLOR[2]);

        // exit;
        // imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $background_color);
        // imagefilledrectangle($image, $board_x - $outline_thick - $frame_thick, $board_y - $outline_thick - $frame_thick,
        //     $board_x + $board - 1 + $outline_thick  + $frame_thick, $board_y + $board - 1 + $outline_thick + $frame_thick, $frame_color);
        // imagefilledrectangle($image, $board_x - $outline_thick, $board_y - $outline_thick,
        //     $board_x + $board - 1 + $outline_thick, $board_y + $board - 1 + $outline_thick, $outline_color);

        // $diagonal = 1;
        // $bw = array($dark_color, $light_color);
        // for ($x = 0; $x < $board; $x += $cell, $diagonal++) {
        //     for ($y = 0; $y < $board; $y += $cell, $diagonal++) {
        //         imagefilledrectangle($image,
        //             $board_x + $x, $board_y + $y,
        //             $board_x + $x + $cell - 1, $board_y + $y + $cell - 1,
        //             $bw[$diagonal & 1]);
        //     }
        // }


        // if ($coordinates) {
        //     for ($y = 0, $count = 0; $y < $board; $y += $cell, $count++) {
        //         $box = imagettfbbox($coordinates_size, 0, $COORDINATES_FONT, $NUMBERS[$bottom][$count]);
        //         $h = $box[1] - $box[7];

        //         $left = $board_x - $coordinates_left;
        //         $base = $board_y  + $y  + $cell / 2 + $h / 2;
        //         imagettftext($image, $coordinates_size, 0, $left, $base, $coordinates_color, $COORDINATES_FONT, $NUMBERS[$bottom][$count]);
        //     }

        //     for ($x = 0, $count = 0; $x < $board; $x += $cell, $count++) {
        //         $box = imagettfbbox($coordinates_size, 0, $COORDINATES_FONT, $LETTERS[$bottom][$count]);
        //         $w = $box[2] - $box[0];

        //         $left = $board_x + $x + $cell / 2 - $w / 2;
        //         $base = $board_y  + $board + $coordinates_size + $coordinates_base;
        //         imagettftext($image, $coordinates_size, 0, $left, $base, $coordinates_color, $COORDINATES_FONT, $LETTERS[$bottom][$count]);
        //     }
        // }

        $diagram->createCaption($image);

        // $box = imagettfbbox($caption_size, 0, $CAPTION_FONT, $caption);
        // $w = $box[2] - $box[0];
        // $left = $board_x + $board / 2 - $w / 2;
        // imagettftext($image, $caption_size, 0, $left, $caption_base, $caption_color, $CAPTION_FONT, $caption);
    }
}
