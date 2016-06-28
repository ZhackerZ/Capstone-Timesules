<?php
/**
 * Timesule
 * settings.php
 *
 * This file contains the settings for the timesules
 * website. This includes database settings and
 * PHPMailer settings.
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
$settings = Array(

// Directories
"dir"=>Array(
	"template"=>"/source/templates/", // The template path
	"mailer"=>"/source/phpMailer/",   // The phpMailer class path
	"avatars"=>"/avatars/"            // Avatar storage path
	),

// MySQL
"MySQL"=>Array(
	"host"=>"localhost", // MySQL Host
	"user"=>"capstoneteamsp15",	     // User
	"pass"=>"TimesulesSp15",          // Password
	"db"=>"timesules",  // Database
	"prefix"=>"",        // The prefix prepended to each table name
	"debug"=>FALSE,      // Turn on debugging dropdown (upper left
	"tracking"=>FALSE    // Turn on user tracking through Tracking.class.php
	),

// Mailer
// These are direct for phpMailer
"phpMailer"=>Array(
	"FromAddress"=>"no-reply@timesules.com",
	"FromName"=>"Timesules",
	"Host"=>"localhost",
	"Port"=>25,
	"Auth"=>false,
	"Secure"=>"ssl",
	"User"=>"",
	"Pass"=>"",
	"Timeout"=>10
	)
);
?>