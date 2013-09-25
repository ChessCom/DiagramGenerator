<?php

namespace DiagramGenerator;

use DiagramGenerator\Fen\Piece;
use DiagramGenerator\Fen\Bishop;
use DiagramGenerator\Fen\King;
use DiagramGenerator\Fen\Knight;
use DiagramGenerator\Fen\Pawn;
use DiagramGenerator\Fen\Queen;
use DiagramGenerator\Fen\Rook;

/**
 * TODO: probably should be a part of chess-game library
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class Fen
{
    const DEFAULT_FEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    /**
     * Array of all board pieces, excluding empty
     * @var array
     */
    protected $pieces = array();

    /**
     * Creates Fen object from the string
     * @param  string  $fenString
     * @param  boolean $silent
     * @return self
     */
    public static function createFromString($fenString, $silent = false)
    {
        $fen  = new Fen();
        $rows = explode('/', self::sanitizeFenString($fenString));
        foreach ($rows as $index => $rowString) {
            $row = array();

            foreach (str_split($rowString) as $pieceKey) {
                if (!is_numeric($pieceKey)) {
                    $row[] = $pieceKey;
                } else {
                    $emptyPieces = array_fill(count($row), $pieceKey, null);
                    $row         = array_merge($row, $emptyPieces);
                }
            }

            $fen->setRow($row, $index, $silent);
        }

        return $fen;
    }

    /**
     * Returns piece by piece key or null if its empty piece
     * @param  string  $key
     * @param  boolean $silent
     * @return mixed
     */
    public static function getPieceByKey($key, $silent = false)
    {
        $color = 'white';
        if (preg_match("/[A-Z]/", $key) === 0) {
            $color = 'black';
        }

        switch (strtolower($key)) {
            case null:
                return $key;
            case 'r':
                return new Rook($color);
            case 'b':
                return new Bishop($color);
            case 'n':
                return new Knight($color);
            case 'k':
                return new King($color);
            case 'q':
                return new Queen($color);
            case 'p':
                return new Pawn($color);
            default:
                if (!$silent) {
                    throw new \InvalidArgumentException(sprintf("Piece with key %s doesn\'t exist", $key));
                }
                null;
        }
    }

    /**
     * @param  string $fen
     * @return string
     */
    public static function sanitizeFenString($fen)
    {
        return (strpos($fen, ' ') === false) ? $fen : substr($fen, 0, strpos($fen, ' '));
    }

    /**
     * Gets the Fen pieces.
     *
     * @return array
     */
    public function getPieces()
    {
        return $this->pieces;
    }

    /**
     * Fills Fen row with values
     * @param array   $row
     * @param boolean $silent
     * @param integer
     */
    public function setRow(array $row, $rowIndex, $silent = false)
    {
        foreach ($row as $columnIndex => $value) {
            $this->setAtPosition($rowIndex, $columnIndex, self::getPieceByKey($value, $silent));
        }
    }

    /**
     * Set piece at position
     * @param integer                     $row
     * @param integer                     $column
     * @param \DiagramGenerator\Fen\Piece $piece
     */
    public function setAtPosition($row, $column, Piece $piece = null)
    {
        if ($piece) {
            $piece
                ->setRow($row)
                ->setColumn($column)
            ;
            $this->pieces[sprintf("%u:%u", $row, $column)] = $piece;
        }
    }
}
