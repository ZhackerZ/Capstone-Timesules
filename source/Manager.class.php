<?php
/**
 * Timesules
 * Manager.class.php
 *
 * This file contains the main manager class
 * used as the main backend controller.
 * @author Tyler Hadidon
 * @copyright 2012
 */
if(!defined('IN_TIMESULES'))
	exit;

/**
 * Manages all the things :P (better comment later)
 * @author Tyler Hadidon
 */
class Manager {
	private $root;
	private $settings;
	private $dirs;
	private $templateDir;
	private $mailerDir;
	private $avatarDir;

	const NEW_CONTACT = 1;
	const NEW_GROUP_CAPSULE = 2;
	// const RECEIVED_CAPSULE = 3;
	const RELEASED_CAPSULE = 3;
	const RELEASED_GROUP = 5;
	const ADDED_TO_GROUP = 6;
	const PREF_COUNT = 7;

	/**
	 * The manager starts up.
	 */
	public function Manager() {
		global $sql, $user, $theme, $root;
		$this->root = $root;

		// Destroy the $_REQUEST array
		// TODO: Research if this is the SAFEST way
		unset($_REQUEST);

		// Load the settings file
		require($this->root."source/settings.php");
		$this->settings = $settings;
		unset($settings);

		// Setup directories
		$this->dirs = $this->settings["dir"];
		$this->templateDir = $this->root.$this->dirs["template"];
		define('TEMPLATE_DIR', $this->templateDir."/errors/"); // For error handler
		$this->mailerDir = $this->root.$this->dirs["mailer"];
		$this->avatarDir = $this->root.$this->dirs["avatars"];

		// Tell the theme handler where the templates are stored
		if($theme != null)
			$theme->setDir($this->templateDir);

		// Connect to the MySQL server
		try {
			$sql->connect($this->settings["MySQL"]);
			unset($this->settings["MySQL"]["pass"]);

		} catch(SQLException $e) {
			switch($e->getCode()) {
				case SQLException::MYSQL_CONNECT:
				trigger_error('Error connecting to MySQL server. Check server settings.<br />'.$sql->error(), E_USER_ERROR);
				break;

				case SQLException::MYSQL_SELECT_DB:
				trigger_error('Error selecting the MySQL database. Check server settings.<br />'.$sql->error(), E_USER_ERROR);
				break;

				default:
				trigger_error('An unknown error occurred with the MySQL connection. Check server settings.<br />'.$sql->error(), E_USER_ERROR);
				break;
			}
		}

		// Check for TRACKING]
		if($this->settings["MySQL"]["tracking"]===TRUE) {
			if(is_null(($_POST["ajaxCall"])) && is_null($_GET["ajaxCall"])) {
				require_once 'Tracker.class.php';
				$tracker = new Tracker();
				$sql->setDebugging(TRUE);
			} else
			$sql->setDebugging(FALSE);
		}

		// Load user data
		if($user != null)
			$user->setupUser();
	}

	/**
	 * Requires the user to login inorder to view a page.
	 * @param String $url - The URL to redirect to
	 */
	public function requireLogin($url = "/index.php") {
		global $user, $basename, $theme;
		if($user->isLoggedin()===TRUE)
			return true;

		if($_GET["ajaxCall"] == "true")
			exit('{"code":401,"msg":"Authorization required. Not currently logged in."}');
		else
			$this->redirect($url);
		exit;
	}

	/**
	 * Sends an email to an email address.
	 * @param String $to - The address to send to
	 * @param String $subject - The Email's subject or template file to use
	 * @param Mixed $msg - The message (HTML allowed) or message data for the template file
	 * @return True if the email sent properly, False otherwise.
	 */
	public function mail($to, $subject, $msg) {
		global $user, $theme;

		// If the $msg is a template file
		if(file_exists($this->templateDir."/emails/".$subject.".php")) {
			$data = $msg;
			if(!(include $this->templateDir."/emails/".$subject.".php"))
				return false;
		} else {
			$plain_text = @html_entity_decode($msg, ENT_NOQUOTES);
			$html = $msg;
		}

		// Add the email theme
		if(file_exists($this->templateDir."/emails/email_template.php"))
			include $this->templateDir."/emails/email_template.php";

		return $this->sendMail($to, $subject, $plain_text, $html, $fromUser, $bbc);
	}

