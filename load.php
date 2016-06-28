<?php
/**
 * Timesules load prompt.
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */

//Added by Paul
// exit('{"code":401,"msg":"normal prompt code starting point"}');
// exit;

/*$value = Array(
		"code"=>401
		);
echo json_encode($value);
exit;*/

define('IN_TIMESULES', true);
require_once 'source/startup.php';

$manager->requireLogin();
header("Content-Type: text/json");
$sql->connect();

/*
// Add Comment
if(!is_null($_POST["addComment"])) {
	$myID = $user->get("id");

	$postID = $sql->escape($_POST["pid"]);
	$groupID = $sql->escape($_POST["gid"]);
	$msg = nl2br($sql->escape($_POST["msg"]));
	$isGroup = ($groupID != "")?true:false;

	$authorized = FALSE;
	// Are we doing a group comment or regular prompt?
	if($isGroup) {
		$sql->query("SELECT `r`.*, `p`.*,(SELECT `group_admin` FROM `groups` WHERE `group_id`='{$groupID}') AS `admin`
			FROM `group_prompts` AS `r`
			LEFT JOIN `group_posts` AS `p` ON `p`.`gpo_pid`=`r`.`gpr_id`
			WHERE `p`.`gpo_id`='{$postID}' AND `r`.`gpr_release`<NOW() AND `r`.`gpr_gid`='{$groupID}'
			");
		$row = $sql->fetch();

		// See if we found it or not
		if($row["gpo_id"] != $postID) exit('{"code":404,"msg":"The capsule requested was not found."}');

		// If I am 1) admin 2) the creator 3) in the group and it is public
		$myGroups = $user->get("groups");
		if($row["admin"] == $myID || $row["gpo_uid"] == $myID || (in_array($row["gpr_gid"], array_keys($myGroups["groups"])) && $row["gpr_vis"] == 1))
			$authorized = TRUE;
	} else {
		$sql->query("SELECT `p`.`post_id`,
			(FIND_IN_SET('{$user->get("id")}',`p`.`post_to`) OR	#Check that we are in the TO field (OR)
				`p`.`post_user`='{$user->get("id")}' OR
			(`p`.`post_vis`='1' AND FIND_IN_SET(`p`.`post_user`, '{$contacts["list"]}'))) AS `allowed` #If the post is public and we are a contact
		FROM `posts` as `p`
		WHERE `p`.`post_id`='{$postID}' AND `p`.`post_release`<NOW()
		");
		$row = $sql->fetch();

		// See if we found it or not
		if($row["post_id"] != $postID) exit('{"code":404,"msg":"The capsule requested was not found."}');

		// If I am 1) The poster or 2) in the to field or 3) if it is public and I am a contact
		if($row["allowed"])
			$authorized = TRUE;
	}

	if($authorized !== TRUE) exit('{"code":401,"msg":"You are not able to comment on this capsule."}');

	$insert = Array(
		"com_type"=>$isGroup,
		"com_user"=>$user->get("id"),
		"com_post"=>$postID,
		"com_date"=>time(),
		"com_comment"=>$msg
		);
	if($sql->insert("comments", $insert))
		exit('{"code":200,"msg":"Comment added successfully.","id":"'.$postID.'"}');
	else
		exit('{"code":500,"msg":"Failed to add comment to the database."}');
	exit;
}*/

// View a "normal" prompt
if(!is_null($_POST["view"]) && $_POST["type"] == 0) {

	// Get the post
	$id = $sql->escape($_POST["id"]);

	// $contacts = $user->get("contacts");
	// $sql->query("SELECT
	// `u`.`user_first`,`u`.`user_last`,`p`.*,
	// (FIND_IN_SET('{$user->get("id")}',`p`.`post_to`) OR	#Check that we are in the TO field (OR)
	// (`p`.`post_vis`='1' AND FIND_IN_SET(`p`.`post_user`, '{$contacts["list"]}'))) AS `allowed` #If the post is public and we are a contact

	// FROM `posts` AS `p`
	// LEFT JOIN `user` AS `u`
	// ON `p`.`post_user`=`u`.`user_id`

	// WHERE
	// `p`.`post_id`='{$id}' AND
	// `p`.`post_release`<='".time()."' AND	#Check that is has been released
	// `p`.`post_draft`='0'
	// ");
	// $post = $sql->fetch();

	$cap = $sql->select("capsules", "*", "WHERE `cap_id`='{$id}'");


	// Grab all attachments from the attachment table.
	$attachments = $sql->select("attachments","*", "WHERE `cap_id`='{$id}'");

	$cap = $sql->fetch();

	// Check that we are allowed to view it!
	if($cap["cap_hidden"] == 1 ) exit('{"code":404,"msg":"The capsule requested was not found or is hidden."}');
	else if( $cap["cap_email_to"] != $user->get("email")) exit('{"code":401,"msg":"You are not authorized to view this capsule."}');

	// Grab all of the comments
	// $sql->query("SElECT `c`.*,`u`.`user_first`,`u`.`user_last`
	// 	FROM `comments` AS `c`
	// 	LEFT JOIN `user` AS `u` ON `c`.`com_user`=`u`.`user_id`
	// 	WHERE `com_post`='{$post["post_id"]}' AND `com_type`='0'"
	// );
	// $commentPosts = $sql->fetchAll();

	// Generate the comments
	// $comments = Array();
	// foreach($commentPosts as $comment) {
	// 	$comments[] = Array(
	// 		//"uid"=>$comment["com_user"],
	// 		"name"=>$comment["user_first"].' '.$comment["user_last"],
	// 		"date"=>date('n/j/y \a\t g:i a', $comment["com_date"]),
	// 		//"icon"=>$manager->getAvatar($comment["user_avatar"], false),
	// 		"msg"=>$comment["com_comment"]
	// 	);
	// }

	if($cap["cap_email_to"] != $user->get("email")){
		$author = $sql->select("users", "`user_id`, `user_first`, `user_last`", "WHERE `user_email`='{$cap["cap_email_to"]}'");
		$author = $sql->fetch();
	}else{
		$author = Array("user_id"=$user->get("id"), "user_first"=>$user->get("first"), "user_last"=>$user->get("last"));
	}

	// Return the JSON
	$ret = Array(
		"code"=>200,
		"type"=>0,
		"id"=>$cap["cap_id"],
		"author"=>$author["user_first"]." ".$author["user_last"],
		"auid"=>$author["user_id"],
		"ldate"=>$cap["cap_lock"],
		"rdate"=>$cap["cap_release"],
		//"attachments"=>"",
		"msg"=>$cap["cap_msg"],
		"title"=>$cap["cap_title"]
		// "attachments"=>$post["post_attachments"],
		// "comments"=>$comments
		);

	echo json_encode($ret);

	 //echo my_json_encode($ret);

	exit;
}

