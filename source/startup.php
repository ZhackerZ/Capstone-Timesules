<?php
/**
 * Timesules
 * startup.php
 *
 * This file is the main entry point for the Timesules
 * website backend.
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
include('includes/functions.php');
include('includes/sessions.php');

if(!defined('IN_TIMESULES'))
	exit;

// Check if Error handleing has already been setup
if(!defined('TIMESULES_ERROR_SET'))
	require_once 'Error.handler.php';

// Includes
require_once 'MySQL.class.php';
require_once 'Manager.class.php';
require_once 'User.class.php';
require_once 'Theme.class.php';
require_once 'vendor/autoload.php';


// Get the root directory
$self = $_SERVER['PHP_SELF'];
$basename = basename($self);
$path = str_replace($basename, "", $self);
$root = "./".preg_replace("|[^/]*/|", "../", substr($path, 1));

// Setup system
$sql = new SQL();
$user = new User();
$theme = new Theme();
$manager = new Manager();
// // global $debugbar;
// use DebugBar\StandardDebugBar;
// $debugbar = new StandardDebugBar();
// $debugbarRenderer = $debugbar->getJavascriptRenderer();

?>