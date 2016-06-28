<?php
/**
 * Timesules very basic Captcha (for now).
 * 
 * @author Tyler Hadidon
 * @copyright 2012
 */

$width = 400; // Image width
$height = 60; // Image height
$length = 5; // Number of characters in the captcha
$str = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789"; // Characters the captcha pulls from
$font = "font0.ttf"; // Font file to use for the characters

$dots = 500; // Number of dots to draw
$dotSize = 1; // The wdith&height of each dot
$dotColor = Array(50,50,50); // Dot color RGB

$lines = 7; // Number of lines to draw
$lineThickness = 2; // Thickness of the lines
$lineColor = Array(150,150,150); // Color of the lines RGB

// ========================================================================
// Start Captcha Code
// ========================================================================

// We only need VERY BASIC session information
session_start();

// Create new image with black background
$img = imagecreate($width, $height);
$bg = imagecolorallocate($img, 0, 0, 0);
imagefill($img, 0, 0, $bg);

// Generate lines
$lineColor = imagecolorallocate($img, $lineColor[0], $lineColor[1], $lineColor[2]);
imagesetthickness($img, $lineThickness);
$angles = Array(90,280);
for($i=0;$i<$lines;$i++) {
	imagearc($img, rand(0,$width), rand(0,$height), rand(100,300), rand(90,200), $angles[rand(0,1)], $angles[rand(0,1)], $lineColor);
}

// Get the root directory for the font file
$self = $_SERVER['PHP_SELF'];
$basename = basename($self);
$path = str_replace($basename, "", $self);
$root = "./".preg_replace("|[^/]*/|", "../", substr($path, 1));
$font = $root."source/".$font;

// Generate message
$captcha = "";
$failsafe = 0;
for($i=0;$i<$length;$i++) {
	$char = $str[rand(0, strlen($str)-1)];
	
	// If the character already exists, skip it, unless we have seen it more than 5 times
	if(strpos($captcha,$char) !== FALSE && $failsafe < 5) {
		$i--;
		$failsafe++;
		continue;
	}
	$failsafe = 0;
	
	// Add the character to the captcha code
	$captcha .= $char;
	
	// Print out the single character
	$size = rand(45,55);
	$x = $i*70+25;
	$y = 60-((60-$size)/2)+5;
	imagettftext($img, 55, rand(0,15), $x, $y, imagecolorallocate($img, 255, 255, 255), $font, $char);
}
$_SESSION["LAST_CAPTCHA"] = $captcha; // Add the captcha to the session data

// Generate dots
$dotColor = imagecolorallocate($img, $dotColor[0], $dotColor[1], $dotColor[2]);
for($i=0;$i<$dots;$i++) {
	$coor = Array(rand(0,$width-$dotSize),rand(0,$height-$dotSize));
	imagefilledrectangle($img, $coor[0], $coor[1], $coor[0]+$dotSize, $coor[1]+$dotSize, $dotColor);
}

// Print out generated image
header("Content-Type: image/png");
imagepng($img);
imagedestroy($img);
?>