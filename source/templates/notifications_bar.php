<div class="leftBar">
 <div class="sidebarGroup">
  <div class="sidebarHeader">Notifications</div>
  <div class="sideContentWrapper" id="notificationsBar">
   <div class="sideContent">
   <div id="notifications" style="overflow-y:scroll;">

    <!--  <div class="notification">
      <div class="noteIcon" style="background-color:#3cb878"></div>
      <div class="noteTitle">Capsule Released</div>
      <div class="noteInfo">Where would you like to be in 10 years?</div>
     </div>
     <div class="noteSeparator"></div>
     <div class="notification">
      <div class="noteIcon" style="background-color:#0077a7"></div>
      <div class="noteTitle">New Contact</div>
      <div class="noteInfo">Tyler Hadidon wants to be contacts!<br /><a href="/contacts.php?accept=1">Accept</a> | <a href="/contacts.php?deny=1">Deny</a></div>
     </div>
     <div class="noteSeparator"></div>
     <div class="notification">
      <div class="noteIcon" style="background-color:#e03c3f"></div>
      <div class="noteTitle">New Group Prompt</div>
      <div class="noteInfo">
       There is a new prompt avaliable in the "English Class" group!<br />
       <a href="/groups.php?g=1&p=1">Reply Now!</a>
      </div>
     </div>
     <div class="noteSeparator"></div>
     <div class="notification">
      <div class="noteIcon groupRelease"></div>
      <div class="noteTitle">Group Capsule Released</div>
      <div class="noteInfo">
        A prompt has been released in the "English Class" group!<br />
       <a href="/groups.php?g=1&p=1">View Now!</a>
      </div>
     </div>
     <div class="noteSeparator"></div> -->

     <?php

         //construct date
         $today = date('Y-m-d H:i:s', time());

       // $id = json_decode($user->get("id"), true);
     // echo "id = ".$id;

// mysql_connect("localhost", "vagrant", "vagrant") or die(mysql_error()); // Connect to database server(localhost) with username and password.
// mysql_select_db("timesules") or die(mysql_error());

// $result = mysql_query("SELECT * FROM notifications WHERE user_id='".$id."'") or die(mysql_error());
// $count = mysql_num_rows($result);

// $notes = mysqli_fetch_assoc($result);

//ONLY SHOW NOTIFICATIONS THAT HAVEN'T BEEN SEEN
     $results = $sql->select("notifications", "*", "WHERE `user_id`='{$user->get("id")}'");
	 //AND `viewed`='0'");
      //var_dump($results);

     if($results > 0) {
       $notifications = $sql->fetchAll();

       foreach($notifications as $note) {
        $type = $note["notification_type"];
        $data = $note["message"];
        //$viewed = $note['viewed'];
		// $time = $note['t'];

        $icon = $title = $info = $color = '';

        switch($type) {
         case Manager::NEW_CONTACT:
         //get friend
         $friend = $sql->select("users","*", "WHERE `user_id`='{$note["friend_id"]}'");
         $friend = $sql->fetch();

         $color = '0077a7';
         $title = 'New Contact Request';
		 $info = $data;//." <br />".'<span id="requestID-accept-'.$friend["user_id"].'" class="fake-link">Accept</span> | <span id="requestID-ignore-'.$friend["user_id"].'" class="fake-link">Ignore</span>';

         //$info = $friend["user_first"]." ".$friend["user_last"].' wants to be contacts!<br />
         //<span id="requestID-accept-'.$friend["user_id"].'" class="fake-link">Accept</span> | <span id="requestID-ignore-'.$friend["user_id"].'" class="fake-link">Ignore</span>';
         break;

         case Manager::NEW_GROUP_CAPSULE:

         $group = $sql->select("groups","*", "WHERE `group_id`='{$note["group_id"]}'");
         $group = $sql->fetch();

         $color = 'e03c3f';
         $title = 'New Group Capsule';
         $info = 'There is a new group capsule available in the "'.$group["group_name"].'" group!<br />
         <a href="/groups.php?g='.$group["group_id"].'&capsule='.$group["cap_id"].'">Reply Now!</a>';
         break;

         // case Manager::RECEIVED_CAPSULE:

         // $cap = $sql->select("capsules","*", "WHERE `cap_id`='{$note["cap_id"]}'");
         // $cap = $sql->fetch();

         // $color = '3cb878';
         // $title = 'Capsule Recieved';
         // $info = '"'.$cap["cap_title"].'" has been received!<br />
         // <span class="fake-link" id="view-cap-'.$note["cap_id"].'">View Now!</span>';
         // break;

         case Manager::RELEASED_CAPSULE:

         $cap = $sql->select("capsules","*", "WHERE `cap_id`='{$note["cap_id"]}'");
         $cap = $sql->fetch();

         // if($cap["cap_release"] < $today){
          $color = '3cb878';
          $title = 'Capsule Released';
          $info = '"'.$cap["cap_title"].'" has been released!<br />
          <span class="fake-link" id="view-cap-'.$note["cap_id"].'">View Now!</span>';
        // }else{
        //   break;
        // }
        break;


        case Manager::RELEASED_GROUP:
         //TODO:

        $group_name = $sql->select("groups", "group_name", "WHERE `group_id`='{$note["group_id"]}'");
        $group_name = $sql->fetch();

        $icon = 'groupReleaseIcon';
        $title = 'Group Capsule Released';
        $info = 'A prompt has been released in the "'.$group_name.'" group!<br />
        <a href="/groups.php?g='.$note["group_id"].'&capsule='.$data["i"].'">View Now!</a>';
        break;

        case Manager::ADDED_TO_GROUP:
          //TODO:

        $color = 'ff0';
        $title = 'Added to Group';
        $info = $data;
         // $info = "You have been added to the <a href='/group.php?g={$data["i"]}'>{$data["n"]}</a> group.";
        break;

        default:
        continue;
        break;
      }

      if($color != "") $color = ' style="background-color:#'.$color.';"';
      if($icon != "") $icon = " {$icon}";

      echo '     <div class="notification">
      <div class="noteIcon'.$icon.'"'.$color.'></div>
      <div class="noteTitle">'.$title.'</div>
      <div class="noteInfo">
        '.$info.'
      </div>
      <div class="noteSeparator"></div>
    </div>
    ';
  }
} else {
	echo '<div style="text-align:center;">Sorry, no notifications to display at this time!</div>';
}
?>

<pre>
  <?php
// var_dump($notifications);
 // var_dump($notifications[1]["message"]);
  // echo date('Y-m-d');
  // $date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')));
  //        echo $date;
  ?>
</pre>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">Timesules.notifications();</script>
<?php
if(!function_exists('json_decode')) {
	function json_decode($json, $dummy) {
		$quote = false;
		$ret = '$ret = ';
		for($i=0;$i<strlen($json);$i++) {
			if($quote) {
				$ret .= ($json[$i] == '$')?'\$':$json[$i];
			} else {
				switch($json[$i]) {
					case '{':
					case '[':
          $ret .= 'Array(';
            break;
            case '}':
            case ']':
            $ret .= ')';
break;
case ':':
$ret .= '=>';
break;
default:
$ret .= ($json[$i] == '$')?'\$':$json[$i];
break;
}
}
if($json[$i] == '"') $quote = !$quote;
}
eval($ret.';');
return $ret;
}
}
?>
