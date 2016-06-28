<?php
$group = $data["group"][0];

//Set variable of whether user is owner or not
$results = $sql->select("user_owns_groups", "*", "WHERE `group_id`='{$group[group_id]}' and `user_id`='{$user->get("id")}'");
$row = $sql->fetchAll();
$isOwner = $row[0][user_role];

//Get group owner id
$results = $sql->select("user_owns_groups", "*", "WHERE `group_id`='{$group[group_id]}' and `user_role`='owner'");
$row = $sql->fetchAll();
$ownerId = $row[0][user_id];
?>
<script type="text/javascript">
Timesules.groups.init({
	id:"<?php echo $group["group_id"];?>",
	name:"<?php echo $group["group_name"];?>",
	isAdmin:"<?php echo ($isOwner=="owner"? true : false)?>"
});
</script>
<?php
$this->load("main_header", $data);
?>
<div id="centerContent">
 <div id="groupsButtons">
  <a href="/groups.php?create"><button class="toggle-button">CREATE GROUP</button></a>
  <?php
if($isOwner == "owner") echo '<a href="/groups.php?rename='.$group["group_id"].'"><button class="toggle-button">RENAME GROUP</button></a>';
else echo '<button class="toggle-button disabled">RENAME GROUP</button>';
?>
  <?php
if($isOwner == "owner") echo '<a href="/groups.php?prompt='.$group["group_id"].'"><button class="toggle-button">CREATE TIMESULE</button></a>';
else echo '<button class="toggle-button disabled">CREATE TIMESULE</button>';
?>
 </div>

 <div id="groupsContent">
  <div id="groupName"><?php echo $group["group_name"];?></div>
  <div class="table" id="capsuleList">
<?php
if($group["group_id"]!="") {
?>
   <div class="table-row header">
    <div class="col1">Timesules</div><div>|</div>
    <div class="col2">Lock Date</div><div>|</div>
    <div class="col3">Release Date</div>
   </div>
<?php

  $sql->select("group_has_capsules","*","WHERE `group_id`='{$group["group_id"]}'");
  $rows = $sql->fetchAll();

  $groupCapsuleIds = array();
  foreach ($rows as $row) {
    array_push($groupCapsuleIds, $row[cap_id]);
  }



  $groupCapsules = array();
  foreach ($groupCapsuleIds as $groupCapsuleId) {
    $sql->select("capsules","*","WHERE `cap_id`='{$groupCapsuleId}'");
    $row = $sql->fetch();
    array_push($groupCapsules, $row);
  }

	foreach($groupCapsules as $timesule) {
		echo '<div class="table-row blank-row"><div></div></div>
     <div class="table-row">
      <div class="col1"><a href="/groups.php?g='.$group["group_id"].'&p='.$timesule["cap_id"].'">'.$timesule["cap_title"].'</a></div><div></div>
      <div class="col2">'.$timesule["cap_lock"].'</div><div></div>
      <div class="col3">'.$timesule["cap_release"].'</div>
     </div>';
	}
} else {
	$adminMessage = 'Would you like to <a href="/groups.php?capsule='.$group["group_id"].'">Create a Group Timesule</a>?';
	$defaultMessage = "Check back later.";
	echo '<div class="table-row"><div>There are no Timesules for this group at this time. '.(($isOwner=="owner")?$adminMessage:$defaultMessage).'</div></div>';
}

?>
  </div>
  <div id="errorBlock"></div>
  <div id="groupDetails">
   <div id="groupTabs"><div id="capsuleDetailsTab" class="active">Capsule Details</div><div id="membersTab">Members</div></div>
   <div id="capsuleDetailsBlock">
