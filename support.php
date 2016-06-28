<?php
/**
 * Timesules support page.
 * 
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

$ticketTypes = explode("|", "User Account|Prompt Help|Error Reporting|Other");

if(!is_null($_POST["support"])) {
	$name = $sql->escape($_POST["name"]);
	$email = $sql->escape($_POST["email"]);
	$area = $sql->escape($_POST["area"]);
	$msg = $sql->escape($_POST["msg"]);
	$userID = $sql->escape($_POST["user"]);

	$error = "";
	$success = false;
	if($name == "") $error .= "Please give your name.<br />";
	else if(strlen($name) < 2) $error .= "Your name must be longer than 2 characters.<br />";
	if($email == "") $error .= "Please supply your email so we can reply to you.<br />";
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $error .= "The email address you have supplied is not valid. Please supply a valid email address.<br />";
	if($area == "") $error .= "Please select an area relating to the support you are requesting.<br />";
	if($msg == "") $error .= "Please enter a brief message describing the reason you are contacting us and any additional information you feel is necessary.<br />";

	if($error === "") {
		$insert = Array(
			"ticket_name"=>$name,
			"ticket_area"=>$area,
			"ticket_email"=>$email,
			"ticket_msg"=>$msg,
			"ticket_user"=>($userID !="")?$userID:"-1",
			"ticket_date"=>time()
		);
		if($sql->insert("tickets", $insert) != FALSE) {
			$manager->mail("hadidotj@muohio.edu", "Timesules Support Request", implode("\r\n", $insert));
			$success = true;
		} else {
			$error .= "An error was encountered while adding your message to the support system. Please try again.<br />";
		}
	}
}

if($user->isLoggedIn())
	$theme->load("main_header", Array("title"=>"Timesules Support"), false);
?>
<div style="padding: 25px;">
 <div id="generalContent" style="text-align: left;width: 400px; margin: 0 auto;">
  <div class="generalContentHeader">TIMESULES SUPPORT</div>
<?php if($error != "") echo '  <div class="ui-error">'.$error.'</div>';?>
<?php if($success === true) echo '  <div class="ui-success">Your support request has been sent. Please allow three to five business days for us to process your request. Thank you!</div>';?>
  <div class="generalContainer">
  <form class="supportForm" action="/support.php" method="POST" style="width: 100%;margin:0px;padding:0px;">
   Welcome to the Timesules.com support center! Please fill out the form below and we will contact you as soon as possible.<br /><br />
   NAME<br /><input type="text" name="name" style="width:100%;<?php echo ($user->isLoggedIn())?'background-color:#DDD;;color:#555" readonly="readonly" value="'.$user->get("first").' '.$user->get("last").'"':'" value="'.$_POST["name"].'"'; ?>" /><br />
   E-MAIL<br /><input type="text" name="email" style="width:100%;<?php echo ($user->isLoggedIn())?'background-color:#DDD;;color:#555" readonly="readonly" value="'.$user->get("email").'"':'" value="'.$_POST["email"].'"'; ?>" /><br />
   SUPPORT AREA<br /><select name="area"><option value="">--- Please Choose ---</option><?php foreach($ticketTypes as $type) echo '<option value="'.$type.'"'.(($_POST["area"]==$type)?' selected="selected"':'').'>'.$type.'</option>'; ?></select><br /><br />
   MESSAGE <span style="font-size: 10px;">(2000 characters)</span><br /><textarea style="width:100%;height:100px;" name="msg" placeholder="Please explain here..."><?php echo $_POST["msg"]; ?></textarea><br />
   <?php if($user->isLoggedIn()) echo '<input type="hidden" name="user" value="'.$user->get("id").'" />';?>
   <input type="submit" class="submit-button" value="Send" name="support" />
  </form>
  </div>
 </div>
</div>
<?php
if($user->isLoggedIn())
	$theme->load("main_footer", Array(), false);
else
	$theme->load("welcome_page", Array("title"=>"Timesules Support"));
?>