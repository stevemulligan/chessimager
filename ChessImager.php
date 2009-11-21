<?php

require_once("./ChessImagerUtils.php");

$direction = getBoardDirection();
$board = makeBoardImage($direction);

$pieceArray = parseFenString($_GET['fen']);

for ($square = 0; $square < 64; $square++) {
  mergePiece($board, $pieceArray[$square], $square, $direction);
}

sendImage($board);
?>