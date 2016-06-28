 <?php
/**
 * Basic install script
 */
if(!is_null($_POST["install"])) {
	// First create the settings file
	$sql = $_POST["mysql"];
	$mail = $_POST["mail"];

	$settings = <<<END_SETTINGS
<?php
/**
 * Timesule
 * settings.php
 *
 * This file contains the settings for the timesules
 * website. This includes database settings and
 * PHPMailer settings.
 * @author Tyler Hadidon
 * @copyright 2012
 */
\$settings = Array(

// Directories
"dir"=>Array(
	"template"=>"/source/templates/", // The template path
	"mailer"=>"/source/phpMailer/",   // The phpMailer class path
	"avatars"=>"/avatars/"            // Avatar storage path
	),

// MySQL
"MySQL"=>Array(
	"host"=>"{$sql['host']}", // MySQL Host
	"user"=>"{$sql['user']}",	     // User
	"pass"=>"{$sql['pass']}",          // Password
	"db"=>"{$sql['db']}",  // Database
	"prefix"=>"",        // The prefix prepended to each table name
	"debug"=>FALSE,      // Turn on debugging dropdown (upper left
	"tracking"=>FALSE    // Turn on user tracking through Tracking.class.php
	),

// Mailer
// These are direct for phpMailer
"phpMailer"=>Array(
	"FromAddress"=>"{$mail['fromAddress']}",
	"FromName"=>"{$mail['fromName']}",
	"Host"=>"{$mail['host']}",
	"Port"=>{$mail['port']},
	"Auth"=>{$mail['auth']},
	"Secure"=>"{$mail['secure']}",
	"User"=>"{$mail['user']}",
	"Pass"=>"{$mail['pass']}",
	"Timeout"=>{$mail['timeout']}
	)
);
?>
END_SETTINGS;
	file_put_contents("../source/settings.php", $settings);

	// Next, install the database
	$con = mysql_connect($sql["host"],$sql["user"],$sql["pass"]);
	if($con) {
		mysql_select_db($sql["db"],$con);
		$database = file_get_contents("timescules-2013-02-28.sql");
		$database = explode(";",$database);
		mysql_query("DROP DATABASE IF EXISTS `".$sql["db"]."`",$con);
		mysql_query("CREATE DATABASE `".$sql["db"]."`",$con);
		mysql_select_db($sql["db"],$con);
		$results = true;
		foreach($database as $sql) {
			if($sql == "") continue;
			$results = $results && mysql_query($sql,$con);
		}
		mysql_close($con);
		if($results)
			exit('Everything installed successfully!');
		else
			echo 'Error! Something failed with the database install! Maybe lack of permissions?<br />';
	} else {
		echo 'Error! Could not establish connection to MySQL database!<br />';
	}
}
?>
<form action="/install/index.php" method="post">
<h1>MySQL</h1>
Host: <input type="text" name="mysql[host]" value="localhost" /><br />
Username: <input type="text" name="mysql[user]" value="root" /><br />
Password: <input type="text" name="mysql[pass]" /><br />
Database: <input type="text" name="mysql[db]" value="timesules" /><br />
<h1>phpMailer</h1>
FromAddress: <input type="text" name="mail[fromAddress]" value="no-reply@timesules.com" /><br />
FromName: <input type="text" name="mail[fromName]" value="Timesules" /><br />
Host: <input type="text" name="mail[host]" /><br />
Port: <input type="text" name="mail[port]" value="25" /><br />
Auth: <input type="text" name="mail[auth]" value="false" /><br />
Secure: <input type="text" name="mail[secure]" value="ssl" /><br />
User: <input type="text" name="mail[user]" /><br />
Pass: <input type="text" name="mail[pass]" /><br />
Timeout: <input type="text" name="mail[timeout]" value="10" /><br />
<input type="submit" name="install" value="Install!" />
</form>
