<?php
/**
 * Timesules groups.
 *
 * Dan Telljohann
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

$manager->requireLogin();

function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

//-----------------------------------------------------------------
// Create a group
//-----------------------------------------------------------------
if(!is_null($_GET["create"])) {
	if(!is_null($_POST["doit"])) {
		$error = "";
		if($_POST["group_name"] == "") $error = "\"Group Name\" is a required field.";
		$insert = Array("group_name"=>$_POST["group_name"]);
		$insert = $sql->escape($insert);

		if($error === "" && ($id = $sql->insert("groups", $insert)) !== FALSE) {
			//$groups = $user->get("groups");
			//$groups = ($groups["list"]!="")?$groups["list"].",".$id:$id;

			//$sql->update("users", "`user_groups`='{$groups}'", "WHERE `user_id`='{$user->get("id")}'");
			//header("Location: /groups.php?g={$id}");
			$insert = Array("group_id"=>$id,"user_id"=>$user->get("id"),"user_role"=>"owner");
			$sql->insert("user_owns_groups", $insert);
			header("Location: /groups.php?g={$id}");
		} else if($error === "") {
			$error = "Failed to create new group due to an unknown error. Please try again.";
		}

		if($error !== "") {
			$theme->load("groups_page_default", Array("title"=>"Create Group","create"=>true,"message"=>$error));
		}
	} else {
		$theme->load("groups_page_default", Array("title"=>"Create Group","create"=>true));
	}
	exit;
}

//-----------------------------------------------------------------
// Rename a group
//-----------------------------------------------------------------
if(!is_null($_GET["rename"])) {
	$gid = $sql->escape((!is_null($_POST["gid"]))?$_POST["gid"]:$_GET["rename"]);
	
	//Get the user from the database
	$results = $sql->select("user_owns_groups", "*", "WHERE `group_id`='$gid' and `user_id`='{$user->get("id")}'");
	$row = $sql->fetchAll();
	$currentUser = $row[0];	

	if($currentUser["user_role"] != "owner") $theme->load("groups_page_default", Array("title"=>"Error Group","error"=>"You are not the admin of this group. Only admins can rename groups."));

	if(!is_null($_POST["doit"])) {
		$error = "";
		if($_POST["group_name"] == "") $error = "\"Group Name\" is a required field.";
		$update = Array("group_name"=>$_POST["group_name"]);
		$update = $sql->escape($update);

		if($error === "" && $sql->update("groups", $update, "WHERE `group_id`='{$gid}'") !== FALSE) {
			header("Location: /groups.php?g={$gid}");
		} else if($error === "") {
			$error = "Failed to rename group due to an unknown error. Please try again.";
		}

		if($error !== "") {
			$theme->load("groups_page_default", Array("title"=>"Rename Group","rename"=>$row,"message"=>$error));
		}
	} else {
		$theme->load("groups_page_default", Array("title"=>"Rename Group","groupId"=>$gid));
	}
	exit;
}

//-----------------------------------------------------------------
// Create a group prompt
//-----------------------------------------------------------------
if(!is_null($_GET["prompt"])) {
	$gid = $sql->escape((!is_null($_POST["gid"]))?$_POST["gid"]:$_GET["prompt"]);

	//Get the user from the database
	$results = $sql->select("user_owns_groups", "*", "WHERE `group_id`='$gid' and `user_id`='{$user->get("id")}'");
	$row = $sql->fetchAll();
	$currentUser = $row[0];	

	//Get group to pass to form
	$results = $sql->select("groups", "*", "WHERE `group_id`='$gid'");
	$row = $sql->fetchAll();

	if($currentUser["user_role"] != "owner") $theme->load("groups_page_default", Array("title"=>"Create Group Prompt Error","error"=>"You are not the admin of this group. Only admins can create group prompts."));

	if(!is_null($_POST["doit"])) {
		$error = "";

		$prompt = $_POST["prompt"];
		$lockPost = $_POST["lock"];
		$releasePost = $_POST["release"];
		$vis = ($_POST["vis"] == "1")?"1":"0";

		

		if($prompt == ""){
			$error = "\"Prompt\" is a required field.<br />";
		} 




		// Process lock and release date/time
		if(@checkdate($lockPost["month"], $lockPost["date"], $lockPost["year"])) {
			$lock = $lockPost["year"];
			$lock .= "-";
			$lock .= $lockPost["month"];
			$lock .= "-";
			$lock .= $lockPost["date"];
			$lock .= " ";
			if ($lockPost["time"]["hour"] < 10){
				$lock .= "0";
				$lock .= $lockPost["time"]["hour"];
			}
			else {
				$lock .= $lockPost["time"]["hour"];
			}
			$lock .= ":";
			if ($lockPost["time"]["minute"] < 10) {
				$lock .= "0";
				$lock .= $lockPost["time"]["minute"];
			}
			else {
				$lock .= $lockPost["time"]["minute"];
			}
			$lock .= ":00 ";
		}
		else {
			$lock = -1;
		}
		if(@checkdate($releasePost["month"], $releasePost["date"], $releasePost["year"])) {
			$release = $releasePost["year"];
			$release .= "-";
			$release .= $releasePost["month"];
			$release .= "-";
			$release .= $releasePost["date"];
			$release .= " ";
			if ($releasePost["time"]["hour"] < 10){
				$release .= "0";
				$release .= $releasePost["time"]["hour"];
			}
			else {
				$release .= $releasePost["time"]["hour"];
			}
			$release .= ":";
			if ($releasePost["time"]["minute"] < 10) {
				$release .= "0";
				$release .= $releasePost["time"]["minute"];
			}
			else {
				$release .= $releasePost["time"]["minute"];
			}
			$release .= ":00 ";
		}
		else{
			$release = -1;
		}

		if($lock == "") $error .= "Please enter a lock date.<br />";
		else if($lock == -1) $error .= "Lock date is invalid.<br />";
		if($release == "") $error .= "Please enter a release date.<br />";
		else if($release == -1) $error .= "Relase date is invalid.<br />";

		$sql->select("users","*","WHERE `user_id`='{$user->get("id")}'");
		$row = $sql->fetch();
		$email = $row["user_email"];

		$insert = Array(
			"cap_email_to"=>"Group",
			"cap_email_from"=>$email,
			"cap_title"=>$prompt,
			"cap_msg"=>$_POST["des"],
			"cap_lock"=>$lock,
			"cap_release"=>$release,
			"cap_vis"=>$vis
		);
		$insert = $sql->escape($insert);

		if($error === "" && ($id = $sql->insert("capsules", $insert)) !== FALSE) {
			/*$data = Array(
				"name"=>$row["group_name"],
				"prompt"=>$prompt,
				"id"=>$id,
				"g"=>$gid,
				"lock"=>date("m/d/y g:ia", $lock),
				"url"=>"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?g={$gid}&p={$id}"
			);
			$manager->addNotification($row["group_users"], Manager::NEW_GROUP_PROMPT, 'newGroupPrompt', $data);
			*/

			$insert = Array(
				"cap_id"=>$id,
				"group_id"=>$gid
			);

			$sql->insert("group_has_capsules", $insert);

			header("Location: /groups.php?g={$gid}&p={$id}");
		} else if($error === "") {
			$error = "Failed to create group prompt due to an unknown error. Please try again.";
		}

		if($error !== "") {
			$theme->load("groups_page_default", Array("title"=>"Create Group Prompt","prompt"=>$row,"message"=>$error));
		}
	} else {
		$theme->load("groups_page_default", Array("title"=>"Create Group Prompt","prompt"=>$row[0]));
	}
	exit;
}

