<?php

function sendErrorImageAndDie($cerr) {
  $new = imageCreate(600, 30);
  $bgc = imageColorAllocate($new,255,255,255);
  $tc  = imageColorAllocate($new,0,0,0);
  imageFilledRectangle($new,0,0,150,30,$bgc);
  imageString($new,5,5,5,"Error: $cerr", $tc);
  sendImage($new);
  die;
}

function sendImage($img) {
  if (! $img) {
    sendErrorImageAndDie("Invalid image object");
  }
  else {
    header("Content-type: image/png");
    imagePNG($img);
    imageDestroy($img);
  }
}

function loadPNG($image_name) {
  $im = imageCreateFromPNG($image_name);
  if (! $im) {
    sendErrorImageAndDie("Could not load piece image: $image_name");
  }
  return($im);
}

function parseColorString($str, &$red, &$green, &$blue) {
  preg_match("/\(?(\d+),(\d+),(\d+)\)?/", $str, $array);
  if (strlen($array[0]) > 0) {
    $red   = $array[1];
    $green = $array[2];
    $blue  = $array[3];
  }
}

function getColorFromUrl($str, $default_red, $default_green, $default_blue,
       &$red, &$green, &$blue) {
  $red = $default_red;
  $green = $default_green;
  $blue = $default_blue;
  parseColorString($_GET[$str], $red, $green, $blue);
}

function getDarkSquareColor($im) {
  getColorFromUrl('ds_color', 41, 161, 151, $red, $green, $blue);
  return imageColorAllocate($im, $red, $green, $blue);
}

function getLightSquareColor($im) {
  getColorFromUrl('ls_color', 255, 255, 240, $red, $green, $blue);
  return imageColorAllocate($im, $red, $green, $blue);
}

function getOutlineColor($im) {
  getColorFromUrl('border_color', 150, 150, 150, $red, $green, $blue);
  return imageColorAllocate($im, $red, $green, $blue);
}

function getBorderWidth() {
  $border_width_string = $_GET['border_width'];
  if (strlen($border_width_string) > 0) {
    return $border_width_string;
  }
  else {
    return 1;
  }
}

function isCoordinatesEnabled() {
  return(strcmp($_GET['coordinates'], "on") == 0);
}

function getCoordinateWidth() {
  if (isCoordinatesEnabled()) {
    $width = max(imageFontWidth(getCoordinateFont()), 
                 imageFontHeight(getCoordinateFont())) * 1.5;
  }
  else {
    $width = 0;
  }
  return($width);
}

function getDecorationWidth() {
  if (isCoordinatesEnabled()) {
    $width = getBorderWidth() + getCoordinateWidth() + 1;
  }
  else {
    $width = getBorderWidth();
  }

  return($width);
}

function getCoordinateFont() {
  if (1.5 * max(imageFontWidth(4), imageFontHeight(4)) <= getSquareSize()) {
    return(4);
  }
  else if (1.5 * max(imageFontWidth(2), imageFontHeight(2)) <= getSquareSize()) {
    return(2);
  }
  else {
    return(1);
  }    
}
 
function addCoordinates($im, $direction) {
  if (! isCoordinatesEnabled()) {
    return;
  }

  $decorationWidth = getDecorationWidth();
  $squareSize = getSquareSize();
  $font = getCoordinateFont();

  $x_left_numbers = ($decorationWidth - imageFontWidth($font)) / 2;
  $x_right_numbers = $x_left_numbers + 8 * $squareSize + $decorationWidth;
  $y1 = $decorationWidth + ($squareSize - imageFontHeight($font)) / 2;
  if ($direction == 'normal') {
    $deltaY = $squareSize;
  }
  else {
    $y1 = $y1 + (7 * $squareSize);
    $deltaY = -$squareSize;
  }

  $black = imageColorAllocate($im, 0, 0, 0);

  $y = $y1;
  for ($k = 8; $k >= 1; $k--) {
    imageString($im, $font, $x_left_numbers, $y, $k, $black);
    imageString($im, $font, $x_right_numbers, $y, $k, $black);
    $y += $deltaY;
  }

  $file = substr($files, $k - 1, 1);
  $x1 = $decorationWidth + ($squareSize - imageFontWidth($font)) / 2;
  $y_top_letters = ($decorationWidth - imageFontHeight($font)) / 2;
  $y_bottom_letters = $y_top_letters + 8 * $squareSize + $decorationWidth;

  if ($direction == 'normal') {
    $deltaX = $squareSize;
  }
  else {
    $x1 = $x1 + (7 * $squareSize);
    $deltaX = -$squareSize;
  }
  
  $files = 'abcdefgh';
  $x = $x1;
  for ($k = 0; $k < 8; $k++) {
    $file = substr($files, $k, 1);
    imageString($im, $font, $x, $y_top_letters, $file, $black);
    imageString($im, $font, $x, $y_bottom_letters, $file, $black);
    $x += $deltaX;
  }
}

