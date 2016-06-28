<?php
$this->load("main_header", $data);
echo $data["message"];
?>
<div id="centerContent">
 <div id="groupsButtons">
  <a href="/groups.php?create"><button class="toggle-button">CREATE GROUP</button></a>
 </div>

 <div id="groupsContent">
  <div class="groupsContainer">
<?php
if(isset($_GET['create'])) {
	if(isset($_GET['message'])) echo '<div class="ui-error">'.$data["message"].'</div>';
?>
   <form action="/groups.php?create" method="POST" class="groupsCreateForm">
    Group Name: &nbsp;&nbsp;&nbsp;<input type="text" name="group_name" size="50" /><br />
    <input class="submit-button" type="submit" name="doit" value="Create Group!" />
   </form>
<?php
} else if(isset($_GET['rename'])) {
	if(isset($_GET['message'])) echo '<div class="ui-error">'.$data["message"].'</div>';
?>
   <form action="/groups.php?rename" method="POST" class="groupsCreateForm">
    Group Name: &nbsp;&nbsp;&nbsp;<input type="text" name="group_name" size="50" /><br />
    <input type="hidden" name="gid" value="<?php echo $data["groupId"];?>" />
    <input class="submit-button" type="submit" name="doit" value="Rename Group" />
   </form>
<?php
} else if(isset($_GET['prompt'])) {
	if(isset($_GET['message'])) echo '<div class="ui-error">'.$data["message"].'</div>';
	
  if (isset($_POST['prompt'])) {
    $prompt = $_POST["prompt"];
  }
  else {
    $prompt = null;
  }
	
  if (isset($_POST['vis'])) {
    $vis = ($_POST["vis"] == "1")?"1":"0";
  }
  else {
    $vis = null;
  }
	
	
	if(isset($_POST['lock']) && $_POST["lock"]) {
		$lock = $_POST["lock"];
		$lockDate = Array($lock["month"],$lock["date"],$lock["year"],$lock["time"],$lock["meridiem"]);
	} else
		$lockDate = explode(" ", date('n j Y g i A', time()+3600*24));
	
	$release = false;
	if(isset($_POST['release']) && $_POST["release"]) {
		$release = $_POST["release"];
		$releaseDate = Array($release["month"],$release["date"],$release["year"],$release["time"],$release["meridiem"]);
	} else
		$releaseDate = explode(" ", date('n j Y g i A', time()+3600*24*30));
?>
   <form action="/groups.php?prompt=<?php echo $data["prompt"]["group_id"];?>" method="POST" class="groupsCreateForm">
    Group Timesule: &nbsp;&nbsp;&nbsp;<input type="text" name="prompt" size="50" value="<?php echo $prompt; ?>" /><br />

    <div style="margin-bottom: 20px;">
    Lock Day/Time &nbsp;
    <select name="lock[month]">
<?php
$months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC");
foreach($months as $key=>$month) echo "<option value=\"".($key+1)."\"".(($key==$lockDate[0]-1)?" selected=\"selected\"":"").">{$month}</option>\n";
?>
    </select> &nbsp;
    <select name="lock[date]">
<?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\"".(($i==$lockDate[1])?" selected=\"selected\"":"").">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
    </select> &nbsp;
    <select name="lock[year]">
<?php $thisYear = date("Y"); for($i=$thisYear;$i<$thisYear+100;$i++) echo "<option value=\"{$i}\"".(($i==$lockDate[2])?" selected=\"selected\"":"").">{$i}</option>\n"; ?>
    </select> &nbsp;
    <div class="separator"></div> &nbsp;
    <select name="lock[time][hour]">
<?php
for($i=1;$i<=12;$i++) {
  echo "<option value=\"".$i."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">{$i}</option>\n";
  
}
?>
    </select> &nbsp;

:

    <select name="lock[time][minute]">

<?php
for($j=0; $j<=59; $j++){
    
  if($j<10){
    echo "<option value=\"".$j."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">0{$j}</option>\n";
  }
  else{
    echo "<option value=\"".$j."\"".((($i==$lockDate[3] && $lockDate[4] < 15))?" selected=\"selected\"":"").">{$j}</option>\n";
  }
}
?>
    </select> &nbsp;

    <select name="lock[meridiem]">
     <option value="AM"<?php if($lockDate[5] == "AM") echo " selected=\"selected\""; ?>>AM</option>
     <option value="PM"<?php if($lockDate[5] == "PM") echo " selected=\"selected\""; ?>>PM</option>
    </select>
   </div>
   <div style="margin-bottom: 20px;">
    Release Day/Time &nbsp;
    <select name="release[month]">
<?php foreach($months as $key=>$month) echo "<option value=\"".($key+1)."\"".(($key==$releaseDate[0]-1)?" selected=\"selected\"":"").">{$month}</option>\n"; ?>
    </select> &nbsp;
    <select name="release[date]">
<?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\"".(($i==$releaseDate[1])?" selected=\"selected\"":"").">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
    </select> &nbsp;
    <select name="release[year]">
<?php for($i=$thisYear;$i<$thisYear+100;$i++) echo "<option value=\"{$i}\"".(($i==$releaseDate[2])?" selected=\"selected\"":"").">{$i}</option>\n"; ?>
    </select> &nbsp;
    <div class="separator"></div> &nbsp;
    <select name="release[time][hour]">
<?php
for($i=1;$i<=12;$i++) {
  echo "<option value=\"".$i."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">{$i}</option>\n";
  
}
?>
    </select> &nbsp;

:

    <select name="release[time][minute]">

<?php
for($j=0; $j<=59; $j++){
    
  if($j<10){
    echo "<option value=\"".$j."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">0{$j}</option>\n";
  }
  else{
    echo "<option value=\"".$j."\"".((($i==$lockDate[3] && $lockDate[4] < 15))?" selected=\"selected\"":"").">{$j}</option>\n";
  }
}
?>
    </select> &nbsp;
    <select name="release[meridiem]">
     <option value="AM"<?php if($releaseDate[5] == "AM") echo " selected=\"selected\""; ?>>AM</option>
     <option value="PM"<?php if($releaseDate[5] == "PM") echo " selected=\"selected\""; ?>>PM</option>
    </select>
   </div>
   
    Prompt Description/Notes:<br />
    <textarea name="des"><?php if (isset($_POST["des"])) {echo $_POST["des"];}  ?></textarea><br />
    <div style="margin-bottom:30px;">
     <input type="radio" name="vis" value="1" id="Public"<?php if($vis == 1) echo " checked=\"checked\""; ?> /> <label for="Public">Public</label>
     <input type="radio" name="vis" value="0" id="Private"<?php if($vis != 1) echo " checked=\"checked\""; ?> /> <label for="Private">Private</label>
    </div>
    <input type="hidden" name="gid" value="<?php echo $data["prompt"]["group_id"];?>" />
    <input class="submit-button" type="submit" name="doit" value="Create Group Timesule" />
   </form>
<?php
} else if(isset($_GET['reply'])) {
	if(isset($data["message"])) echo '<div class="ui-error">'.$data["message"].'</div>';
?>
   <form action="/groups.php?<?php echo (!isset($data["edit"]))?"reply":"edit";?>" method="POST" class="groupsCreateForm">
   Timesule: <span class="groupReplyPrompt"><input type="text" disabled="disabled" size="50" value="<?php echo $data["reply"]["cap_title"]; ?>" /></span><br />
   Lock: <span class="groupReplyLock"><input type="text" disabled="disabled" size="20" value="<?php echo $data["reply"]["cap_lock"]; ?>" /></span> &nbsp;&nbsp;&nbsp;
   Release: <span class="groupReplyRelease"><input type="text" disabled="disabled" size="20" value="<?php echo $data["reply"]["cap_release"]; ?>" /></span><br />
   Message:<br />
<?php

$params = Array(
	"name"=>"msg",
	"placeholder"=>"Enter message here...",
	"class"=>"groupsReplyForm",
	"text"=>""
);
$theme->load("formatBar",$params,false);
?><br />
    <input type="hidden" name="pid" value="<?php echo $data["reply"]["cap_id"];?>" />
    <input class="submit-button" type="submit" name="doit" value="<?php echo (!isset($data["edit"]))?"Reply to Timesule":"Edit Response";?>" />
   </form>
<?php
} else if(isset($_GET['error'])) {
	echo '<div class="ui-error">'.$data["error"].'</div>';
} else {
  $user_groups = $sql->select("user_owns_groups", "*", "WHERE `user_id`='{$user->get("id")}'");
  $user_groups = $sql->fetchAll();

  $allGroupIds = array();
  foreach ($user_groups as $group) {
    array_push($allGroupIds, $group[group_id]);
  }

  $allUserGroups = array();
  foreach ($allGroupIds as $groupId) {
      $group = $sql->select("groups", "*", "WHERE `group_id`='{$groupId}'");
      $group = $sql->fetch();

      array_push($allUserGroups, $group);
  }

	$out = "You are not apart of any groups. Would you like to <a href='/groups.php?create'>Create a Group</a>?";

	if(count($allUserGroups) > 0) {
		$out = (isset($_GET['message']))?$data["message"]:"   Choose a group to view:<br />";
		foreach($allUserGroups as $group) {
			$out .= '<div><a href="/groups.php?g='.$group['group_id'].'">'.$group['group_name'].'</a></div>'; 
		}
	}
	echo $out;
}
?>
  </div>
  <!-- <div id="groupDetails" style="background-color: #EEE;">
   <div id="groupTabs"><div id="capsuleDetailsTab" class="disabled">Capsule Details</div><div id="membersTab" class="disabled">Members</div></div>
  </div> -->
 </div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("reverse","closeContacts"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
exit;
?>