<?php
if(isset($_GET['p'])) {
	// Find the correct prompt
	$timesule = NULL;
	$sql->select("capsules","*","WHERE `cap_id`='{$_GET['p']}'");
  $timesule = $sql->fetch();

	if($timesule != NULL) {
		echo '<div id="capsulePrompt">'.$timesule['cap_title'].'</div>
 <div class="capsuleMessage">'.$timesule["cap_msg"].'</div>
 <div class="table" id="responseList">
  <div class="table-row header">
   <div class="col1">User</div><div>|</div>
   <div class="col2">Submit Date</div><div>|</div>
   <div class="col2">Content</div><div></div>
  </div>';
		$replied = false;
    $sql->select("attachments","*","WHERE `cap_id`='{$_GET['p']}'");
    $attachments = $sql->fetchAll();

    $current = date('Y-m-d H:i:s ', time());
    $capLock = $timesule["cap_lock"];
    $capRelease = $timesule["cap_release"];

    $isLocked = ($capLock < $current) && ($current < $capRelease);
    $isReleased = ($capRelease < $current);

		if(sizeof($attachments) > 0) {
			foreach($attachments as $attachment) {
        $sql->select("users","*","WHERE `user_id`='{$attachment["user_id"]}'");
        $attachUser = $sql->fetch();

				if($isReleased) {
          echo '  <div class="table-row disabled">
          <div class="col1"><span class="fake-link" id="view-cap-'.$attachment["attachment_id"].'">'.$attachUser["user_first"].' '.$attachUser["user_last"].'</span></div><div></div>
          ';
          echo '   <div class="col2">'.$attachment["attach_time"].'</div><div></div>
          <div class="col3">'.$attachment["attachment_text"].'</div>
          </div>';
        }
				else {
					echo '  <div class="table-row disabled">
          <div class="col1">'.$attachUser["user_first"].' '.$attachUser["user_last"].'</div><div></div>
          ';
        	echo '   <div class="col2">'.$attachment["attach_time"].'</div><div></div>
          <div class="col3">Content Hidden</div>
          </div>';
        }
			}
		}



		if(!$isLocked && !$isReleased) {
			echo '  <div class="table-row">
        <div class="col1">'.$user->get("first").' '.$user->get("last").'</div><div></div>
        <div class="col2"><a href="/groups.php?reply='.$timesule["cap_id"].'">(+) ADD </a></div><div></div>
        <div class="col3"></div>
        </div>';
    }
		else if($isLocked && !$isReleased) {
			echo '  <div class="table-row disabled">
        <div class="col1"></div><div></div>
        <div class="col2">Timesule Locked</div><div></div>
        <div class="col3"></div>
        </div>';
    }
    else if($isReleased) {
      echo '  <div class="table-row disabled">
        <div class="col1"></div><div></div>
        <div class="col2">Timesule Has Been Released</div><div></div>
        <div class="col3"></div>
        </div>';
    }
		else if(sizeof($attachments) <= 0) {
			echo '  <div class="table-row">
        <div class="col1">'.$user->get("first").' '.$user->get("last").'</div><div></div>
        <div class="col2">No Reponse</div><div></div>
        <div class="col3"></div>
        </div>';
    }
		echo ' </div>';
	} else
		echo '<div id="capsulePrompt">The prompt requested could not be found. Please choose a Timesule from the list above.</div>';
} else {
	echo '<div id="capsulePrompt">Please choose a Timesule from above to view more details!</div>';
}
/*    <div id="capsulePrompt">This is a capsule prompt</div>
    <div id="capsuleMessage">This is some more information about this capsule.</div>
    <div class="table" id="responseList">
     <div class="table-row header">
      <div class="col1">User</div><div>|</div>
      <div class="col2">Submit Date</div><div>|</div>
      <div class="col3">Word Count</div>
     </div>
     <div class="table-row">
      <div class="col1">Romanola</div><div></div>
      <div class="col2">7/17/12</div><div></div>
      <div class="col3">20</div>
     </div>
     <div class="table-row">
      <div class="col1">Hadidotj</div><div></div>
      <div class="col2">(+) ADD REPLY</div><div></div>
      <div class="col3"></div>
     </div>
    </div>*/
?>
   </div>
   <div id="membersBlock">
    <div style="position: absolute;bottom:5px; left: 5px;color:#494B4D">To add group members, drag a contact from the right and drop them above. Drag them away to remove them.</div>
    <div id="contactsDrop">
<?php
      $user_owns_groups = $sql->select("user_owns_groups", "*", "WHERE `group_id`='{$group[group_id]}'");
      $user_owns_groups = $sql->fetchAll();
      $allGroupUsersIds = array();
      foreach ($user_owns_groups as $groupUser) {
        array_push($allGroupUsersIds, $groupUser[user_id]);
      }

      $allGroupUsers = array();
      foreach ($allGroupUsersIds as $userId) {
        $tempUser = $sql->select("users", "*", "WHERE `user_id`='$userId'");
        $tempUser = $sql->fetch();
        array_push($allGroupUsers, $tempUser);
      }


foreach($allGroupUsers as $groupUser) {
	echo '<span id="promptCT-'.$groupUser["user_id"].'"'.(($groupUser["user_id"] != $ownerId)?'':' class="groupAdmin"').'><img src="'.$manager->getAvatar($groupUser["user_avatar"],false).'" class="avatar32" /> '.$groupUser["user_first"].' '.$groupUser["user_last"].''.(($groupUser["user_id"]==$ownerId)?' (Owner)':'').'</span>';
}


?>
    </div>
   </div>
  </div>
 </div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("reverse","closeContacts"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
?>
