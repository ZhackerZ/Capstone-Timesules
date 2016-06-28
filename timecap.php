<?php
/**
 * Timesules capsule list.
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
//removed SENT, PENDING
// define('MODE_SENT',1);
// define('MODE_PENDING', 5);

define('IN_TIMESULES', true);
define('MODE_ALL', 0);
define('MODE_DRAFT', 2);
define('MODE_RELEASED', 3);
define('MODE_LOCKED', 4);

require_once 'source/startup.php';

$manager->requireLogin();

$today = date("Y-m-d H:i:s", time());

// Check to see if we are deleting
if(isset($_POST["delete"])) {
	$sql->connect();
	header("Content-Type: text/json");

	// TODO: Implement removing of Timecapsules from list
	// Things to think about when doing this
	// 1) What if it is a draft? It should probably be DELETED
	// 2) What if the owner of the post also is in the TO and removes it?
	$list = $sql->escape(implode(",",$_POST["list"]));
	$mode = $_POST["timesule"];
	$count = $sql->select("capsules",
   	"`cap_id`,`cap_to`",
	"WHERE (FIND_IN_SET('{$user->get("id")}',`cap_to`) OR `cap_user`='{$user->get("id")}')
	AND `cap_release`<=UNIX_TIMESTAMP()
	AND `cap_id` IN ({$list})
	AND `cap_draft`='".(($mode==MODE_DRAFT)?"1":"0")."'"
	);

	switch($mode) {
		// If it is a capsule sent by us, just set the hide flag.
		case MODE_SENT:
   			$delete = "";
  			while(($row=$sql->fetch()) !== FALSE) $delete .= "{$row["cap_id"]},";
				$delete = substr($delete,0,-1); // Remove last comma (,)
				if(($count = $sql->update("capsules","`cap_hidden`='1'","WHERE `cap_user`='{$user->get("id")}' AND `cap_id` IN ({$delete})")) !== FALSE) {
					exit('{"code":200,"msg":"Removed '.$count.' from sent list!","count":'.$count.'}');
			}
		break;
				
		// If it is a draft, actually delete it from the database (to save size space)
		case MODE_DRAFT:
			$delete = "";
			while(($row=$sql->fetch()) !== FALSE) $delete .= "{$row["cap_id"]},";
				$delete = substr($delete,0,-1); // Remove last comma (,)
				if(($count = $sql->delete("capsules", "`cap_user`='{$user->get("id")}' AND `cap_draft`='1' AND `cap_id` IN ({$delete})")) !== FALSE) {
					exit('{"code":200,"msg":"Deleted '.$count.' drafts!","count":'.$count.'}');
			}
		break;

		// If it is a capsule in which we are in the "to" field, just remove our ID
		default:
			$count = 0;
			$capsules = $sql->fetchAll();
			foreach($capsules as $row) {
				// Remove the user's ID from the TO list
				$to = explode(",", $row["cap_to"]);
				$index = array_search($user->get("id"), $to);
				array_splice($to, $index, 1);
				$set = "`cap_to`='".implode(",", $to)."'";
				if($sql->update("capsules",$set,"WHERE FIND_IN_SET('{$user->get("id")}',`cap_to`) AND `cap_id`='{$row["cap_id"]}'") !== FALSE) {
					$count++;
				}
			}
	
			if($count > 0)
				exit('{"code":200,"msg":"Removed '.$count.' from time capsules list!","count":'.$count.'}');
		break;
	}

	echo '{"code":500,"count":0,"msg":"Error occured while updating the database. Please try again."}';
	exit;
}


//GET REQUEST FOR TIMESULES
// if(isset($_GET["timesule"]) && $_GET["timesule"] == MODE_SENT) {
//   //query for capsules sent by me
//   // $sql->select("capsules","*","WHERE `cap_email_from`='{$user->get("email")}' AND `cap_hidden`='0' AND `cap_draft`='0' ORDER BY `cap_title` ASC");
//   $capsules = $sql->select("capsules","*","WHERE `cap_email_from`='{$user->get("email")}' ORDER BY `cap_title` ASC");
// } else

if(isset($_GET["timesule"])){
	$sql->connect();
	if($_GET["timesule"] == MODE_DRAFT) {
		// Query for my capsules in draft status
		$capsules = $sql->select("capsules","*","WHERE `cap_email_to`='{$user->get("email")}' AND `cap_hidden`='0' AND `cap_draft`='1' ORDER BY `cap_title` ASC");
		$capsules = $sql->fetchAll();
		$capsulecount = count($capsules);
		$cap_result = array("capresult"=>$capsules);	
		
		/*********************************************************************/
		/****** Draft *******************************************************/
		/*********************************************************************/
		foreach($capsules as $capsule) {
		
			$hide = "id=\"capsuleID-{$capsule["cap_id"]}\""; ?>
		
			<div class="timecapBlock" <?php echo $hide; ?>>
			<div class="locked"><img src="/source/templates/images/capsule.png" /></div>
			<div class="timecapPrompt"><?php echo $capsule["cap_title"]; ?></div>
			<div class="timecapInfo">To: <?php echo $capsule["cap_email_to"]; ?><br />
			<!--    <div class="timecapInfo">From: <?php //echo $capsule["cap_email_from"]; ?><br />-->
			Date: <?php echo $capsule["cap_release"]; ?> </div>
			<div class="timecapPreview"><?php echo $manager->getSummary($capsule["cap_msg"]); ?></div>
			<div class="timecapLink"><a href="/capsule.php?capsule=<?php echo $capsule["cap_id"]; ?>&edit=true">Edit capsule</a></div>
			</div>
		    <?php }
	} else {
		// Query for all my sent capsules
		$capsules = $sql->select("capsules","*","WHERE `cap_email_to`='{$user->get("email")}' ORDER BY `cap_title` ASC");
		$capsules = $sql->fetchAll();
		$capsulecount = count($capsules);
		$cap_result = array("capresult"=>$capsules);
	
		foreach($capsules as $capsule) {
	
			$hide = "id=\"capsuleID-{$capsule["cap_id"]}\"";
			/*********************************************************************/
			/****** Locked *******************************************************/
			/*********************************************************************/
	
			if($capsule["cap_release"] > $today) {
				$hide .= ' style="display: none;"'; ?>
	
				<div class="timecapBlock locked" <?php echo $hide; ?>>
				<div class="locked"><img src="/source/templates/images/capsule.png" /></div>
				<div class="timecapPrompt"><?php echo $capsule["cap_title"]; ?></div>
				<div class="timecapInfo">To: <?php echo $capsule["cap_email_to"]; ?><br />
				<!--    <div class="timecapInfo">From: <?php //echo $capsule["cap_email_from"]; ?><br />
				 -->  Date: <?php //echo date('m/d/y \a\t g:i a', $capsule["cap_release"]);
				 echo $capsule["cap_release"]; ?>
				</div>
				<div class="timecapPreview">&nbsp;</div>
				<div class="timecapLink"><span class="fake-link">See full capsule</span></div>
				</div>
				<?php
				/*********************************************************************/
				/****** Released *****************************************************/
				/*********************************************************************/
			}
	
			if($capsule["cap_release"] <= $today) { ?>
				<div class="timecapBlock active" <?php echo $hide; ?>>
	 			<div class="timecapActive"></div>
	 			<div class="timecapPrompt"><?php echo $capsule["cap_title"]; ?></div>
	 			<div class="timecapInfo">To: <?php echo $capsule["cap_email_to"]; ?><br />
				<!--    <div class="timecapInfo">From: <?php //echo $capsule["cap_email_from"]; ?><br />
	 			-->    Date: <?php //echo date('m/d/y \a\t g:i a', $capsule["cap_release"]);
				 echo $capsule["cap_release"]; ?>
				</div>
				<div class="timecapPreview"><?php echo $manager->getSummary($capsule["cap_msg"]); ?></div>
				<div class="timecapLink"><a href="/capsule.php?capsule=<?php echo $capsule["cap_id"]; ?>">See full capsule</a></div>
				</div>
				<?php
			}
		} // End foreach
	} // End if
}
$theme->load("timecap_page", Array("title"=>"Time Capsules", "count"=>$capsulecount, "cap_result"=>$cap_result));
?>