//-----------------------------------------------------------------
// Add member
//-----------------------------------------------------------------
if(!is_null($_POST["manageUser"])) {
	header("Content-Type: text/json");

	$id = $sql->escape($_POST["gid"]);
	$usr =  $sql->escape($_POST["user"]);
	$add = ($_POST["remove"] != "true");
	
	$insert = Array(
		"group_id"=>$id,
		"user_id"=>$usr,
		"user_role"=>"member",
	);
	$insert = $sql->escape($insert);

	// Send the update to the database
	if($sql->insert("user_owns_groups", $insert)) {
		$msg = (!$add)?"Removed":"Added";
		echo '{"code":200,"msg":"'.$msg.' Successfully!","removed":'.((!$add)?"true":"false").'}';
	} else {
		echo '{"code":500,"msg":"Failed to update database"}';
	}
	exit;
}

//-----------------------------------------------------------------
// Reply to group prompt
//-----------------------------------------------------------------
if(!is_null($_GET["reply"])) {
	$pid = $sql->escape((!is_null($_POST["pid"]))?$_POST["pid"]:$_GET["reply"]);
	
	$results = $sql->select("capsules", "*", "WHERE `cap_id`='{$pid}'");
	$capsule = $sql->fetch();

	if(!is_null($_POST["doit"])) {
		$error = "";
		if($_POST["msg"] == "") $error = "\"Message\" is a required field.";

		$attachTime = date("Y/m/d h:m:s", time());

		$insert = Array(
			"cap_id"=>$pid,
			"user_id"=>$user->get("id"),
			"attachment_text"=>$_POST["msg"],
			"attach_time"=>$attachTime,
		);
		$insert = $sql->escape($insert);

		if($error === "" && ($id = $sql->insert("attachments", $insert)) !== FALSE) {
			$results = $sql->select("group_has_capsules", "*", "WHERE `cap_id`='{$pid}'");
			$row = $sql->fetch();
			header("Location: /groups.php?g={$row[group_id]}&p={$pid}");
		} else if($error === "") {
			$error = $pid;
			$error .= " ";
			$error .= $user->get("id");
			$error .= " ";
			$error .= $_POST["msg"];
			$error .= " ";
			$error .= $attachTime;
			$error .= " ";
		}

		$row["gpo_msg"] = $_POST["msg"];
		if($error !== "") {
			$theme->load("groups_page_default", Array("title"=>"Reply to Group Prompt","reply"=>$row,"message"=>$error));
		}
	} else {
		$theme->load("groups_page_default", Array("title"=>"Reply to Group Prompt","reply"=>$capsule));
	}
	exit;
}

