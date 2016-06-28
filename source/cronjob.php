<?php
define('DEBUG', FALSE);
define('IN_TIMESULES', TRUE);
ob_start();

set_time_limit(0);
ignore_user_abort(true);

//if($argv[1] != "timesules") exit;

// First, get the classes
require_once 'MySQL.class.php';
require_once 'Manager.class.php';

// Get the root directory
$root = "../";

// Setup system
$sql = new SQL();
$user = null;
$theme = null;
$manager = new Manager();

// Turn debugging on for the time being
$sql->setDebugging((constant('DEBUG') === TRUE)?true:false);

// Check sendacapsule for new capsules
$sql->select('sendacapsule', '*', "WHERE `cap_time`<='".time()."'");
$sendCapsuleDelete = Array();
foreach($sql->fetchAll() as $cap) {
	$data = Array("message"=>$cap["cap_msg"],"subject"=>$cap["cap_subj"]);
	$manager->mail($cap["cap_email"], "sendACapsuleReleased", $data);
	$sendCapsuleDelete[] = $cap["cap_id"];
}
if(count($sendCapsuleDelete) > 0)
	$sql->delete('sendacapsule', "`cap_id` IN(".implode(",", $sendCapsuleDelete).")");

// Gather up all of the capsules that were released in the
// last 15 minutes (script runs every 15 minutes)
$sql->query("
SELECT `p`.*,`u`.`user_first`,`u`.`user_last`
FROM `posts` AS `p`
LEFT JOIN `user` AS `u`
ON `u`.`user_id`=`p`.`post_user`
WHERE `p`.`post_release`>'".(time()-60*15)."' AND `p`.`post_release`<='".time()."' AND `p`.`post_draft`='0'");
foreach($sql->fetchAll() as $cap) {
	$data = Array(
		"name"=>$cap["user_first"].' '.$cap["user_last"],
		"prompt"=>$cap["post_prompt"],
		"id"=>$cap["post_id"]
	);
	$manager->addNotification($cap["post_to"], Manager::RELEASED_PERSONAL, 'releasePersonal', $data);
}

// Gather up all of the group capsules that were released in the
// last 15 minutes (script runs every 15 minutes)
$sql->query("
SELECT *
FROM `group_prompts` as `p`
LEFT JOIN `groups` as `g`
ON `p`.`gpr_gid`=`g`.`group_id`
WHERE `p`.`gpr_release`>'".(time()-60*15)."' AND `p`.`gpr_release`<='".time()."'");
foreach($sql->fetchAll() as $prmt) {
	$sql->select("group_posts","*","WHERE `gpo_pid`='".$prmt["gpr_id"]."'");
	foreach($sql->fetchAll() as $cap) {
		$data = Array(
			"name"=>$prmt["group_name"],
			"group_id"=>$prmt["gpr_gid"],
			"post_id"=>$cap["gpo_id"]
		);
		$manager->addNotification($cap["gpo_uid"], Manager::RELEASED_GROUP, 'releaseGroup', $data);
	}
}

if(constant('DEBUG') !== TRUE) {
	ob_end_clean();
} else {
	ob_end_flush();
}
?>