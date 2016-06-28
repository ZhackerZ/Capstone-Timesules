<?php
/**
 * Timesules login handler. Response codes are as follows:
 * 200 = OK
 * 500 = Unknown Error
 * 900 = Password fields did not match
 * 901 = Could not confim key
 *
* @author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

if($user->isLoggedIn())
	$manager->redirect("/index.php");

// A. Update the password
if(!is_null($_POST["fgtpwReset"]) &&
!is_null($_POST["fgtpw"]["email"]) &&
!is_null($_POST["fgtpw"]["conf"]) &&
!is_null($_POST["fgtpw"]["uid"]) &&
!is_null($_POST["fgtpw"]["pass"]) &&
!is_null($_POST["fgtpw"]["passConf"])
) {
	$pass = $_POST["fgtpw"]["pass"];
	$passConf = $_POST["fgtpw"]["passConf"];
	$email = $sql->escape($_POST["fgtpw"]["email"]);
	$conf = $sql->escape($_POST["fgtpw"]["conf"]);
	$id = $sql->escape($_POST["fgtpw"]["uid"]);
	unset($_POST["pass"]);
	unset($_POST["passConf"]);
	$num = $sql->select("users","`user_email`,`user_id`,`user_conf`,`user_ban`", "WHERE `user_id`='{$id}' AND `user_email`='{$email}' AND `user_conf`='{$conf}'");
	$row = $sql->fetch();

	if($num===1 && $row["user_email"]==$email && $row["user_conf"]===$conf && $row["user_ban"]=="0" && $row["user_id"]==$id && $pass!="" && $pass===$passConf) {
		define('GEN_PWD', TRUE);
		$update = Array(
			"user_conf"=>"",
			"user_password"=>$user->genPasswordHash($pass),
			"user_ip"=>$_SERVER['REMOTE_ADDR']
		);

		if($sql->update("users", $update, "WHERE `user_id`='{$id}' AND `user_email`='{$email}' AND `user_conf`='{$conf}'")) {
			try {
				$_POST["user"] = $row["user_email"];
				$_POST["pass"] = $pass;
				unset($pass);
				$user->login();
			} catch(LoginFailureException $e) { } // Ignore not being able to log in.

			$manager->redirect("/index.php");
			exit;
		} else {
			$fgtpwError = 'An error occured while updating your password. Please try again.';
		}
	} else if($num===1 && $row["user_email"]==$email && $row["user_conf"]===$conf && $row["user_ban"]=="0" && $row["user_id"]==$id) {
		$fgtpwError = 'Your password fields did not match or you did not enter a new password. Please try again.';
	} else {
		$fgtpwError = 'An error occured while validating your email and confirmation code! Either the code given was incorrect, your account has been banned, or your email could not be found.';
	}
	$confirming_reset_pass = TRUE;
	require 'index.php';
	exit;
}

// C. Check if they are sending a new request
// 200 = OK
// 500 = Database/Confirmation code error
// 900 = Email not found
// 901 = Account disabled
if(!is_null($_POST["fgtpw"]["email"]) && !is_null($_POST["fgtpwSend"])) {
	$email = $sql->escape($_POST["fgtpw"]["email"]);
	$num = $sql->select("users", "`user_id`,`user_email`,`user_ban`,`user_first`,`user_last`", "WHERE `user_email`='{$email}'");
	$row = $sql->fetch();

	// Validate email and ban status (2->Tell them they need to validate first, 1->Tell them they are banned, 3->Not possible... but should be no)
	if($num===1 && $row["user_email"]==$email && $row["user_ban"]=="0") {
		$confChecksum = md5('fgtpw?'.rand(3,10).time().$row["user_last"].'yep:(');
		if($sql->update("users", "`user_conf`='{$confChecksum}',`user_ip`='{$_SERVER['REMOTE_ADDR']}' WHERE `user_id`='{$row["user_id"]}'")) {
			$data = Array(
				"first"=>$row["user_first"],
				"last"=>$row["user_last"],
				"confirm"=>'http://'.$_SERVER["SERVER_NAME"]."/index.php?conf={$confChecksum}&email={$row["user_email"]}"
			);
			$manager->mail($row["user_email"], "forgotPassword", $data);
			$fgtpwSuccessful = TRUE;
			$fgtpwError = 'An email has been sent to "'.$row["user_email"].'" confirming you want to reset your password. Once your request has been confirmed, you will be able to reset your password.';
		}
		else {
			$fgtpwError = 'An error occured while trying to generate a confirmation code. If this problem persists, please contact <a href="support.php">support</a>. Sorry for the inconvenience.';
		}
	} else if($row["user_ban"]=="1") {
		$fgtpwError = 'It appears your account has been disabled by an administrator. Please contact <a href="support.php">support</a> for further information.';
	} else {
		$fgtpwError = 'The email address "'.$email.'" could not be found in the system. Please try again.';
	}
	require 'index.php';
	exit;
}

$manager->redirect("/index.php");
?>
