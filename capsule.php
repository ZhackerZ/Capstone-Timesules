<?php
/**
* Timesules new prompt.
*
* @author Tyler Hadidon Yuzhen Liu
 * @copyright 2015
*/
define('IN_TIMESULES', true);
require_once 'source/startup.php';
date_default_timezone_set ( "America/New_York" );

$manager->requireLogin();

$data = null;
$toList = null;
$allowDraft = true;

$sql->connect();

// Load draft capsule
if(!is_null($_GET["edit"]) && $_GET["edit"]=="true"){
	$_SESSION["draft"] = true;

	$id = $sql->escape($_GET["capsule"]);
	$data = $sql->select("capsule", "*", "WHERE `cap_id`='{$id}'");
	$data = $sql->fetch();
}


//Accessing a capsule
if(!is_null($_GET["capsule"])) {
	$id = $sql->escape($_GET["capsule"]);

	$cap = $sql->select("user_has_capsules", "*", "WHERE `cap_id`='{$id}' AND `user_id`='{$user->get("id")}'"); //
	if ($cap === FALSE){
		return; // no capsules found
	} else {
		$cap = $sql->select("capsules", "*", "WHERE `cap_id`='{$id}'"); //
		$cap = $sql->fetch();
	
		// $sql->select("posts", "*", "WHERE `post_id`='{$id}' AND
		// 	(`post_user`='{$user->get("id")}' OR (FIND_IN_SET('{$user->get("id")}',`post_to`)!=0 AND `post_draft`='0')) AND
		// 	(`post_draft`='1' OR `post_lock`>UNIX_TIMESTAMP())");
		// $row = $sql->fetch();
	
		// $sql->select("users","user_id, user_first, user_last, user_avatar","WHERE `user_email` IN({$row["cap_email_to"]})");
		$sql->select("users","*","WHERE `user_email` IN({$row["cap_email_to"]})");
		$toList = $sql->fetchAll();
	
		if($cap["cap_id"] == $id) {
			$data = $cap;
			$allowDraft = ($cap["cap_draft"] == "1");
		} else {
			$data = FALSE;
		}
	}

} else if(!is_null($_GET["capsule"])) {

	$c = $sql->escape($_GET["capsule"]);

	$sql->select("users","user_id,user_first,user_last,user_avatar","WHERE `user_id` IN({$c})");
	$toList = $sql->fetchAll();
}

$cap = null;