	/**
	 * Sends email.
	 * @param String $to - The email(s) to send to
	 * @param String $subject - The subject of the email
	 * @param String $plain_text - The plain text version of the email
	 * @param String $html - The HTML version of the email
	 * @param Boolean $fromUser - Whether to send from the user's email address or not
	 * @param Boolean $bcc - BCC $to field
	 * @return If the email was sent successfully
	 */
	private function sendMail($to, $subject, $plain_text, $html = "", $fromUser = FALSE, $bcc = FALSE) {
		global $user;

		// Make sure $to is an array
		if(!is_array($to)) {
			$to = Array($to);
		}

		if(!class_exists('PHPMailer'))
			require $this->mailerDir.'class.phpmailer.php';
		$conf = $this->settings["phpMailer"];

		try {
			$mail = new PHPMailer(true);
			$mail->IsSMTP();

			$mail->SMTPAuth = $conf["Auth"];
			$mail->SMTPSecure = $conf["Secure"];
			$mail->Host = $conf["Host"];
			$mail->Port = $conf["Port"];
			$mail->Username = $conf["User"];
			$mail->Password = $conf["Pass"];
			if($from !== TRUE) {
				$mail->SetFrom($conf["FromAddress"], $conf["FromName"]);
				$mail->AddReplyTo($conf["FromAddress"], $conf["FromName"]);
			} else {
				$mail->SetFrom($user->get("email"), $user->get("first").' '.$user->get("last"));
				$mail->AddReplyTo($user->get("email"), $user->get("first").' '.$user->get("last"));
			}
			$mail->Timeout = $conf["Timeout"];

			foreach($to as $add) {
				if($bcc === TRUE)
					$mail->AddBCC($add);
				else
					$mail->AddAddress($add);
			}

			$mail->Subject = $subject;
			$mail->AltBody = $plain_text;
			if($html != "")
				$mail->MsgHTML($html);
			$mail->Send();

		} catch(Exception $e) {
			return false;
		}
		return true;
	}

	/**
	 * Redirects the client to a new location if able.
	 * @param String $loc - The URL to go to
	 * @param String $time - The amount of time till the redirect takes place.
	 * @return True if the redirect is successful (and time is not 0), otherwise False.
	 */
	public function redirect($loc, $time = 0) {
		// Check headers were not already sent.
		// Print out the link if they were!
		if(headers_sent()) {
			trigger_error("HTTP headers were already sent. Unable to redirect to <a href='{$loc}'>{$loc}</a>.", E_USER_WARNING);
			return false;
		}

		// If we are making this an instant redirect.
		if($time == 0) {
			header("Location: {$loc}");
			exit; // Exit is needed to send the header
		} else
		header("Refresh: {$time}; url={$loc}");

		return true;
	}

	/**
	 * Gets the Avatar for the requested user.
	 * @param Mixed $contact - The avatar image name, the contact ID to get the
	 * Avatar for, or the current user if empty string
	 * @param Boolean $html - If TRUE, will return an HTML img tag,
	 * if FALSE, only the path the the Avatar is returned
	 * @return The path to the Avatar
	 */
	public function getAvatar($contact = "", $html = true) {
		/*if($contact == NULL) {
			global $user;
			$contact = $user->get("id");
		}

		$img = md5($contact);
		$img = substr($img, 0, 20);
		*/

		global $user;
		$contacts = $user->get("contacts");
		if($contact == $user->get("id"))
			$img = $user->get("avatar");
		else if(isset($contacts["users"][$contact]))
			$img = $contacts["users"][$contact]["avatar"];
		else
			$img = $contact;

		if($img == "" || !file_exists($this->root."{$this->avatarDir}{$img}"))
			$img = "unknown.png";

		$img = $this->dirs["avatars"].$img;
		if(!$html)
			return $img;
		else
			return "<img src='{$img}' />";
	}

	/**
	 * Gets the Avatar for the requested group.
	 * @param Mixed $id - The id of the group
	 * @param Boolean $html - If TRUE, will return an HTML img tag,
	 * if FALSE, only the path the the Avatar is returned
	 * @return The path to the Avatar
	 */
	public function getGroupImage($id = "", $html = true) {
		global $user;
		$contacts = $user->get("groups");
		$img = $contacts["groups"][$id]["avatar"];

		if($img == "" || !file_exists($this->root."{$this->avatarDir}{$img}"))
			$img = "group-unknown.png";

		if(!$html)
			return "{$this->avatarDir}{$img}";
		else
			return "<img src='{$this->avatarDir}{$img}' />";
	}

