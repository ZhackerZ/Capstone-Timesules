<?php
/**
 * Timesules contacts page.
 *
* @author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

$manager->requireLogin();
function getMyContacts($contacts, $id){
	$myContactIds = array();

	foreach($contacts as $cont) {
		if($cont["user_id"]==$id){
			$id = $cont["contact_id"];
			array_push($myContactIds, $id);
		}else{
			$id = $cont["user_id"];
			array_push($myContactIds, $id);
		}
	}
	return $myContactIds;
}

// Send a request to someone
if(!is_null($_POST["add"])) {
	$id = $sql->escape($_POST["contact"]);

	// $noti = '%"n":'.Manager::NEW_CONTACT.',"d":{"i":'.$user->get("id").',"n":%';
	// $query = "WHERE `user_id`='{$ids}' AND `user_id` NOT IN({$contacts["list"]})";// LIMIT 0,10";
	$count = $sql->select("users", "*", "WHERE `user_id`='{$id}'");
	$row = $sql->fetch();

	$contacts = $sql->select("user_has_contacts", "*", "WHERE `user_id`='{$user->get("id")}' OR `contact_id`='{$user->get("id")}'");
	$contacts = $sql->fetchAll();

	$user_contacts = getMyContacts($contacts, $user->get("id"));

	// var_dump($user_contacts);

	//this is someone user's not already connected to
	if(!in_array($id, $contacts)){
		$url = "http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?res={$user->get("id")}";

		$back = $manager->addNotification($row["user_id"], Manager::NEW_CONTACT, "newContact", Array("url"=>$url));

		header("Content-Type: text/json");
		if(!$back){
			// var_dump($back);
			exit('{"code":200,"msg":"Your contact request has been sent to '.$row["user_first"].' '.$row["user_last"].'. This user must accept your invitation in order to appear on your contacts list."}');
		}else{
			exit('{"code":500,"msg":"Sorry, an unknown error has occurred and the contact request was not able to be sent. Please try again."');
				// exit('{"code":500,"msg":"'.var_dump($back).'"}');
		}
		//else display already connected error
	}else{
		exit('{"code":500,"msg":"Seems Like you are already contacts"}');
	}

	exit;
}

// Accept or reject a person as a contact
if(!is_null($_POST["respond"])) {
	header("Content-Type: text/json");

	$accept = $_POST["respond"] === 'accept';
	$deny = $_POST["respond"] === 'deny';
	$id = $sql->escape($_POST["id"]);
	$notId = $sql->escape($_POST["notification_id"]);

	// $noti = '"n":'.Manager::NEW_CONTACT.',"d":{"i":'.$id.',"n":';

	// if(strstr($user->get("notifications"), $noti) === FALSE)
	// 	exit('{"code":401,"msg":"You do not have a request from that user."}');

	if($accept === true) {
		//add that user as our contact in the table
		$val = Array("user_id"=>$user->get("id"), "contact_id"=>$id);
		$added = $sql->insert("user_has_contacts", $val);

		$note = $sql->delete("notifications","`notification_id`='{$notId}'");

		if(($added && $note) !== FALSE ) {
			exit('{"code":200,"title":"Contact Accepted","msg":"You have accepted the contact request."}');
		} else {
			exit('{"code":500,"title":"Error!","msg":"Failed to make changes to the database. Please try again."}');
		}
	}

	if($deny) {

		$note = $sql->delete("notifications","`notification_id`='{$notId}'");

	// if($deny || $accepted === TRUE) {
		// $notifies = explode("},{", substr($user->get("notifications"),1,-1));
		// $needle = '"n":'.Manager::NEW_CONTACT.',"d":{"i":'.$id.',"n":';
		// for($i=0;$i<count($notifies);$i++) {
		// 	if(strstr($notifies[$i], $needle)!==FALSE) {
		// 		unset($notifies[$i]);
		// 	}
		// }
		// $notifies = implode("},{", $notifies);
		// $notifies = ($notifies!="")?"{".$notifies."}":$notifies;

		// $result = $sql->update("users", "`user_notifications`='{$notifies}'", "WHERE `user_id`='{$user->get("id")}'");

		exit('{"code":200,"title":"Contact Denied","msg":"You have denied the contact request."}');

	// 	if($result !== FALSE && $deny) {
	// 		exit('{"code":200,"title":"Contact Denied","msg":"You have denied the contact request."}');
	// 	} else if($result !== FALSE && $accepted === TRUE) {
	// 		exit('{"code":200,"title":"Contact Accepted","msg":"You have accepted the contact request."}');
	// 	} else if($accepted === TRUE)
	// 		exit('{"code":400,"title":"Warning!","msg":"Failed to remove request from the database; however, the user was added to your contacts."}');
	// 	else
	// 		exit('{"code":500,"title":"Error!","msg":"Failed to make changes to the database. Please try again."}');
	// } else
	// 	exit('{"code":300,"title":"Invalid Command","msg":"Server received an invalid command."}');
	// exit;
	}
}

// Accept or Deny a reponese
$notFound = NULL;
if(!is_null($_GET["res"])) {
	$id = $sql->escape($_GET["res"]);

	$hasRequest = FALSE;
	$noti = '"n":'.Manager::NEW_CONTACT.',"d":{"i":'.$id.',"n":';
	if(strstr($user->get("notifications"), $noti) !== FALSE) {
		$hasRequest = TRUE;
		$sql->select("users", "`user_id`,`user_first`,`user_last`,`user_avatar`,`user_email`,`user_gender`,`user_age`","WHERE `user_id`='{$id}'");
		$row = $sql->fetch();
		$icon = $manager->getAvatar($row["user_avatar"], false);
	}

	$contacts = $user->get("contacts");
	$count = count($contacts["users"]);

	if($id != "" && $row["user_id"] == $id && $hasRequest === TRUE) {
		$theme->load("contacts_AcceptDeny", Array("title"=>"Accept Contact Request","userData"=>$row));
		exit;
	} else {
		//$theme->load("contacts_page", Array("title"=>"Contact Request Not Found","notFound"=>TRUE));
		$notFound = Array("title"=>"Contact Request Not Found","notFound"=>TRUE);
	}
}

// Remove a contact
if(!is_null($_POST["remove"])) {
	header("Content-Type: text/json");

	$id = $sql->escape($_POST["remove"]);
	$confirm = ($_SESSION["confirm_remove"] === $_POST["remove"] && $_POST["conf"] == 'true');

	// You cannot remove yourself silly!
	if($id == $user->get("id"))
		exit('{"code":401,"msg":You cannot remove yourself silly!"}');

	// Are we contacts?
	$myContacts = $user->get("contacts");

	$myContacts = @explode(",",$myContacts["list"]);
	if(!in_array($id, $myContacts))
		exit('{"code":401,"msg":"You are not contacts with the user requested to be removed."}');

	// Can we be found in the database?
	$sql->select("users","`user_contacts`,`user_id`,`user_first`,`user_last`", "WHERE `user_id`='{$id}'");
	$row = $sql->fetch();
	if($row["user_id"] != $id)
		exit('{"code":404,"msg":"The user you tried to remove was not found."}');

	// If you already confirmed
	if($confirm === TRUE) {

		// Take HIS contacts, and remove your ID
		$otherCont = @explode(",", $row["user_contacts"]);
		array_splice($otherCont,array_search($user->get("id"),$otherCont),1);
		$otherCont = @implode(",", $otherCont);


		// Remove HIS id from your list
		array_splice($myContacts,array_search($id,$myContacts),1);
		$meCont = @implode(",", $myContacts);

		// Update both
		$query = "UPDATE `user`
		SET `user_contacts`=(
			CASE
			WHEN `user_id`='{$id}'
			THEN '{$otherCont}'
			ELSE '{$meCont}'
			END) WHERE `user_id`='{$id}' OR `user_id`='{$user->get("id")}'";
if($sql->query($query)!==FALSE) {
	$_SESSION["confirm_remove"] = -1;
	exit('{"code":200,"msg":"Removed '.$row["user_first"].' '.$row["user_last"].' from your contacts.","id":'.$row["user_id"].'}');
} else {
	exit('{"code":500,"msg":"There was an error while making changes to the database. Please try again."}');
}
} else {
	$_SESSION["confirm_remove"] = $id;
	exit('{"code":201,"id":'.$row["user_id"].',"msg":"Please confirm that you want to remove '.$row["user_first"].' '.$row["user_last"].' from your contacts.","name":"'.$row["user_first"].'"}');
}
exit;
}

$userContacts = Array();
$searchResults = NULL;

// Search for a contact
if(!is_null($_GET["search"]) && $_GET["search"] != "") {
	$search = $sql->escape($_GET["search"]);
	$searchResults = Array();

	// //get contacts
	// // $sc = $sql->select("user_has_contacts", "*", "WHERE `user_id`='{$user->get("id")}' OR `contact_id`='{$user->get("id")}'" );
	// // $sc = $sql->fetchAll();

	// $sc = getMyContacts($var1, $var2);

	// // $userContacts = $sc;
	// // $sc = $user->get("contacts");
	// // $sc = $sc["users"];

	// // Run search criteria here! First, Last, Email
	// $fields = Array("first", "last", "email");
	// $found = 0;
	// foreach($sc as $c) {
	// 	foreach($fields as $field) {
	// 		if(strstr(strtolower($c[$field]), strtolower($search)) !== FALSE) {
	// 			$userContacts[] = $c;
	// 			$found++;
	// 			break; // Stop searching and continue to the next contact
	// 		}
	// 	}
	// }

	// Search database for similarities
	$ret = ($found < 5)?10-$found:5;
	$sql->select("users",
		"`user_id` as `id`,`user_first` as `first`,`user_last` as `last`,`user_email` as `email`,`user_avatar` as `avatar`",
		"WHERE `user_first` LIKE '%{$search}%' OR `user_last` LIKE '%{$search}%' OR `user_email` LIKE '%{$search}%'
		AND `user_id`!='{$user->get("id")}'
		ORDER BY `user_last` DESC LIMIT 0,{$ret}"
		);
	$searchResults = $sql->fetchAll();

}
// else {
// 	$userContacts = $user->get("contacts");
// 	$userContacts = $userContacts["users"];
// }

// $data = Array("title"=>"Contacts","userContacts"=>$userContacts,"searchResults"=>$searchResults);
$data = Array("title"=>"Contacts","searchResults"=>$searchResults);
if(!is_null($notFound)) { $data = array_merge($data, $notFound); }
$theme->load("contacts_page", $data);

?>