//-----------------------------------------------------------------
// Edit reply to group prompt
//-----------------------------------------------------------------
if(!is_null($_GET["edit"])) {
	$pid = $sql->escape((!is_null($_POST["pid"]))?$_POST["pid"]:$_GET["edit"]);
	$sql->query("
		SELECT *,(
			SELECT (
				FIND_IN_SET('{$user->get("id")}',`g`.`group_users`)!=0 OR
				`g`.`group_admin`='{$user->get("id")}'
			)
			FROM `groups` AS `g`
			WHERE `g`.`group_id`=`p`.`gpr_gid`
		) AS `inGroup`
		FROM `group_prompts` AS `p`
		LEFT JOIN `group_posts` AS `o`
		ON `o`.`gpo_pid`=`p`.`gpr_id`
		WHERE `p`.`gpr_id`='{$pid}' AND `o`.`gpo_uid`='{$user->get("id")}'
	");
	$row = $sql->fetch();

	if($row["inGroup"] != "1")
		$theme->load("groups_page_default", Array(
			"title"=>"Reply to Group Prompt",
			"error"=>"You are not a member of this group. Only members can reply to group prompts."
		));

	if(!is_null($_POST["doit"])) {
		$error = "";
		if($_POST["msg"] == "") $error = "\"Message\" is a required field.";
		$update = Array(
			"gpo_msg"=>$_POST["msg"],
			"gpo_date"=>time()
		);
		$update = $sql->escape($update);

		if($error === "" && $sql->update("group_posts", $update, "WHERE `gpo_id`='{$row["gpo_id"]}'") !== FALSE) {
			header("Location: /groups.php?g={$row["gpr_gid"]}&p={$pid}");
		} else if($error === "") {
			$error = "Failed to edit reply due to an unknown error. Please try again.";
		}

		$row["gpo_msg"] = $_POST["msg"];
		if($error !== "") {
			$theme->load("groups_page_default", Array("title"=>"Reply to Group Prompt","reply"=>$row,"edit"=>true,"message"=>$error));
		}
	} else {
		$theme->load("groups_page_default", Array("title"=>"Reply to Group Prompt","edit"=>true,"reply"=>$row));
	}
	exit;
}

//-----------------------------------------------------------------
// Load a group
//-----------------------------------------------------------------
if(!is_null($_GET["g"])) {
	$gid = $sql->escape($_GET["g"]);

	$results = $sql->select("groups", "*", "WHERE `group_id`='{$gid}'");
	$row = $sql->fetchAll();

	//$row[0]["group_promptCount"] = $sql->rows();
	$error = "";
	if($row[0]["group_id"] != $gid) $error = "The group requested could not be found. Please choose another group:<br />";

	if($error !== "") $theme->load("groups_page_default", Array("title"=>"Choose Group", "message"=>$error));

	$theme->load("groups_page", Array("title"=>$row[0]["group_name"],"group"=>$row));
} else {
	$theme->load("groups_page_default", Array("title"=>"Choose Group"));
}
?>
