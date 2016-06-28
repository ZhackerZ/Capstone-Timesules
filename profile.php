<?php
/**
 * Timesules user profile page.
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
define('IN_PROFILE', true);
require_once 'source/startup.php';

$manager->requireLogin();

$id = $sql->escape($_GET["user"]);
$sql->select("users", "`user_id`,`user_first`,`user_middle`,`user_last`,`user_email`,`user_age`,`user_gender`,`user_avatar`", "WHERE `user_id`='{$id}'");
$row = $sql->fetch();


$contacts = $sql->select("user_has_contacts", "*", "WHERE `user_id`='{$user->get("id")}' OR `contact_id`='{$user->get("id")}'");
$contacts = $sql->fetchAll();
$myContactIds = array();

$row["isContact"] = true;
$row["id"] = $id;

foreach($contacts as $cont) {
  if($cont == $id){
    $row["isContact"] = true;
  }
}
//require 'user_index.php';


// GET THE CAPSULES CONTACT WAS
$cutoff_date = date('Y-m-d', mktime(0, 0, 0, date('m')-1, date('d'), date('Y')));
$today = date("Y-m-d H:i:s", time());

$rec_cap_ids = $sql->select("capsules", "*", "WHERE `cap_email_to`='{$row["user_email"]}' AND (`cap_release`<='{$today}' AND `cap_release`>='{$cutoff_date}')");
$rec_cap_ids = $sql->fetchAll();

$capsuleCount = count($rec_cap_ids);

// Now print out the capsules
if($capsuleCount > 0) {
  foreach($rec_cap_ids as $cap) {
   echo '  <div class="capsuleBlock">
   <div class="capsulePrompt">'.$cap["title"].'</div>
   ';
   if(!defined('IN_PROFILE'))
    echo '<div class="capsuleAvatar">'.$manager->getAvatar($cont["id"]).'</div>
  ';

  echo '<div class="capsuleInfo">From: '.$cap["cap_email_from"].'<br />Date: '.$cap["cap_release"].'</div>
  <div class="capsuleSummary">'.$manager->getSummary($cap["cap_msg"], 75).'</div>
  <div class="capsuleLink"><span class="fake-link" id="view-cap-'.$cap["cap_id"].'">See full capsule</span></div>
</div>
';
}
} else if($start == 0) {
  echo '  <div class="capsuleBlock">There are no released capsules to view at this time.</div>';
}


// require 'user_index.php';
// $row["capsuleCount"] = $capsuleCount-1;
// $row["nowContacts"] = FALSE;

// if(!is_null($_SESSION["nowContacts"]) && $_SESSION["nowContacts"] == $id) {
// 	$_SESSION["nowContacts"] = NULL;
// 	$row["nowContacts"] = TRUE;
// }

$theme->load("user_profile", $row);
?>
