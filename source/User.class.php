<?php
if(!defined('IN_TIMESULES'))
	exit;

/**
 * Timesules
 * User.class.php
 *
 * The user handler class handles user authentication and
 * session status.
 * @author Tyler Hadidon
 * @copyright 2012
 */
class User {
	// Password stuff
	private $PASS_LEN = 9;
	private $PASS_WATCHA_CALL_IT = '1P3TimeA3sc1es$:P';
	private $PASS_ONE = "sha224";
	private $PASS_TWO = "md5";
	private $PASS_ONE_START = 0;
	private $PASS_ONE_END = 20;
	private $PASS_TWO_START = 10;
	private $PASS_TWO_END = 15;
	private $PASS_ID = 3;

	private $AUTH_LEN = 7;
	private $AUTH_WATCHA_CALL_IT = '63ATimeu3sct1ehs$):';
private $AUTH_ONE = "sha1";
private $AUTH_TWO = "md5";
private $AUTH_ONE_START = 2;
private $AUTH_ONE_END = 15;
private $AUTH_TWO_START = 3;
private $AUTH_TWO_END = 20;
private $AUTH_ID = 5;

private $SITE_COOKIE = "Timesules";
	private $COOKIE_LEN = 2419200; // 4 weeks

	const GET_BOTH = 0;
	const GET_ASSOC = 1;
	const GET_NUM = 2;

	/** Default data for guests */
	private $GUEST = Array(
		"user_id"=>-1,
		"user_email"=>"guest@timesules",
		"user_first"=>"Guest",
		"user_last"=>"",
		"user_age"=>-1,
		"user_gender"=>-1,
		"user_ban"=>-1
		);

	/** Stores the user's info from the database table */
	private $info;
	/** Saves the user's login status */
	private $isLoggedIn;

	public function User() {
		$this->info = $this->GUEST;
		$this->isLoggedIn = FALSE;
	}

	/**
	 * Sets up the the user object by checking user authentication status.
	 */
	public function setupUser() {
		 // //session should be started outside user instance
		// session_start();

		$data = "";

		// Check authentication data

		if (isset($_SESSION[$this->SITE_COOKIE])){
			$data = $_SESSION[$this->SITE_COOKIE];
		}
		else{
			$data = null;
		}

		// $hasData = ($data != "" || $cookie != "")?true:false;

		// // Do we have data to check and do cookies and session agree?
		// if(!$hasData || ($cookie != "" && $data != "" && $data != $cookie)) {
		// 	$this->logout();
		// 	return;
		// }

		// // If we are loading from a cookie, set data to the cookie
		// if($data == "" && $hasData)
		// 	$data = $cookie;



		if(isset($_COOKIE[$this->SITE_COOKIE])) {
			$cookie = $_COOKIE[$this->SITE_COOKIE];
			$hasData = ($data != "" || $cookie != "")?true:false;

			// Do we have data to check and do cookies and session agree?
			if(!$hasData || ($cookie != "" && $data != "" && $data != $cookie)) {
				$this->logout();
				return;
			}

			// If we are loading from a cookie, set data to the cookie
			if($data == "" && $hasData)
				$data = $cookie;
		}


		// Get the user ID out and query it
		$id = substr($data, $this->AUTH_ID, @strpos($data, ".", $this->AUTH_ID)-$this->AUTH_ID);
		global $sql;
		$count = $sql->select("users", "*,BIN(user_prefs) AS `pref`", "WHERE `user_id`='{$id}'");
		$checker = $sql->fetch();
		$checker["user_password"] = NULL;
		unset($checker["user_password"]);

		// Temporaroly set data to info to check
		$this->info = $checker;

		if($this->checkAuthHash($this->get("id"), $data, $id) === TRUE) {
			$this->isLoggedIn = TRUE;

			// Reset session
			/*$auth = $this->genAuthHash($this->get("id"), $id);

			$this->logout();
			session_start();
			session_regenerate_id(true);
			$_SESSION[$this->SITE_COOKIE] = $auth;

			if($cookie != "")
			setcookie($this->SITE_COOKIE, $auth, time()+$this->COOKIE_LEN, "/");*/

			// Load in names and ids of the user's contacts and groups
			if($checker["user_contacts"] != "") {
				$contactsCount = $sql->select(
					"users",
					"`user_id` as `id`,`user_first` as `first`,`user_last` as `last`,`user_email` as `email`,`user_avatar` as `avatar`",
					"WHERE `user_id` IN ({$checker["user_contacts"]}) ORDER BY `user_first`, `user_last`"
					);
			} else {
				$contactsCount = 0;
			}

			$contacts = Array();
			$contacts["count"] = $contactsCount;
			$contacts["list"] = $checker["user_contacts"];
			if($contactsCount > 0) {
				while($row = $sql->fetch()) {
					$contacts["users"][$row["id"]] = $row;
				}
			}
			$checker["user_contacts"] = $contacts;

			// Now groups
			if($checker["user_groups"] != "") {
				$groupsCount = $sql->select(
					"groups",
					"`group_id` as `id`,`group_name` as `name`,`group_avatar` as `avatar`",
					"WHERE `group_id` IN ({$checker["user_groups"]}) ORDER BY `group_name`"
					);
			} else {
				$groupsCount = 0;
			}

			$groups = Array();
			$groups["count"] = $groupsCount;
			$groups["list"] = $checker["user_groups"];
			if($groupsCount > 0) {
				while($row = $sql->fetch()) {
					$groups["groups"][$row["id"]] = $row;
				}
			}
			$checker["user_groups"] = $groups;
			//$checker["user_prefs"] = decbin($checker["pref"]);
			$checker["user_prefs"] = $checker["pref"];

			// Put all information back
			$this->info = $checker;
			$this->isLoggedIn = TRUE;
			$this->checkIP();

		} else {
			$this->logout();
		}
	}