// View a "group" prompt
if(!is_null($_POST["view"]) && $_POST["type"] == 1) {
	// Get the post
	$postID = $sql->escape($_POST["id"]);
	$sql->query("SELECT `r`.*, `p`.*,(SELECT `group_admin` FROM `groups` WHERE `group_id`=`r`.`gpr_gid`) AS `admin`
		FROM `group_prompts` AS `r`
		LEFT JOIN `group_posts` AS `p` ON `p`.`gpo_pid`=`r`.`gpr_id`
		WHERE `p`.`gpo_id`='{$postID}' AND `r`.`gpr_release`<='".time()."'
		");
	$post = $sql->fetch();

	// Check that we are allowed to view it!
	$myGroups = $user->get("groups");
	if($post["gpo_id"] != $postID) exit('{"code":404,"msg":"The capsule requested was not found or is not yet released."}');
	else if($post["gpr_vis"]==0 && $post["gpo_uid"]!=$user->get("id") && $post["admin"]!="1")
		exit('{"code":401,"msg":"This prompt is taged as a \"Private Prompt\" and only the owner and admin can view it."}');
	else if(!in_array($post["gpr_gid"], array_keys($myGroups["groups"])))
		exit('{"code":401,"msg":"You are not a member of this group. Replies can only be viewed by group members."}');

	// Grab all of the comments
	$sql->query("SElECT `c`.*,`u`.`user_first`,`u`.`user_last`
		FROM `comments` AS `c`
		LEFT JOIN `user` AS `u` ON `c`.`com_user`=`u`.`user_id`
		WHERE `com_post`='{$post["gpo_id"]}' AND `com_type`='1'"
		);
	$commentPosts = $sql->fetchAll();

	// Generate the comments
	$comments = Array();
	foreach($commentPosts as $comment) {
		$comments[] = Array(
			//"uid"=>$comment["com_user"],
			"name"=>$comment["user_first"].' '.$comment["user_last"],
			"date"=>date('n/j/y \a\t g:i a', $comment["com_date"]),
			//"icon"=>$manager->getAvatar($comment["user_avatar"], false),
			"msg"=>$comment["com_comment"]
			);
	}

	// Return the JSON
	$ret = Array(
		"code"=>200,
		"type"=>1,
		"id"=>$post["gpo_id"],
		"gid"=>$post["gpr_gid"],
		"author"=>$post["user_first"].' '.$post["user_last"],
		"auid"=>$post["gpo_uid"],
		"ldate"=>date('m/j/y \a\t g:i a',$post["gpr_lock"]),
		"rdate"=>date('m/j/y \a\t g:i a',$post["gpr_release"]),
		"prompt"=>$post["gpr_prompt"],
		"msg"=>$post["gpo_msg"],
		"attachments"=>$post["gpo_attachments"],
		"comments"=>$comments
		);
	echo my_json_encode($ret);

	exit;

}

/**
 * Encode to json (for PHP < 5.3)
 * @param $input - The data to encode
 * @return $input as JSON
 */
function my_json_encode($input) {
	$ret = "";

	// Check to see if it is an array
	if(is_array($input)) {
		// Then see if we are doing a normal array
		if(!is_assoc($input)) {
			if(count($input) == 0) $ret = "[]";
			else {
				$ret = "[";
				foreach($input as $val) {
					$ret .= my_json_encode($val).',';
				}
				$ret = substr($ret, 0, -1).']';
			}

		// Or an object
		} else {
			if(count($input) == 0) $ret = "{}";
			else {
				$ret = "{";
				foreach($input as $key=>$val) {
					$ret .= '"'.$key.'":'.my_json_encode($val).',';
				}
				$ret = substr($ret, 0, -1).'}';
			}
		}
	// true or false
	} else if($input === true || $input === false || $input == "true" || $input == "false") {
		$ret = ($input === true || $input == "true")?"true":"false";

	// numbers 0-9
	} else if(preg_match("/[0-9]*/", $input, $match) && $match[0] == $input && $input != "") {
		$ret = $input;

	// Strings (or anything else)
	} else {
		$ret = '"'.str_replace(Array('"',"\r","\n"), Array('\"', '\r', '\n'), $input).'"';
	}

	return $ret;
}

// Modified from
// http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric/4254008#4254008
function is_assoc($array) {
	return count(array_filter(array_keys($array), 'is_string')) > 0 ? true : false;
}

// Print a Not Found to anything that is not a request!
$_SERVER["QUERY_STRING"] = 404;
header("Content-Type: text/html");
require 'error.php';
?>
