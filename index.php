<?php
/**
 * Timesules page index. The start of it all!
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */

define('IN_TIMESULES', true);
require_once 'source/startup.php';

if($_GET['alert']=="verify"){
  $_SESSION['message'] = "Your account has been made, please verify it by clicking the activation link that has been send to your email: ".$_GET['email'];
}

if($user->isLoggedIn()) {
	require("user_index.php");
	exit;
}

// See if they are trying to send a capsule
if (isset($_POST["sendCapsule"])){
	if(!is_null($_POST["sendCapsule"])) {
		$sendCap = $_POST["sendCap"];
		$cemail = $sql->escape($sendCap["email"]);
		$subject = $sql->escape($sendCap["subject"]);
		$message = $sql->escape($sendCap["message"]);
		$mon = $sendCap["mon"];
		$day = $sendCap["day"];
		$year = $sendCap["year"];

		$error = "";
		if($cemail == "") $error .= "Please enter an email address.<br />";
		else if(!filter_var($cemail, FILTER_VALIDATE_EMAIL)) $error .= "That is an <span>invalid email address</span>.<br />";

		if($subject == "") $error .= "Please enter a subject.<br />";
		if($message == "") $error .= "You must write a short message.<br />";
		if(!@checkdate($mon, $day, $year)) $error .= "The date you have entered is invalid.<br />";

		if($error === "") {
			$sendCap = Array(
				"cap_email"=>$cemail,
				"cap_subj"=>$subject,
				"cap_msg"=>$message,
				"cap_time"=>strtotime("{$mon}/{$day}/{$year}")
       );

			if($sql->insert("sendacapsule",$sendCap) !== FALSE) {
				$relDate = date("l F jS, Y", $sendCap["cap_time"]);
				$data = Array("release"=>$relDate,"subject"=>$subject);
				$manager->mail($cemail, "sendACapsuleSend", $data);
				$success = "Your capsule has been buried and will be delivered on {$relDate}!";
			} else {
				$error .= "There was an error while trying to save your Time Capsule. Please be sure you have filled out all of the boxes below and try again.";
			}
		}
	}
}
?>
<div style="padding:10px;">
  <?php //echo '<div class="ui-notice">'.$_SESSION['test'].'</div>';
  echo message(); ?>

</div>
<div id="send-a-capsule-wrapper">
  <div id="send-a-capsule">
    <form action="/index.php" method="post">
     <h2><span>&bull;&bull;</span> SEND A CAPSULE <span>&bull;&bull;</span></h2>
     <?php
     if (isset($error) && isset($success)){
       if($error != "") {
        echo '    <div class="ui-error">'.$error.'</div>';
      }else if($success != "") {
        echo '    <div class="ui-success">'.$success.'</div>';
      }
    }?>

    <div id="send-capsule-form">
      <div>E-mail Address</div>
      <input type="text" name="sendCap[email]" value="<?php if(isset($cemail)){ echo $cemail; }?>" /><br />
      <div>Subject</div>
      <input type="text" name="sendCap[subject]" value="<?php if(isset($subject)){ echo $subject; }?>" /><br />
      <div>Message</div>
      <textarea name="sendCap[message]" style="height:125px;"><?php if(isset($message)){ echo $message; }?></textarea><br />
      <div>
       Deliver On:&nbsp;
       <select name="sendCap[mon]">
        <?php
        $tomorrow = ($capDate!="")?$capDate:(time()+3600*24);
        $months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC");
        foreach($months as $key=>$month)
          echo "<option value='".($key+1)."'".(($key+1==date("",$tomorrow))?' selected="selected"':"").">{$month}</option>\n";
        ?>
      </select>
      <select name="sendCap[day]">
        <?php for($i=1;$i<32;$i++) echo "<option value='{$i}'".(($i==date("d",$tomorrow))?' selected="selected"':"").">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
        </select>
        <select name="sendCap[year]">
          <?php $thisYear = date("Y"); for($i=$thisYear;$i<$thisYear+11;$i++) echo "<option value='{$i}'".(($i==date("Y",$tomorrow))?' selected="selected"':"").">{$i}</option>\n"; ?>
        </select>
      </div>
    </div>
    <div style="text-align:right;"><input type="submit" class="submit-button" name="sendCapsule" value="SEND THIS CAPSULE" /></div>
  </form>
</div>
</div>

<div id="welcome-right-content">
  <div id="timesules-quote"><img src="/source/templates/images/timesules-quote.png" alt="Timesules lets individuals and organizations relive memorable experiences and events." /></div>
  <div id="timesules-video-wrapper">
   <div id="timesules-video">
    <img src="/source/templates/images/tmp-video.png" alt="Welcome to Timesules.com!" style="margin:0px" />
  </div>
</div>
</div>
<?php
$theme->load("welcome_page");
?>
