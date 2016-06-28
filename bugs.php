<?php
/**
 * Timesules bug reporting page.
 * 
 * @author Tyler Hadidon Yuzhen Liu
 * @copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

$manager->requireLogin();

$pages = explode(",", "Calendar,Contacts,Error,Groups,Home,Viewing Capsules,Profiles,Settings,Time Prompt,Time Capsules,Other");

if(!is_null($_POST["support"])) {
	$page = $sql->escape($_POST["page"]);
	$msg = $sql->escape($_POST["msg"]);
	$userID = $sql->escape($_POST["user"]);

	$error = "";
	$success = false;
	if($page == "") $error .= "Please select the page where the bug exists.<br />";
	if($msg == "") $error .= "Please provide information that caused the bug.<br />";

	if($error === "") {
		$insert = Array(
			"bug_page"=>$page,
			"bug_msg"=>$msg,
			"bug_user"=>($userID !="")?$userID:"-1",
			"bug_date"=>time()
		);
		if($sql->insert("bugs", $insert) != FALSE) {
			//$manager->mail("", "Timesules Bug Reported", implode("\r\n", $insert));
			$success = true;
		} else {
			$error .= "An error was encountered while adding your message to the support system. Please try again.<br />";
		}
	}
}

$theme->load("main_header", Array("title"=>"Bug Reporter"), false);
?>
<div style="padding: 25px;">
 <div id="generalContent" style="text-align: left;width: 600px; margin: 0 auto;">
  <div class="generalContentHeader">TIMESULES BUG REPORT</div>
<?php if($error != "") echo '  <div class="ui-error">'.$error.'</div>';?>
<?php if($success === true) echo '  <div class="ui-success">Your bug report has been sent.</div>';?>
  <div class="generalContainer">
  <form class="supportForm" action="/bugs.php" method="POST" style="width: 100%;margin:0px;padding:0px;">
   PAGE<br />
   <select name="page"><option value="">--- Please Choose ---</option><?php foreach($pages as $type) echo '<option value="'.$type.'"'.(($_POST["page"]==$type)?' selected="selected"':'').'>'.$type.'</option>'; ?></select><br /><br />
   Please provide as much detail about the bug encountered in the area below:<br />
   <textarea style="width:100%;height:200px;" name="msg"><?php echo $_POST["msg"]; ?></textarea><br />
   <input type="hidden" name="user" value="<?php echo $user->get("id");?>" />
   <input type="submit" class="submit-button" value="Send" name="support" />
  </form>
  </div>
 </div>
</div>
<?php
$theme->load("main_footer", Array(), false);
?>