function makeBoardImage($direction) {
  $squareSize = getSquareSize();
  $decorationWidth = getDecorationWidth();
  $coordinateWidth = getCoordinateWidth();
  $borderWidth = getBorderWidth();
  $numRows = 8 * $squareSize + 2 * $decorationWidth;
  $numCols = $numRows;

  $im = imageCreateTruecolor($numRows, $numCols);
  imageAlphaBlending($im, 1);

  $dark_square_color = getDarkSquareColor($im);
  $light_square_color = getLightSquareColor($im);
  $outline_color = getOutlineColor($im);
  $white = imageColorAllocate($im, 255, 255, 255);

  imageFilledRectangle($im, 0, 0, $numRows - 1, $numCols - 1, $outline_color);
  if (isCoordinatesEnabled()) {
    imageFilledRectangle($im, $borderWidth, $borderWidth, $numRows - $borderWidth - 1,
      $numCols - $borderWidth - 1, $white);
    imageFilledRectangle($im, $borderWidth + $coordinateWidth, 
      $borderWidth + $coordinateWidth,
      $numRows - $borderWidth - $coordinateWidth - 1, 
      $numCols - $borderWidth - $coordinateWidth - 1, $outline_color);
  }

  for ($rank = 0; $rank < 8; $rank++)
  {
      for ($file = 0; $file < 8; $file++)
      {
          $square_color = ($rank + $file) % 2 ? $dark_square_color : $light_square_color;
          $x1 = $file * $squareSize + $decorationWidth;
          $y1 = $rank * $squareSize + $decorationWidth;
          $x2 = $x1 + $squareSize - 1;
          $y2 = $y1 + $squareSize - 1;
          imageFilledRectangle($im, $x1, $y1, $x2, $y2, $square_color);
      }
  }

  addCoordinates($im, $direction);

  return($im);
}

function getSquareSize() {
  $square_size_str = $_GET['square_size'];
  if (strlen($square_size_str) == 0) {
    return(35);
  }
  else {
    return(min($square_size_str, 150));
  }
}

function parseFenString($str)
{
  $count = 0;
  for ($k = 0; $k < strlen($str); $k++) {
    $char = substr($str, $k, 1);
    if ($char == "/") {
      continue;
    }

    else if (ereg("[prnbqkPRNBQK]", $char)) {
      $out[$count++] = $char;
    }

    else if (ereg("[1-8]", $char)) {
      for ($c = 0; $c < $char; $c++) {
        $out[$count++] = " ";
      }
    }

    else {
      // Invalid FEN character; bail
      break;
    }

    if ($count >= 64) {
      // array is full; bail
      break;
    }
  }

  $out = array_pad($out, 64, " ");

  return $out;
}

function getPieceStyle() {
  $piece_style_str = $_GET['piece_style'];
  if (strlen($piece_style_str) == 0) {
    $piece_style_str = "merida";
  }
 
  return($piece_style_str);
}

function pieceFilename($piece)
{
  static $map = array( "p" => "black_pawn",
                       "r" => "black_rook",
                       "n" => "black_knight",
                       "b" => "black_bishop",
                       "q" => "black_queen",
                       "k" => "black_king",
                       "P" => "white_pawn",
                       "R" => "white_rook",
                       "N" => "white_knight",
                       "B" => "white_bishop",
                       "Q" => "white_queen",
                       "K" => "white_king"   );

  return "./pieces/" . getPieceStyle() . "/" . $map[$piece] . ".png";
}

function getBoardDirection() {
  $board_direction_str = $_GET['direction'];
  if (strlen($board_direction_str) == 0) {
    $board_direction_str = 'normal';
  }

  return($board_direction_str);
}

function mergePiece($board, $piece, $square, $direction) {
  if ($piece == " ") {
    return;
  }

  $file = $square % 8;
  $rank = ($square - $file) / 8;
  
  $numCols = imagesx($board);
  $squareSize = getSquareSize();
  $decorationWidth = ($numCols - 8 * $squareSize) / 2;

  if ($direction == 'normal') {
    $x = $decorationWidth + $file * $squareSize;
    $y = $decorationWidth + $rank * $squareSize;
  }
  else {
    $x = $decorationWidth + (7 - $file) * $squareSize;
    $y = $decorationWidth + (7 - $rank) * $squareSize;
  }

  $pieceImage = loadPNG(pieceFilename($piece));
  $pieceSize = imageSx($pieceImage);
  if (! imageCopyResampled($board, $pieceImage, $x, $y, 0, 0, $squareSize,
        $squareSize, $pieceSize, $pieceSize)) {
    sendErrorImageAndDie("imageCopy returned false");
  }

  imageDestroy($pieceImage);
}
?>