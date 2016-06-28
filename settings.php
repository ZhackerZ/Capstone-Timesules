<?php
/**
 * Timesules account settings.
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

$minAge = 13;

$manager->requireLogin();

// Update settings
if(!is_null($_POST["update"])) {
	$update = $_POST["update"];
	$pref = $_POST["pref"];
	unset($_POST);

	$update["email"] = trim($update["email"]);

	// First varify that all required field are given
	$error = "";
	if(!validateEmpty($update["first"])) $error .= "<span>First name</span> must be filled out.<br />";
	if(!validateEmpty($update["last"])) $error .= "<span>Last name</span> must be filled out.<br />";

	// Check age requirements
	$birthday = Array(
		"month"=>$update["bdayMonth"],
		"day"=>$update["bdayDate"],
		"year"=>$update["bdayYear"]
	);
	$age = date("Y")-$birthday["year"];
	if($age == $minAge && ($birthday["month"]>date("m") || ($birthday["month"]==date("m") && $birthday["day"]>date("d"))))
		$age = 0;

	if(!@checkdate($birthday["month"],$birthday["day"],$birthday["year"])) $error .= "Please enter your birthday.<br />";
	else if($age < $minAge)
		$error .= "<span>Too young</span> in compliance with the \"Children's Online Privacy Protection Act\".<br />";

	$update["bday"] = $birthday["year"]."-".$birthday["month"]."-".$birthday["day"];

	// Make sure gender is valid (PHP security rule #4, never trust anything until proven valid)
	if(filter_var($update["gender"], FILTER_VALIDATE_INT, Array('options'=>Array('min_range'=>0,'max_range'=>2))) === FALSE)
		$error .= "That <span>gender is not valid</span>. Please select a valid gender.<br />";

	// Check that the email is valid (for the most part, real validation comes later)
	$emailChanged = FALSE;
	$newEmail = "";
	if(!validateEmpty($update["email"])) $error .= "An <span>email address</span> is  required.<br />";
	else if(!filter_var($update["email"], FILTER_VALIDATE_EMAIL)) $error .= "That is an <span>invalid email address</span>.<br />";
	else if($update["email"] != $user->get("email")) {
		// Make sure this email does not already exist (if they changed it)
		$email = $sql->escape($update["email"]);
		$num = $sql->select("users", "user_email", "WHERE `user_email`='{$email}'");
		$check = $sql->fetch();

		if($check["user_email"]==$email || $num != 0)
			$error .= '<span>Email address already in use.</span> The same email address can not be used for two accounts.<br />';
		else {
			$emailChanged = TRUE;
			$newEmail = $email;
		}
	}

	// Check passwords
	$changingPassword = FALSE;
	$newPWHash = "";
	if($update["newPW"] != "" && $update["currentPW"] == "")
		$error .= 'To change your password, you must supply your current password.<br />';
	else if($update["newPW"] != "" && $update["currentPW"] != "" && $update["confPW"] != $update["newPW"])
		$error .= 'Your password confirmation did not match. Please enter your password twice for confirmation.<br />';
	else if($update["newPW"] != "" && $update["confPW"] == $update["newPW"] && $update["currentPW"] != "") {
		define('CHECK_PWD', TRUE);
		if($user->checkPasswordHash($update["currentPW"]) === TRUE) {
			define('GEN_PWD', TRUE);
			$newPWHash = $user->genPasswordHash($update["newPW"]);
			define('GEN_PWD', FALSE);
			$changingPassword = TRUE;
		} else
			$error .= "Your current password was incorrect. Please try again.<br />\n";
		define('CHECK_PWD', FALSE);
	}
	unset($update["newPW"]);
	unset($update["currentPW"]);
	unset($update["confPW"]);

	// Get the preferences
	$prefs = "";
	ksort($pref);
	//foreach($pref as $p) {
	for($i=0;$i<Manager::PREF_COUNT;$i++) {
		$prefs .= ($pref[$i]=="on")?'1':'0';
	}

	// See if we are uploading an avatar?
	$changingAvatar = FALSE;
	if($_FILES["avatarUL"]["error"] != 4) {
		$file = $_FILES["avatarUL"];
		$fer = $file["error"];
		$ftype = $file["type"];
		$fsize = $file["size"];
		$ftmp = $file["tmp_name"];
		$fname = $manager->getAvatarDir().substr(md5(time().$user->get("id")),3,15);
		$fext = ($ftype=="image/png")?".png":(($ftype=="image/gif")?".gif":".jpg");

		// Make sure the file does not exist...
		$catch = 0;
		while(file_exists($fname.$fext) && $catch < 2) {
			$fname = $manager->getAvatarDir().substr(md5(time().$user->get("id")),3,15);
			$catch++;
		}
		if($catch>=2)
			$fer = 502;

		// Check file type
		if($fer == 0 &&
		$ftype != "image/jpeg" && $ftype != "image/pjpeg" &&
		$ftype != "image/png" && $ftype != "image/gif")
			$fer = 500;

		// Check file size
		if($fer == 0 && $fsize > 30720)
			$fer = 1;

		// If there were no errors...
		if($fer == 0) {
			// Create a new GD image, scale, and save
			$img = NULL;
			switch($ftype) {
			case "image/png": $img = @imagecreatefrompng($ftmp); break;
			case "image/gif": $img = @imagecreatefromgif($ftmp); break;
			default: $img = @imagecreatefromjpeg($ftmp); break;
			}
			$save = imagecreatetruecolor(75, 75);
			$width = imagesx($img);
			$height = imagesy($img);
			imagefill($save, 0, 0, imagecolorallocate($save, 255, 255, 255));
			imagecopyresampled($save, $img, 0, 0, 0, 0, 75, 75, $width, $height);

			imagepng($save, $fname.'.png');
			imagedestroy($img);
			imagedestroy($save);

			// Delete the old avatar and get ready for update
			if($user->get("avatar") != "")
				@unlink($manager->getAvatarDir().$user->get("avatar"));
			$changingAvatar = str_replace($manager->getAvatarDir(),"",$fname.'.png');

		} else {
			switch($fer) {
			// Too big!
			case 1:
			case 2:
				$error .= "The avatar image you tried to upload was too big. Max size: 30KB.<br />";
				break;

			// The file was only partially uploaded
			case 3:
				$error .= "The avatar image was only partially uploaded. Please try again.<br />";
				break;

			// Bad file type
			case 500:
				$error .= "The avatar's filetype is not allowed. Please choose a JPEG, JPG, PNG or GIF image.<br />";
				break;

			// All other errors
			default:
				$error .= "A server error was encountered while uploading your avatar. Please try again.<br />";
				break;
			}
		}
	} else if($update["removeAvatar"] == "removeAvatar") {
		if($user->get("avatar") != "")
				@unlink($manager->getAvatarDir().$user->get("avatar"));
		$changingAvatar = "test";
	}

	// Now, I finally can update their profile by building a list of changes
	$success = "";
	if($error === "") {
		$upd = Array();
		$usr = $user->get("*");

		if($emailChanged === TRUE && $newEmail != "") $upd["user_email"] = $newEmail;
		if($changingPassword === TRUE && $newPWHash != "") $upd["user_password"] = $newPWHash;
		unset($newPWHash);

		if($changingAvatar !== FALSE) $upd["user_avatar"] = $changingAvatar;

		if($update["first"] != $usr["first"]) $upd["user_first"] = $update["first"];
		if($update["middle"] != $usr["middle"]) $upd["user_middle"] = $update["middle"];
		if($update["last"] != $usr["last"]) $upd["user_last"] = $update["last"];
		if($update["bday"] != $usr["age"]) $upd["user_age"] = $update["bday"];
		if($update["gender"] != $usr["gender"]) $upd["user_gender"] = $update["gender"];

		$upd = $sql->escape($upd);

		// Update prefs AFTER we escape. I need to insert as bin which my MySQL update method does not support!
		if($prefs != $usr["prefs"]) $upd["user_prefs"] = "',`user_prefs`=b'".$prefs;

		// Now send the update if there is anything to update
		if($error === "" && count($upd)>0) {
			if($sql->update("users",$upd,"WHERE `user_id`='{$user->get("id")}'") !== FALSE) {
				$success = "Your account has been updated!";

				// Require the user to log back in if they changed their password, otherwise reload their data
				if($changingPassword === TRUE) {
					$user->logout();
					$manager->redirect("/index.php");
				} else {
					$user->setupUser();
				}
			} else
				$error .= "Failed to update your settings due to a database error. Please try again.";
		}
	}

	// Lastly, generate my status message.
	if($success !== "")
		$status = "<div  class='ui-success'>{$success}</div>";
	else if($error != "")
		$status = "<div class='ui-error'>{$error}</div>";
}

function validateEmpty($thing) { return !(is_null($thing) || $thing == ""); }

$theme->load("accountSettings_page", Array("title"=>"Account Settings"));
?>