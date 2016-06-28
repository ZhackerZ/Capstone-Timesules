<?php
/**
 * Timesules calendar.
 * 
* @author Tyler Hadidon Yuzhen Liu
 * @copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

$manager->requireLogin();

// 1,13
$date = explode(":",date("n:y"));
if(!is_null($_GET["m"])) {
	$date = explode(":", $_GET["m"]);
}

// Get the date info
$monthNames = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
$monthStartTime = strtotime("{$date[0]}/1/{$date[1]} 00:00:00");
$thisMonthStr = implode(":", $date);
$thisMonth = date("F Y", $monthStartTime);
$infoThisMonth = explode(":",date("t:N", $monthStartTime));
$infoThisMonth[1] = ($infoThisMonth[1]==7)?0:$infoThisMonth[1];
$lastMonth = ($date[0]-2<0)?Array($monthNames[11],12,$date[1]-1):Array($monthNames[$date[0]-2],$date[0]-1,$date[1]);
$nextMonth = ($date[0]>11)?Array($monthNames[0],1,$date[1]+1):Array($monthNames[$date[0]],$date[0]+1,$date[1]);
$monthEndTime = strtotime("{$date[0]}/{$infoThisMonth[0]}/{$date[1]} 23:59:59");
$data = Array("tmon"=>$thisMonth,"itmon"=>$infoThisMonth,"lmon"=>$lastMonth,"nmon"=>$nextMonth);

// Grab all capsules schedule for release and lock this month
//$sql->setDebugging(true);
$sql->query("SELECT
	`p`.*,`p`.`post_id` AS `id`,`p`.`post_msg` AS `msg`,`p`.`post_prompt` AS `prompt`,
	CONCAT(`u`.`user_first`,`u`.`user_last`) AS `author`
	FROM `posts` AS `p`
	LEFT JOIN `user` AS `u`
	ON `p`.`post_user`=`u`.`user_id`
	WHERE
	(FIND_IN_SET('{$user->get("id")}',`p`.`post_to`)!=0 OR `p`.`post_user`='{$user->get("id")}') AND
	`p`.`post_draft`='0' AND
	(`p`.`post_lock` BETWEEN '{$monthStartTime}' AND '{$monthEndTime}' OR
	`p`.`post_release` BETWEEN '{$monthStartTime}' AND '{$monthEndTime}')
	"
);
$caps = $sql->fetchAll();

$groups = $user->get("groups");
$groups = $groups["list"];
$sql->query("SELECT
	`p`.*,`r`.*,
	`p`.`gpo_id` AS `id`,`p`.`gpo_msg` AS `msg`,`r`.`gpr_prompt` AS `prompt`,
	`g`.`group_name` AS `name`
	FROM `group_posts` AS `p`
	LEFT JOIN `group_prompts` AS `r` ON `p`.`gpo_pid`=`r`.`gpr_id`
	LEFT JOIN `groups` AS `g` ON `g`.`group_id`=`r`.`gpr_gid`
	WHERE
	`r`.`gpr_gid` IN({$groups}) AND
	`p`.`gpo_uid`='{$user->get("id")}' AND
	(`r`.`gpr_lock` BETWEEN '{$monthStartTime}' AND '{$monthEndTime}' OR
	`r`.`gpr_release` BETWEEN '{$monthStartTime}' AND '{$monthEndTime}')
	"
);
$caps = array_merge($caps, $sql->fetchAll());

// Now place them in a lovely array for print out :)
$capsuleList = Array();
foreach($caps as $cap) {
	$msg = $cap["prompt"]."<hr />".(($cap["gpr_release"]!='')?"Group: ".$cap["name"]:"From: ".$cap["author"])."<br /><br />";
	$released = true;
	if($cap["gpr_release"]>time() || $cap["post_release"]>time()) {
		$msg .= "This capsule has not yet been released! It will be released on:<br />".date("D, M j, Y \\a\\t g:ia", (($cap["gpr_release"]!='')?$cap["gpr_release"]:$cap["post_release"]));
		$released = false;
	} else
		$msg .= $manager->getSummary($cap["msg"],30);
	if($cap["gpr_release"] != "" && date("n:y", $cap["gpr_release"]) == $thisMonthStr)
		$capsuleList[date("j",$cap["gpr_release"])][] = Array(0, 'gr-'.$cap["id"], $msg, $released);
	if($cap["post_release"] != "" && date("n:y", $cap["post_release"]) == $thisMonthStr)
		$capsuleList[date("j",$cap["post_release"])][] = Array(1, 'pr-'.$cap["id"], $msg, $released);
	if($cap["gpr_lock"] != "" && date("n:y", $cap["gpr_lock"]) == $thisMonthStr)
		$capsuleList[date("j",$cap["gpr_lock"])][] = Array(2, 'gl-'.$cap["id"], $msg, $released);
	if($cap["post_lock"] != "" && date("n:y", $cap["post_lock"]) == $thisMonthStr)
		$capsuleList[date("j",$cap["post_lock"])][] = Array(3, 'pl-'.$cap["id"], $msg, $released);
}

$theme->load("calendar_page", Array("title"=>"Calendar","monthInfo"=>$data,"capsules"=>$capsuleList));
?>