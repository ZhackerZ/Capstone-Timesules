<?php
$file = $_GET["f"];
$size = strtolower($_GET["s"]);

$width = 64;
$height = 64;

if($size != "" && strpos($size, "x") !== FALSE) {
	$width = substr($size, 0, strpos($size, "x"));
	$height = substr($size, strpos($size, "x")+1);
} else if($size != "") {
	$width = $size;
	$height = $size;
}

if($file == "" || !file_exists($file)) {
	thumbFail();
}

$type = substr($file, strrpos($file,".")+1);
$img = null;

switch($type) {
case "gif":
	$img = imagecreatefromgif($file);
	break;

case "jpeg":
case "jpg":
case "pjpeg":
case "pjpg":
	$img = imagecreatefromjpeg($file);
	break;

case "png":
	$img = imagecreatefrompng($file);
	break;

default:
	thumbFail();
}

$src_w = imagesx($img);
$src_h = imagesy($img);

if($src_w > $width || $src_h > $height) {
	$sw = ($src_w>$width)?$width/$src_w:1;
	$sh = ($src_h>$height)?$height/$src_h:1;
	$s = ($sw<$sh)?$sw:$sh;
	
	$width = $src_w*$s;
	$height = $src_h*$s;
} else {
	$width = $src_w;
	$height = $src_h;
}

$out = imagecreatetruecolor($width, $height);

//imagecopy($out, $img, 0, 0, 0, 0, $src_w, $src_h);
imagecopyresampled($out, $img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);

header("Content-Type: image/png");
imagepng($out);

imagedestroy($img);
imagedestroy($out);

function thumbFail() {
	global $width, $height;
	$img = imagecreatefrompng("source/templates/images/imageError.png");
	$out = imagecreatetruecolor($width, $height);
	
	imagecopy($out, $img, 0, 0, 0, 0, $width, $height);
	
	header("Content-Type: image/png");
	imagepng($out);
	
	imagedestroy($img);
	imagedestroy($out);
	exit;
}
?>