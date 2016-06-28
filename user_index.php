<?php
/**
 * Timesules logged in index. It was cleaner to do it this way.
 *
 * @author Tyler Hadidon Yuzhen Liu
 * @copyright 2015
 */
// NOTE: This document is loaded by the index.php
if(!defined('IN_TIMESULES'))
	exit;

// Figure out what capsules to load
$start;
if(isset($_GET["loadCapsules"])) {
	$start = $_GET["loadCapsules"];
}
$length = 21; // MUST BE > 1
if(!isset($_GET["loadCapsules"])) {
	$start = 0;
	$_SESSION["timesulesHomeLoadTime"] = time();
} else if($start < 0)
$start = 0;
$startTime = $_SESSION["timesulesHomeLoadTime"];

$onlyFrom;
if(isset($_GET["u"])) {
	$onlyFrom = $sql->escape($_GET["u"]);
}

// // GET THE CAPSULES I SENT
// $my_sent_cap_ids = $sql->select("user_has_capsules", "cap_id", "WHERE `user_id`='{$user->get("id")}'");
// $my_sent_cap_ids = $sql->fetchAll();

// $caps = array();

// for($my_sent_cap_ids as $cap){
//   $cap_item = $sql->select("capsules","*","WHERE `cap_id`='{$cap}'");
//   $cap_item = $sql->fetch();
//   array_push($caps, $cap_item);
// }


// GET THE CAPSULES I WAS SENT
$cutoff_date = date('Y-m-d', mktime(0, 0, 0, date('m')-1, date('d'), date('Y')));
$today = date("Y-m-d H:i:s", time());

$my_rec_cap_ids = $sql->select("capsules", "*", "WHERE `cap_email_to`='{$user->get("email")}' AND (`cap_release`<='{$today}' AND `cap_release`>='{$cutoff_date}')");
$my_rec_cap_ids = $sql->fetchAll();


// $contacts = $user->get("contacts");
// $sql->query("SELECT
// `u`.`user_first`,`u`.`user_last`,`p`.*,

// ##########################
// (SELECT COUNT(*) FROM `posts`
// WHERE
// `post_release`<='".time()."' AND
// `post_release`>'".$startTime."' AND
// `post_draft`='0' AND
// FIND_IN_SET('{$user->get("id")}',`p`.`post_to`) OR	#Check that we are in the TO field (OR)
// (`p`.`post_vis`='1' AND FIND_IN_SET(`p`.`post_user`, '{$contacts["list"]}')) #If the post is public and we are a contact
// ) AS `newPosts`
// ##########################

// FROM `posts` AS `p`
// LEFT JOIN `user` AS `u`
// ON `p`.`post_user`=`u`.`user_id`

// WHERE
// `p`.`post_release`<='".$startTime."' AND	#Check that is has been released
// `p`.`post_draft`='0' AND					#Check that it is not a draft
// (FIND_IN_SET('{$user->get("id")}',`p`.`post_to`) OR	#Check that we are in the TO field (OR)
// (`p`.`post_vis`='1' AND FIND_IN_SET(`p`.`post_user`, '{$contacts["list"]}'))) #If the post is public and we are a contact

// ORDER BY `p`.`post_release` DESC LIMIT {$start},{$length}
// ");
// $capsuleCount = count($caps);
$capsuleCount = count($my_rec_cap_ids);
// $posts = $sql->fetchAll();

//REMOVED THIS POSTS TABLE IN OLD DB
// Calculate new, previous, more posts
// if (!empty($posts)) {
// 	$newPosts = $posts[0]["newPosts"];
// }
// else {
// 	$newPosts = 0;
// }
// $prevPosts = ($start!=0)?true:false;
// $morePosts = ($capsuleCount==$length)?true:false;

// // Remove last post if there are more posts
// if($morePosts) { array_pop($posts); $capsuleCount--; }

// // If there are new posts, let the user know by printing a message (noscript) or trigger a JS event
// if($newPosts > 0)
// 	echo '<script type="text/javascript">if(Timesules && Timesules.user_home && Timesules.user_home.newPosts) Timesules.user_home.newPosts("'.$newPosts.'");</script>
// <noscript><div><a href="/index.php">Click to view '.$newPosts.' new Capsule'.($newPosts>1?'s':'').'</a></div></noscript>
// ';

// Now print out the capsules
if($capsuleCount > 0) {
  foreach($my_rec_cap_ids as $cap) {
   echo '  <div class="capsuleBlock">
   <div class="capsulePrompt">'.$cap["title"].'</div>
   ';
   if(!defined('IN_PROFILE'))
    echo '<div class="capsuleAvatar">'.$manager->getAvatar($cont["id"]).'</div>
  ';

  echo '<div class="capsuleInfo">From: '.$cap["cap_email_to"].'<br />Date: '.$cap["cap_release"].'</div>
  <div class="capsuleSummary">'.$manager->getSummary($cap["cap_msg"], 75).'</div>
  <div class="capsuleLink"><div id="view-cap-'.$cap["cap_id"].'">See full capsule</div></div>
</div>
';
// <div class="capsuleLink"><div id="view-cap-'.$cap["cap_id"].'">See full capsule</div></div>
//Paul
}
} else if($start == 0) {
	echo '  <div class="capsuleBlock">There are no released capsules to view at this time.</div>';
}

// If it was an ajax call, exit
if((!isset($_GET["ajaxCall"]) || !$_GET["ajaxCall"]=="true") && !defined('IN_PROFILE'))

	$theme->load("user_home", Array("capsuleCount"=>$capsuleCount-1,"newPosts"=>$newPosts,"prevPosts"=>$prevPosts,"morePosts"=>$morePosts,"start"=>$start,"length"=>$length));
?>