	/**
	 * Gets a summary of a large piece of text.
	 * @param String $text - The text to get a summary of
	 * @param Int $length - The max length of the summary
	 */
	public function getSummary($text, $length = 25) {
		// Strip tags. I want plain text only!
		$text = strip_tags($text);
		$len = strlen($text);
		if($text == "")
			return "No summary available...";
		else if($len < $length)
			return $text;

		$words = explode(" ",$text);
		$ret = Array();
		for($i=0,$c=0;$c<$length-3;$i++,$c++) {
			$c += strlen($words[$i]);

			if($c>$length-3 && $i == 0)// strlen($words[$i]) > 7)
$ret[] = substr($words[$i], 0, $length-$c-3);
else if($c<$length-3)
	$ret[] = $words[$i];
}

return implode(" ", $ret)."...";
}

	/**
	 * Add a notification to a user's profile.
	 * @param String $users - The list of users (as comma delimited) to send notification to
	 * @param Const $note - The manager constact for the notification type
	 *   (NEW_CONTACT, NEW_GROUP_PROMPT, RELEASED_PERSONAL, RELEASED_GROUP, ADDED_TO_GROUP)
	 * @param String $template - The template file to use for adding the notification
	 * @param Array $data - The data array to use to supply extra data to the template
	 * @return The number of users succesfully added, -1 if update query failed or inproper data supplied
	 */
	public function addNotification($user_id, $note, $template, $data) {
		global $user, $sql;
		$ret = -1;

		$template = "/notifications/{$template}.php";
		if(!file_exists($this->templateDir.$template) || !(include $this->templateDir.$template))
			return -1;

		//user_id is the person I want to add and friend_id is me
		//$vals = array("user_id"=>$user_id,"friend_id"=>$user->get("id"),"type"=>Manager::NEW_CONTACT, "message"=> "{$user->get("first")} {$user->get("last")} wants to be friends");// "viewed"=>"0");

		$msg = "";
		$vals = array("notification_type"=>Manager::NEW_CONTACT, "user_id"=>$user_id, "message"=>$msg);// "viewed"=>"0");
		//insert notification to DB
		$note = $sql->insert("notifications", $vals);
		$msg = "{$user->get("first")} {$user->get("last")} wants to be friends </br><span id=\"requestID-accept-{$user->get("id")}-{$note}\" class=\"fake-link\">Accept</span> | <span id=\"requestID-ignore-{$user->get(id)}-{$note}\" class=\"fake-link\">Ignore</span>";
		$sql->update("notifications","`message`='{$msg}'","WHERE `notification_id`='{$note}'");


		// Add notifications to users if it is a "onsite" notification
		// if($noteData != "") {
		// 	$ret = 0;
		// 	$noteData = '{"n":'.$note.',"d":'.$noteData.',"v":0,"t":'.time().'}';
		// 	$set = "`user_notifications` = CASE
		// 	WHEN `user_notifications`=''
		// 	THEN '{$noteData}'
		// 	ELSE CONCAT(`user_notifications`, ',{$noteData}')
		// 	END";
		// 	if(($ret = $sql->update("users", $set, "WHERE `user_id` IN({$users})")) == FALSE)
		// 		return -1;
		// }

		// Send an email if an email field exists
		if($subject != "") {
			$sql->select("users", "`user_first`,`user_last`,`user_email`,`user_prefs`", "WHERE `user_id` IN({$user_id})");
			$ret = 0;
			while(($usr = $sql->fetch()) !== FALSE) {
				// Check if they have opted into these notifications by email
				$prefs = decbin($usr["user_prefs"]);
				if($prefs[$note-1]!="1")
					continue;

				$replace = Array("{USER_FIRST}", "{USER_LAST}");
				$with = Array($usr["user_first"], $usr["user_last"]);
				$plain_text = str_replace($replace, $with, $plain_text);
				$html = str_replace($replace, $with, $html);

				// Add the email theme
				if(file_exists($this->templateDir."/emails/email_template.php"))
					include $this->templateDir."/emails/email_template.php";

				if($plain_text != "")
					$ret = $ret + $this->sendMail($usr["user_email"], $subject, $plain_text, $html);
			}
		}

		return $ret;
	}

	/**
	 * @return The avatar save directory from the settings file
	 */
	public function getAvatarDir() { return $this->avatarDir; }
}
?>