	/**
	 * Tries to login the user.
	 * @return TRUE if successfuly logged in, FALSE if not
	 * @throws LoginFailureException
	 */
	public function login() {
		global $sql;
		$user = $sql->escape($_POST["user"]);
		$pass = $_POST["pass"];
		$_POST["pass"] = NULL;
		unset($_POST["pass"]);

		// Get the user from the database
		if(($rows = $sql->select("users", "*", "WHERE `user_email`='{$user}'")) === FALSE)
			throw new LoginFailureException("Error with MySQL query.");

		$userData = $sql->fetch(MYSQL_ASSOC);

		// Check password checking status
		$passCheck = $this->checkPassHash($pass, $userData["user_password"]);
		$userData["user_password"] = NULL;
		unset($userData["user_password"]);

		// Check username and password failure
		if($rows != 1 || $userData["user_email"] != $user) {
			throw new LoginFailureException("Bad Username", LoginFailureException::BAD_USER);
		} else if($passCheck !== TRUE) {
			throw new LoginFailureException("Bad Password", LoginFailureException::BAD_PASS);

		// Check ban statuses
		// } else if($userData["user_ban"]=="2") {
		// 	throw new LoginFailureException("Account not yet validated", LoginFailureException::NOT_VALID);

		} else if($userData["user_ban"]=="1") {
			throw new LoginFailureException("Account dissabled", LoginFailureException::BANNED);

			//Check if actived
		} else if($userData["active"]=="0") {
			throw new LoginFailureException("Account not yet validated", LoginFailureException::NOT_VALID);


		// Check for succesful login
		} else if($userData["user_email"] == $user && $passCheck === TRUE) {
			$this->info = $userData;
			$this->isLoggedIn = TRUE;
			$auth = $this->genAuthHash($this->get("id"), $userData["user_id"]);

			$this->logout();
			session_start();
			session_regenerate_id(true);
			$this->info = $userData;
			$this->isLoggedIn = TRUE;
			$_SESSION[$this->SITE_COOKIE] = $auth;

			// If they chose to "stay logged in" then generate a cookie
			if($_POST["yumm"] == "Cookies")
				setcookie($this->SITE_COOKIE, $auth, time()+$this->COOKIE_LEN, "/");

			$this->checkIP();

			return TRUE;

		// What happened?
		} else
		throw new LoginFailureException();

		return FALSE;
	}

	/**
	 * Updates the user's IP address if it is different from their current one.
	 */
	private function checkIP() {
		global $sql;
		$current = $_SERVER['REMOTE_ADDR'];
		$id = $this->get("id");

		if($this->isLoggedIn() && $this->get("ip") != $current && $id != 0)
			$sql->update("users", "`user_ip`='{$current}'", "WHERE `user_id`='{$id}'");
	}

	/**
	 * Destroys all session data to log out the client.
	 */
	public function logout() {
		// First destroy their session
		$_SESSION = Array();
		if(isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time()-3600, "/");
		@session_destroy();

		// Unset user authentication cookie(s)
		setCookie($this->SITE_COOKIE, "", time()-3600, "/");

		// Finally clear the info array
		$this->info = $this->GUEST;
		$this->isLoggedIn = FALSE;
	}

