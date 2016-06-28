<?php
function uploadFile($f) {
	global $user;
	
	// Check file types
	//$validTypes = Array("png","gif","jpg","jpeg","tiff","tif","fpx","jif","jfif","pdf","doc","docx","wav","mp3","mp4","ogg","ogv");
	//$type = substr($f["name"],strrpos($f["name"], ".")+1);
	//if(!in_array($type, $validTypes)) return -1;
	
	$validMime = Array(
		"application/pdf", // .pdf
		"application/vnd.openxmlformats-officedocument.wordprocessingml.document", //.doc .docx
		"application/ogg", // .ogg
		"audio/mp4",  // .mp4
		"audio/mpeg", // .mp3
		"audio/ogg",  // .ogg
		"audio/webm", // .webm
		"image/gif",  // .gif
		"image/jpeg", // .jpg .jpeg
		"image/pjpeg",// .pjpeg .pjpg
		"image/png",  // .png
		"video/mpeg", // .mpeg
		"video/mp4",  // .mp4
		"video/ogg",  // .ogv
		"video/quicktime", // .mov
		"video/webm", // .webm
		"application/vnd.oasis.opendocument.text" // .odt
	);
	if(!in_array($f["type"], $validMime)) return -1;
	
	// Check that there were no errors
	if ($f["error"] > 0) return $f["error"];
	
	// Generate the name for the file and move it
	$type = substr($f["name"],strrpos($f["name"], ".")+1);
	$name = $user->get("id")."_".date("mdyHis")."_".substr(md5($f["name"]),rand(0, 5),rand(15,17)).'.'.$type;
	$i == 0;
	while(file_exists('uploads/'.$name)) {
		$name = $user->get("id")."_".date("mdyHis")."_".substr(md5($f["name"].rand(0,100)),rand(0, 5),rand(15,17)).'.'.$type;
	}
	move_uploaded_file($f["tmp_name"],'uploads/'.$name);
	$name = addWaterMark('uploads/'.$name, str_replace(".{$type}","",$f["name"]));

	return $name;
}

function addWaterMark($data,$name="Screen Capture 1") {
	global $user;

	if(strpos($data,"data:image/")===FALSE && !file_exists($data)) return $data;
	$type = (strpos($data,"data:image/")===FALSE)?substr($data, strrpos($data,".")+1):"string";
	$img = null;

	switch($type) {
	case "gif":
		$img = imagecreatefromgif($data);
		break;

	case "jpeg":
	case "jpg":
	case "pjpeg":
	case "pjpg":
		$img = imagecreatefromjpeg($data);
		break;

	case "png":
		$img = imagecreatefrompng($data);
		break;

	case "string":
		$data2 = base64_decode(substr($data, strpos($data,",")+1));
		$img = imagecreatefromstring($data2);
		break;

	default:
		return $data;
	}

	//$out = imagecreatetruecolor(imagesx($img),imagesy($img));
	//imagecopy($out,$img,0,0,0,0,imagesx($img),imagesy($img));
	$size = (imagesy($img) < 500)?16:32;
	$w = ($size==16)?10:20;
	imagettftext($img, $size, 0, $w, imagesy($img)-$w, imagecolorallocate($img, 255, 255, 255), "source/arial.ttf", date("m-d-Y"));
	imagettftext($img, $size, 0, $w+1, imagesy($img)-$w-1, imagecolorallocate($img, 0,0,0), "source/arial.ttf", date("m-d-Y"));

	if($type == "string") {
		$type = substr($data, 11, strpos($data, ";")-11);
		$name = "Screen Capture 1";
		$data = 'uploads/'.$user->get("id")."_".date("mdyHis")."_".substr(md5($data),rand(0, 5),rand(15,17)).'.'.$type;
	}

	switch($type) {
	case "gif":
		imagegif($img,$data);
		break;

	case "jpeg":
	case "jpg":
	case "pjpeg":
	case "pjpg":
		imagejpeg($img,$data);
		break;

	case "png":
		imagepng($img,$data);
		break;

	default:
		return $data;
	}
	
	//imagedestroy($out);
	imagedestroy($img);
	return $data."|".$name;
}

function getAttachments() {
	// Delete any previously uploaded files that are now marked to delete
	if(is_array($_POST["deleteAttachment"]))
		deleteAttachments($_POST["deleteAttachment"]);

	// Get previously uploaded files if they exist
	$attachments = Array();
	foreach($_POST["attachments"] as $a) {
		$attachments = $a;
	}
	
	// Upload all files
	foreach($_FILES as $file) {
		$test = uploadFile($file);
		switch($test) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			$error .= "Sorry, but the file \"{$file["name"]}\" is too large to upload. The max upload size is ".ini_get("upload_max_filesize").".<br />";
			break;

		case UPLOAD_ERR_PARTIAL:
		case UPLOAD_ERR_NO_FILE:
			$error .= "Part of \"{$file["name"]}\" was not able to upload. Please try again.<br />";
			break;

		case UPLOAD_ERR_NO_TMP_DIR:
		case UPLOAD_ERR_CANT_WRITE:
		case UPLOAD_ERR_EXTENSION:
			$error .= "The system has encoutered an error while uploadin the file \"{$file["name"]}\". Please try again.<br />";
			break;
			
		case -1:
			$type = substr($file["name"], strrpos($file["name"], ".")+1);
			$error .= "The file \"{$file["name"]}\" is a \"{$type}\" file and is not allowed. You are only allowed to upload pictures, movies, sounds, Word Documents (.doc, .docx, .odt) and PDF files.<br />";
			break;

		default:
			$attachments[] = $test;
		}
	}
	
	// Save the screen shot, if added
	if($_POST["screenShot"] != "")
		$attachments[] = addWaterMark($_POST["screenShot"]);
		
	// Split and remove upload path, so it is only the file name
	foreach($attachments as $k=>$a) {
		//$attachments[$k] = explode("|",str_replace("uploads/", "", $a));
		$attachments[$k] = str_replace("uploads/", "", $a);
	}

	return $attachments;
}

function deleteAttachments($attachmentList) {
	foreach($attachmentList as $a) {
		if(file_exists('uploads/'.$a))
			unlink('uploads/'.$a);
	}
}
?>