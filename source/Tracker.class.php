<?php
/**
 * Timesules
 * Tracker.class.php
 * 
 * This file contains a tracker that tracks all user movements around the site
 * Used for debug perperposes on
 * @author Tyler Hadidon
 * @copyright 2012
 */
if(!defined('IN_TIMESULES'))
	exit;

class Tracker {
	private $postData;
	private $getData;
	private $page;
	private $userID;
	private $userData;
	
	public function Tracker() {
		global $user;
		$this->postData = $_POST;
		$this->getData = $_GET;
		$this->page = substr($_SERVER['PHP_SELF'],1,-4);
		$this->userID = $user->get("id");
		$this->userData = "---Contacts---\n".print_r($user->get("contacts"),true)."\n---Groups---\n".print_r($user->get("groups"),true)."\n---Notifications---\n".$user->get("notifications");
	}

	public function __destruct() {
		global $sql, $user;

		$userID = $this->userID;
		$page = $this->page;
		$get = print_r($this->getData,true)."\n---Page End---\n".print_r($_GET,true);
		$post = print_r($this->postData,true)."\n---Page End---\n".print_r($_POST,true);
		$userData = $this->userData."\n\n===END PAGE===\n---Contacts---\n".print_r($user->get("contacts"),true)."\n---Groups---\n".print_r($user->get("groups"),true)."\n---Notifications---\n".$user->get("notifications");
		
		$queries = "Rows|Time|Query\n---------------------------------------------\n";
		foreach($sql->getQueryInfo() as $q) {
			$queries .= $q["rows"].'|'.$q["time"].'|'.$sql->escape($q["query"])."\n";
			if(!$q["success"]) $queries .= "Error: ".$q["error"]."\n";
			$queries .= "---------------------------------------------\n";
		}

		$query = "INSERT INTO `debug_tracker`
		(`user`,`page`,`get_data`,`post_data`,`queries`,`user_data`) VALUES
		('{$userID}','{$page}','{$get}','{$post}','{$queries}','{$userData}')";
		$sql->setDebugging(false);
		$sql->query($query);
	}
}