	/**
	 * Gets value(s) from the user's info array.
	 * @param String $key - The key or keys (separated by ",")
	 * @param User_Constant $type - The format of the recieving array
	 * (similar to MYSQL_BOTH, MYSQL_NUM, and MYSQL_ASSSOC)
	 * @return The value from the info array or an array of data.
	 */
	public function get($key, $type = User::GET_BOTH) {

		// if($key == "*") {
		// 	$keys = array_keys($this->info);
		// 	foreach($keys as $num=>$val)
		// 		$keys[$num] = substr($val, 5);
		// } else
		// 	$keys = explode(",", $key);

		// if(count($keys) == 1)
		// 	return $this->info["user_".$key];

		// $new = Array();
		// foreach ($keys as $key) {
		// 	if($type != User::GET_ASSOC) {
		// 		if(!empty($key) && !is_null($key)) {
		// 			$new[] = $this->info["user_".$key];
		// 		}
		// 	}

		// 	if($type != User::GET_NUM) {
		// 		if(!empty($key) && !is_null($key)) {
		// 			$new[$key] = $this->info["user_".$key];

		if($key == "*") {
			$keys = $this->info;
			//foreach($keys as $num=>$val)
				//$keys[$num] = substr($val, 5);
		} else
		$keys = explode(",", $key);

		$new = array();
		if (isset($this->info["user_".$key])){
			if(count($keys) == 1)
				return $this->info["user_".$key];

			$new = Array();
			foreach ($keys as $key) {
				if($type != User::GET_ASSOC) {
					$new[] = $this->info["user_".$key];
					return $new;
				}

				if($type != User::GET_NUM) {
					$new[$key] = $this->info["user_".$key];
					return $new;
				}
					

			}
		}
		return $keys;
	}

	//----------------------------------------------------------------------
	// Hashing methods
	//----------------------------------------------------------------------

	/**
	 * Used to generate a password hash to store during sign up
	 * or when resetting/changing passwords.
	 * @param String $pass - The plain text to encrypt
	 * @param int $uid - The user's ID if wanted to be used in encrypt
	 * @param String $thing - Well, it is the thing you know?
	 * @return False if not able to generate, else returns the new hash.
	 */
	public function genPasswordHash($pass, $uid = NULL, $thing = NULL) {
		if(!defined('GEN_PWD') || constant('GEN_PWD') !== TRUE) return FALSE;

		return $this->genPassHash($pass, $uid, $thing);
	}

	/**
	 * Used to check a password hash when resetting/changing passwords.
	 * @param String $pass - The plain text to encrypt
	 * @param int $uid - The user's ID if wanted to be used in encrypt
	 * @param String $thing - Well, it is the thing you know?
	 * @return False if not able to check hash, else returns TRUE
	 */
	public function checkPasswordHash($pass, $checker = NULL) {
		if(!defined('CHECK_PWD') || constant('CHECK_PWD') !== TRUE) return FALSE;

		if($checker==NULL) {
			global $sql;

			$num = $sql->select("users", "user_password,user_id", "WHERE `user_id`='{$this->get("id")}'");
			$check = $sql->fetch();
			if($num == 1 && $check["user_id"] == $this->get("id"))
				$checker = $check["user_password"];
		}

		return $this->checkPassHash($pass, $checker);
	}

	/**
	 * Generates a password hash to store in the database.
	 * @param String $pass - The plain text password
	 * @param Integer $uid - The user's ID (optional)
	 * @param String $thing - The thing thing
	 * @return The new hashed password
	 */
	private function genPassHash($pass, $uid = NULL, $thing = NULL) {
		// First, generate a thing and a whatcha-ma-call-it
		if(is_null($thing))
			$thing = substr(md5(uniqid(rand(), true)), 0, $this->PASS_LEN);
		$whatcha_ma_call_it = $this->PASS_WATCHA_CALL_IT;

		// Now, generate a one and a two
		$one = hash_hmac($this->PASS_ONE, $pass.$thing, $whatcha_ma_call_it);
		$two = hash_hmac($this->PASS_TWO, $pass.$thing, $whatcha_ma_call_it);

		// Save one and two
		$save = substr($one, $this->PASS_ONE_START, $this->PASS_ONE_END).$thing.substr($two, $this->PASS_TWO_START, $this->PASS_TWO_END);

		// Add uID if we want
		if(!is_null($uid))
			$save = substr($save, 0, $this->PASS_ID)."{$uid}.".substr($save, $this->PASS_ID);

		return $save;
	}