//Creating a New Capsule
if(!is_null($_POST["draft"]) || !is_null($_POST["send"]) ){
	require_once 'source/FormatBar.func.php';

	$result = "";
	$error = "";

	$cap = $_POST["capsule"];
	$title = substr(strip_tags($cap["title"]),0,200);

	// Format lock & release dates
	$lockDate = date("Y-m-d H:i:s", time());

	$relDate = $cap["cap_release"];
	$relDate = explode("T", $relDate);
	$relDate = $relDate[0]." ".$relDate[1];
	
	// Time conversion Eastern to Europe/London. This is needed to ensure release of capsules at correct times.
	//$date = date_create($relDate);
	//date_add($date, date_interval_create_from_date_string('5 hours'));
	//$relDate= $date->format('Y-m-d H:i:s');
	
	// The string $relDate looks like this: YYYY-MM-DD HH:MM:SS, so we need the substring starting from position 11.
	$relTime = substr($relDate, 11);

	$msg = $cap["cap_msg"];
	$vis = ($cap["cap_vis"] == "1") ? "1" :"0";
	$toVal = $cap["cap_to"];
	$attachments = $_FILES['attachment']['name'];

	$updateID = $sql->escape($cap["update"]);

	// Editing draft?
	$isDraft = !is_null($_POST["draft"]);
	$isUpdate = ($updateID != "");
	$allowDraft = $isDraft || !$isUpdate;

	// Process lock and release date/time

	// Form Validation
	if($title == "") $error .= "Please enter a prompt.<br />";

	if($lockDate == "") $error .= "Please enter a lock date.<br />";
	else if(!$lockDate) $error .= "Lock date is invalid.<br />";

	if($relDate == "") $error .= "Please enter a release date.<br />";
	else if(!$relDate) $error .= "Relase date is invalid.<br />";

	else if(strtotime($relDate) <= strtotime("now"))
		$error .= "The release date/time must be after the current date/time and lock date/time.<br />";

	if($msg == "") $error .= "Please enter a capsule message.<br />";
	if($toVal == "") $error .= "Please add contacts to share capsule with or add yourself for a personal capsule.<br />";

	// Validate contacts
	$ids = explode(",", $toVal);
	$to = Array();

	$contacts = $sql->select("user_has_contacts", "*", "WHERE `user_id`='{$user->get("id")}' OR `contact_id`='{$user->get("id")}'");
	$contacts = $sql->fetchAll();

	$myContactIds= array();

	foreach ($contacts as $cont) {
		if($cont["user_id"] == $user->get("id")){
			array_push($myContactIds, $cont["contact_id"]);
		} else {
			array_push($myContactIds, $cont["user_id"]);
		}
	}

	foreach($ids as $id) {
		if($id == "") continue;
		// if( (in_array($id, $myContactIds) || $id == $user->get("id") ) && !in_array($id, $to))
		if( in_array($id, $myContactIds) || $id == $user->get("id") )
			$to[] = $id;
		else {
			$error .= "There was a problem reading the contacts to share with.
			Remember, you cannot share with contacts that have not accepted your contact request.";
			break; // If there was an error, kill it
		}
	}

	// Finally, if there are no errors, insert/update
	if($error === "") {

		$recipients = Array();

		foreach ($to as $id) {
			$sql->select("users", "user_email", "WHERE `user_id`='{$id}'");
			$recip = $sql->fetch();
			$recipients[] = $recip;
		}

		$insertList = array();
		$c = 0;
		foreach ($recipients as $recip) {
			$insert = Array(
				"cap_email_to"=> $recipients[$c]["user_email"],
				"cap_title"=>$title,
				"cap_msg"=>$msg,
				"cap_time"=>$relTime,
				"cap_lock"=>$lockDate,
				"cap_release"=>$relDate,
				"cap_vis"=>$vis,
				"cap_draft"=>($isDraft)?"1":"0"
				);

			$sqlresult = $sql->escape($insert);
			$insertList[] = $sqlresult;
			$c++;
		}

		// UPDATES (if need be)
		// if($isUpdate) {
		// 	// Varifiy we are allowed to edit this post
		// 	$sql->select("posts", "`post_user`,`post_id`,`post_draft`,`post_lock`,`post_to`",
		// 		"WHERE (`post_user`='{$user->get("id")}' OR FIND_IN_SET('{$user->get("id")}',`post_to`)!=0)
		// 		AND `post_id`='{$updateID}'");
		// 	$row = $sql->fetch();

		// 	if($updateID != $row["post_id"])
		// 		$error = "Could not find the time capsule in order to update it. Please try again or create a new one.";
		// 	else if($user->get("id") != $row["post_user"] && !in_array($user->get("id"), explode(",", $row["post_to"])))
		// 		$error = "You are not the author of that capsule!";
		// 	else if($row["post_lock"] <= time() && $row["post_draft"] != "1")
		// 		$error = "This time capsule has already been locked and cannot be edited.";
		// 	else if($row["post_draft"] != "1" && $isDraft)
		// 		$error = "You cannot make a draft off of an already submitted time capsule.";
		// 	else if($sql->update("posts", $insert, "WHERE `post_id`='{$updateID}'") !== FALSE) {
		// 		$result = (($isDraft)?"Time Capsule draft updated!":"Time Capsule ".(($lock<=time())?"sealed and buried":"updated and open for edits")."!");
		// 	} else {
		// 		$error = "Failed to update database. Please try again.";
		// 	}

		// // Otherwise, insert
		// } else
		
		// Begin insert
		if(($updateID = $sql->insert("capsules", $insertList[0])) !== FALSE) {			
			$val = Array("cap_id"=>$updateID, "user_id"=>$user->get("id"));//, "role"=>"owner");

			// Create an attachments folder in each of the user's attachments folder corresponding to the capsule
			$attachmentsFolder = './attachments/'.$updateID;
			//For file permissions
			$old_umask = umask(0);
			if (!mkdir($attachmentsFolder, 0755)) {
				$error .= "Failed to create capsule attachments directory<br />";
			}
			//For file permissions
			umask($old_umask);
		
			// Add location of each attachment
			foreach($attachments as $attachment) {
				$attachmentLocation = ''.$updateID.'/'.$attachment;
				$attachmentsValues = Array("cap_id"=>$updateID, "location"=>$attachmentLocation);
				if(!$sql->insert("attachments", $attachmentsValues)) {
					$error = "Failed to insert attachments into the database. Please try again.";
				}
			}
			
			//Move to attachments folder
			
			foreach($_FILES['attachment']["tmp_name"] as $index => $tmpName){

					//Take files temp name and save the file
					$name = $_FILES["attachment"]["name"][$index];
					
					if(!move_uploaded_file($tmpName,"$attachmentsFolder/".$name.".file")){
						$error .= "Failed to upload file(s) to the capsule";
					}

					//Form validation example below
					//check file types and file size here.
					//Leave out for now until we can get a handle on error messages.
					//Commented 2/24/15
					/*if($saveFile['name'] != ""){
						if ( ($_FILES[$name]["type"] == "image/gif")|| ($_FILES[$name]["type"] == "image/jpeg") && ($_FILES[$name]["size"] < 10485760)){ 
							if (file_exists("checkdirectoryhere/". $_FILES[$name]["name"])) {								
								echo "<div class='confirmation'";
								echo "<span style='font-size:18pt; color:white;'>".$_FILES[$name]['name']." already exists.  <br />";
								echo "<a href='login.php' style='font-size:18pt; color:white;'>Click here to try again.</a></span>";
								echo "</div>";
						} else {
							//Other wise upload the file
							move_uploaded_file($saveFile['tmp_name'],"./attachments/".$user->get("id")."/".$updateID);
						}
					*/
				
			}

			// Make sure each user has a link to the capsule by updating the user_has_capsules table
			if($sql->insert("user_has_capsules", $val)) {
				foreach ($to as $id) {
					$notMsg = "You have a Timesule available!";
					$notification = array("notification_type"=>3, "user_id"=>$id, "message"=>$notMsg, "cap_id"=>$updateID);
					$note_result = $sql->insert("notifications", $notification);

					if($id != $user->get("id")){
						// Add record to user_has_capsules
						$val = Array("cap_id"=>$updateID, "user_id"=>$id);
						$result = $sql->insert("user_has_capsules", $val);

						if(!$result || !$note_result){
							$error = "Error adding recipients to user_has_capsules table";
							break;
						}
					}
				}
			}

			$result = (($isDraft) ? "Time Capsule saved as draft!" : "Time Capsule ".(($lock<=time()) ? "sealed and buried" : "added and open for edits")."!");
			$cap = null;

		} else {
			$error = "Failed to insert into the database. Please try again.";
		}
	}

	if($error != "") {
		$error = "<strong>One or more errors have occurred!</strong><br />{$error}";

		// $sql->select("users","user_id, user_first, user_last, user_avatar","WHERE `user_id` IN({$to})");
		// $toList = $sql->fetchAll();

		// Delete uploaded files if we had an error. Otherwise they are lost...
		// deleteAttachments($attachments);
	}

}

// Load theme page
// $cap_result = array("tite"=>$title, "lock"=>$lockDate, "rel"=>$relDate, "msg"=>$msg, "error"=>$error, "tos"=>$to, "recipts"=>$recipients, "sqlresult"=>$sqlresult);

$theme->load("capsule_page", Array("title"=>"Capsule", "toList"=>$toList, "allowDraft"=>$allowDraft, "error"=>$error, "result"=>$result, "capsule"=>$data, "cap_result"=>$cap_result) );
?>