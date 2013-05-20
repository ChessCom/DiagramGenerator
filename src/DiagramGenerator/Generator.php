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

        // $board = $sizeConfig->getCell() * 8;
        // $board_x = ($sizeConfig->getWidth() - $board) / 2;

        // $caption_base = $sizeConfig->getCaption()->getSize() - $sizeConfig->getCoordinates()->getSize() / 2;

        // if (!$config->getCaption()) {
        //     $board_y = $board_x = $sizeConfig->getFrameThick() + $sizeConfig->getOutlineThick();
        // } else {
        //     $board_y += $sizeConfig->getCoordinates()->getSize() / 2;
        // }

        // if (!$config->getCaption()) {
        //     $height = $width = $board + 2 * ($sizeConfig->getFrameThick() + $sizeConfig->getOutlineThick());
        // }

        // putenv('GDFONTPATH=' . realpath($FONTS_PATH));
        // var_dump($sizeConfig, $weight, $height);exit;
        $board  = new Board($config);
        $board->drawBoard();
        $board->drawBorder();
        header('Content-Type: image/jpeg');
        echo $board->getBoard();exit;

        header('Content-Type: image/jpeg');
        echo $board;exit;

        $diagram = new Diagram();
        $diagram->setBoard($board->draw());
        $diagram->draw($config, $sizeConfig, $themeConfig);
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

        $diagram->createBackgroud($image);
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

        $diagram->createCells($image);

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

        $fig = array();
        $alpha_allocated = false;
        $left = 0;
        $base = 0;
        for ($i = 0; $i < 12; $i++) {
            $file = $piecesPath . "/" . $pieces_font . "-" . (string)$cell . "-" . $FILES[$i] . ".png";
            $fig[$i] = @imagecreatefrompng($file);
            $docache = ($fig[$i] == NULL);
            if (!$docache) {
                continue;
            }

            $fig[$i] = imagecreatetruecolor($cell, $cell);
            imagesavealpha($fig[$i], true);

            if (!$alpha_allocated) {
                $clear = array();
                for ($t = 0; $t < 128; $t++) {
                    $clear[$t] = imagecolorallocatealpha($image, 0, 0, 0, $t);
                }
                $transparent = array();
                for ($t = 0; $t < 256; $t++) {
                    $transparent[$t] = imagecolorallocatealpha($image, $t, $t, $t, $t / 2);
                }

                $alpha_allocated = true;
                $black = imagecolorallocate($image, 0, 0, 0);
                $white = imagecolorallocate($image, 255, 255, 255);

                if (!$chess_size) {
                    for ($chess_size = 1; $chess_size < $cell ; $chess_size += 1) {
                        $box = imagettfbbox($chess_size, 0, $pieces_font, chr(43));
                        $w = $box[2] - $box[0] + 1;
                        if ($w >= $cell) {
                            break;
                        }
                    }
                }

                $box = imagettftext($fig[$i], $chess_size, 0, $left, $base, $black, $pieces_font, chr(43));
                $dx = ($cell - ($box[2] - $box[0] + 1)) / 2;
                $dy = ($cell - ($box[1] - $box[7] + 1)) / 2;
                $left = $pieces_left - $box[0] + $dx;
                $base = $pieces_base - $box[7] + $dy;
                $box = imagettftext($fig[$i], $chess_size, 0, $left, $base, $black, $pieces_font, chr(43));
            }

            imagefilledrectangle($fig[$i], 0, 0, $cell - 1, $cell - 1, $white);
            imagettftext($fig[$i], $chess_size, 0, $left, $base, $black, $pieces_font, $pieces_figures[$i]);
            imagealphablending($fig[$i], false);

            $qx = array(0);
            $qy = array(0);
            $sz = 1;
            imagesetpixel($fig[$i], 0, 0, $transparent[255]);
            for ($q = 0; $q < $sz; $q++) {
                $x = $qx[$q];
                $y = $qy[$q];
                $rgb = imagecolorat($fig[$i], $x, $y);
                $b = $rgb & 0xFF;
                for ($dx = -1; $dx <= 1; $dx++) {
                    for ($dy = -1; $dy <= 1; $dy++) {
                        $xo = $x + $dx;
                        $yo = $y + $dy;
                        if ($xo < 0 || $xo >= $cell || $yo < 0 || $yo >= $cell || ($dx == 0 && $dy == 0)) continue;

                        $rgbo = imagecolorat($fig[$i], $xo, $yo);
                        $bo = $rgbo & 0xFF;
                        if ($rgbo <= 0xffffff && $bo <= $b && $bo > 0) {
                            imagesetpixel($fig[$i], $xo, $yo, $transparent[$bo]);
                            $qx[$sz] = $xo;
                            $qy[$sz] = $yo;
                            $sz++;
                        }
                    }
                }
            }

            for ($x = 0; $x < $cell; $x++) {
                for ($y = 0; $y < $cell; $y++) {
                    $rgb = imagecolorat($fig[$i], $x, $y);
                    $t = ($rgb >> 24) & 0xFF;
                    if ($t) imagesetpixel($fig[$i], $x, $y, $clear[$t]);
                }
            }

            imagepng($fig[$i], $file);
        }

        $position = array(8);
        $off = 0;
        $enough = false;
        for ($y = 0; $y < 8; $y++) {
            $position[$y] = array(8);
            $pos = strpos($fen, "/", $off);
            if ($pos === FALSE) {
                $pos = strpos($fen, " ", $off);
                if ($pos === FALSE) break;

                $enough = true;
            }

            for ($i = 0, $x = 0; $i < ($pos - $off) && $x < 8; $i++) {
                switch ($fen[$off + $i]) {
                default: break;

                case 'r': $position[$y][$x++] = $pieces_figures[0]; break;
                case 'n': $position[$y][$x++] = $pieces_figures[1]; break;
                case 'b': $position[$y][$x++] = $pieces_figures[2]; break;
                case 'q': $position[$y][$x++] = $pieces_figures[3]; break;
                case 'k': $position[$y][$x++] = $pieces_figures[4]; break;
                case 'p': $position[$y][$x++] = $pieces_figures[5]; break;

                case 'R': $position[$y][$x++] = $pieces_figures[6]; break;
                case 'N': $position[$y][$x++] = $pieces_figures[7]; break;
                case 'B': $position[$y][$x++] = $pieces_figures[8]; break;
                case 'Q': $position[$y][$x++] = $pieces_figures[9]; break;
                case 'K': $position[$y][$x++] = $pieces_figures[10]; break;
                case 'P': $position[$y][$x++] = $pieces_figures[11]; break;

                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                    $position[$y][$x++] = ' ';
                    $c = $fen[$off + $i];
                    for ($s = 1; $s < (int)$c; $s++) {
                        $n = (int)$c;
                        $position[$y][$x++] = ' ';
                    }
                    break;
                }
            }

            $off = $pos + 1;
            if ($enough) break;
        }

        for ($x = 0; $x < 8; $x++) {
            for ($y = 0; $y < 8; $y++) {
                if ($bottom == 0) $d = 7 - $y; else $d = $y;
                $f = strpos($pieces_figures, $position[$d][$x]);
                if ($f === FALSE) continue;
                imagecopy($image, $fig[$f],
                    $board_x + $cell * $x, $board_y + $cell * $y,
                    0, 0, $cell, $cell);
            }
        }

        imagepng($image);
        imagedestroy($image);
    }
}