	/**
	 * Checks the supplied password with an encrypted password hash.
	 * @param String $pass - The plain text password to check
	 * @param String $checker - The encrypted password to check with
	 * @param Integer $uid - The user's ID (optional, but required if $checker includes it)
	 * @return TRUE if the passwords match, otherwise FALSE
	 */
	private function checkPassHash($pass, $checker, $uid = NULL) {
		// First, remove UID if it exists
		$checker2 = $checker;
		if(!is_null($uid) && strpos($checker, $uid.".") !== FALSE)
			$checker2 = substr($checker, 0, $this->PASS_ID).substr($checker, $this->PASS_ID+strlen($uid)+1);

		// Get thing
		$thing = substr($checker2, $this->PASS_ONE_END, $this->PASS_LEN);

		// Now, hash the current password
		$pass = $this->genPassHash($pass, $uid, $thing);

		// Finally compare!
		if($pass == $checker)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Generates an authentication hash to use for session checking.
	 * @param String $data - The data to encrypt
	 * @param Integer $uid - The user's ID (optional)
	 * @return The new authentication hash
	 */
	private function genAuthHash($data, $uid = NULL, $thing = NULL) {
		// First, generate a thing and a whatcha-ma-call-it
		if(is_null($thing))
			$thing = substr(md5(uniqid(rand(), true)), 0, $this->AUTH_LEN);
		$whatcha_ma_call_it = $this->AUTH_WATCHA_CALL_IT;

		// Add more to data that we want in every thing
		$data .= $_SERVER["HTTP_USER_AGENT"];

		// Now, generate a one and a two
		$one = hash_hmac($this->AUTH_ONE, $data.$thing, $whatcha_ma_call_it);
		$two = hash_hmac($this->AUTH_TWO, $data.$thing, $whatcha_ma_call_it);

		// Save one and two
		$save = $thing.substr($one, $this->AUTH_ONE_START, $this->AUTH_ONE_END).substr($two, $this->AUTH_TWO_START, $this->AUTH_TWO_END);

		// Add uID if we want
		if(!is_null($uid))
			$save = substr($save, 0, $this->AUTH_ID)."{$uid}.".substr($save, $this->AUTH_ID);

		return $save;
	}

	/**
	 * Checks the supplied data with an encrypted authentication hash.
	 * @param String $data - The data to check with
	 * @param String $checker - The encrypted authentication hash to check with
	 * @param Integer $uid - The user's ID (optional, but required if $checker includes it)
	 * @return TRUE if the authentication codes match, FALSE if not
	 */
	private function checkAuthHash($data, $checker, $uid = NULL) {
		// First, remove UID if it exists
		$checker2 = $checker;
		if(!is_null($uid) && strpos($checker, $uid.".") !== FALSE)
			$checker2 = substr($checker, 0, $this->AUTH_ID).substr($checker, $this->AUTH_ID+strlen($uid)+1);

		// Get thing
		$thing = substr($checker2, 0, $this->AUTH_LEN);

		// Now, hash the current password
		$data = $this->genAuthHash($data, $uid, $thing);

		// Finally compare!
		if($data == $checker)
			return TRUE;
		else
			return FALSE;
	}

	// Getters and setters
	public function isLoggedIn() { return $this->isLoggedIn; }
}

/**
 * LoginFailureException class used for custom Exception handling.
 * @author Tyler Hadidon
 */
class LoginFailureException extends Exception {
	const BAD_USER = 900;
	const BAD_PASS = 901;
	const NOT_VALID = 902;
	const BANNED = 903;
	const LOGIN_ERROR = 500; // Generic Error

	/**
	 * Constructs a new LoginFailureException.
	 *
	 * @param String $message - The message of the exception
	 * @param LoginFailureException_Constant $code - The custom exception code
	 */
	public function __construct($message, $code = LoginFailureException::LOGIN_ERROR) {

		// Setup defaults
		switch($code) {
			case LoginFailureException::BAD_USER:
			case LoginFailureException::BAD_PASS:
			case LoginFailureException::NOT_VALID:
			case LoginFailureException::BANNED:
			// Code this fine!
			break;

		// Number not found, set to generic error
			default:
			$code = LoginFailureException::LOGIN_ERROR;
			break;
		}

		parent::__construct($message, $code);
	}

	/**
	 * @see Exception::__toString()
	 */
	public function __toString() {
		return __CLASS__.": [{$this->code} {$this->codeToString()}]: {$this->message}\n";
	}

	/**
	 * Gets the text equivilant of the exception's error code.
	 * @return The error message
	 */
	public function codeToString() {
		switch($this->code) {
			case LoginFailureException::BAD_USER:
			return "Incorrect user";
			case LoginFailureException::BAD_PASS:
			return "Incorrect password";
			case LoginFailureException::NOT_VALID:
			return "Account not valid";
			case LoginFailureException::BANNED:
			return "User Banned";
			default:
			return "Login Error";
		}
	}
}
?>