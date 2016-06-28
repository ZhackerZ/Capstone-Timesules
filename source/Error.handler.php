<?php
/**
 * Timesules
 * Error.handler.php
 * 
 * This file is the error handler setup
 * for Timsules.
 * @author Tyler Hadidon
 * @copyright 2012
 */

define('TIMESULES_ERROR_SET', TRUE);

/**
 * Error Logging settings
 */
$timesulesErrorLoggingSettings = Array(
	"enabled"=>TRUE,
	"level"=>4, //0=OFF,1=FATAL(E_USER_ERROR|SHUTDOWN_ERROR),2=WARNINGS(E_USER_WARNING|E_WARNING),3=NOTICES(E_USER_NOTICE,E_NOTICE),4=USER(400,404,403,500)
	"path"=>$_SERVER["DOCUMENT_ROOT"]."/source/logs/",
	"files"=>Array("fatal.log","warning.log","notice.log","httperror.log"),
	"emailLevel"=>0, // See above. Must be set at or below the logging level to receive the email
	"emailAddress"=>"tylerh@ignition-games.com"
);

function tLogError($level, $error) {
	// Get the settings and check if we can even log it
	global $timesulesErrorLoggingSettings;
	$settings = @$timesulesErrorLoggingSettings;
	if($level > @$settings["level"])
		return;
	
	// Get a text version of the error type
	$errorType = @$error["type"];
	$errorType = ($errorType==E_USER_ERROR||$errorType==E_ERROR)?"FATAL ERROR":(($errorType==E_USER_WARNING||$errorType==E_WARNING)?"WARNING":"NOTICE");

	// Generate the message
	$msg = "----------------------------------------------------------------------\n";
	$msg .= @$errorType." | ".@date("n/j/y g:i a T")."\n";
	$msg .= "Error: ".@$error["type"]."\n";
	if($error["file"] != "NA")
		$msg .= "File[line]: ".@$error["file"]."[".@$error["line"]."]\n";
	$msg .= "Msg: ".@str_replace("\n","",$error["message"])."\n";
	$msg .= "RegMethod: ".@$_SERVER["REQUEST_METHOD"]."\n";
	$msg .= "ReqURI: ".@$_SERVER["REQUEST_URI"]."\n";
	$msg .= "FromURL: ".@$_SERVER["HTTP_REFERER"]."\n";
	@file_put_contents($settings["path"].$settings["files"][$level-1], $msg, FILE_APPEND);
	
	// Email
	if($level <= $settings["emailLevel"] && $settings["emailAddress"] != "")
		@mail($settings["emailAddress"], "[ALARM] Timesules.com | ".$errorType, $msg);
}
	
function timesulesErrorHandler($errno, $errstr, $errfile, $errline) {
	// NOT IN REPORTING
	if(!(error_reporting() & $errno))
		return true;
		
	// Make sure root is defined
	if(!defined('TEMPLATE_DIR')) {
		global $root;
		if($root == "")
			$root = $_SERVER['DOCUMENT_ROOT'];
		$templatesFolder = $root.'source/templates/errors/';
	} else
	 	$templatesFolder = constant('TEMPLATE_DIR');
	 	
	// Create error array to be used in template files
	$error = Array("type"=>$errno,"message"=>$errstr,"file"=>$errfile,"line"=>$errline);
	
	switch ($errno) {
	case E_WARNING:
	case E_USER_WARNING:
		tLogError(2, $error);
		if(file_exists($templatesFolder.'warning.php'))
			@include $templatesFolder.'warning.php';
		else
			echo "<div class='ui-error'><strong>Warning</strong>: {$errstr} in <strong>{$errfile}</strong> on line <strong>{$errline}</strong></div>\n";
		break;
		
	case E_NOTICE:
	case E_USER_NOTICE:
		tLogError(3, $error);
		if(file_exists($templatesFolder.'notice.php'))
			@include $templatesFolder.'notice.php';
		else
			echo "<div class='ui-notice'><strong>Notice</strong>: {$errstr} in <strong>{$errfile}</strong> on line <strong>{$errline}</strong></div>\n";
		break;
		
	case E_USER_ERROR:
		// Clear previous buffer if I can
		if(ob_get_level() > 0)
			ob_end_clean();
			
		tLogError(1, $error);
		if(file_exists($templatesFolder.'fatal.php'))
			@include $templatesFolder.'fatal.php';
		else
			echo '<!DOCTYPE HTML>
			<html><div class="ui-error"><strong>Fatal Error:</strong><br />
			<head><title>Fatal Error</title></head>
			<body>
			<p style="width: 600px;">An error occured while trying to display this page and could not recover.
			Additionally, while processing this error, the server was unable to find the proper error page.
			Support has been notified of this error and will fix it as soon as possible.<br />
			Sorry for the inconvenience.<br /><br />
			Timesules Staff</p></div>
			<!-- Developer Debug Info
			'.print_r($error, true).'
			-->
			</body>
			</html>';
		exit;
		break;
		
	case E_USER_DEPRECATED:
	case E_DEPRECATED:
		tLogError(3, $error);
		if(file_exists($templatesFolder.'deprecated.php'))
			@include $templatesFolder.'deprecated.php';
		else
			echo "<div class='ui-warn'><strong>Deprecation Notice</strong>: {$errstr} in <strong>{$errfile}</strong> on line <strong>{$errline}</strong></div>\n";
		break;
		
	default: // E_STRICT, E_RECOVERABLE_ERROR
		tLogError(3, $error);
		if(file_exists($templatesFolder.'default.php'))
			@include $templatesFolder.'default.php';
		else
			echo "<div class='ui-warn'><strong>Default Notice</strong>: {$errstr} in <strong>{$errfile}</strong> on line <strong>{$errline}</strong></div>\n";
		break;
	}
	
	return true;
}
set_error_handler('timesulesErrorHandler');

// Setup shutdown function
function timesulesShutdown() {
	$error = error_get_last();
	
	// If there was a fatal error, use my own method of printing
	if($error != NULL && (
	$error["type"] == E_ERROR ||
	$error["type"] == E_PARSE ||
	$error["type"] == E_COMPILE_ERROR ||
	$error["type"] == E_CORE_ERROR ||
	$error["type"] == E_CORE_WARNING 
	)) {
		tLogError(1, $error);

		// Clear previous buffer if I can
		if(ob_get_level() > 0)
			ob_end_clean();
			
		$templatesFolder = $_SERVER['DOCUMENT_ROOT'].'/source/templates/errors/';

		if(file_exists($templatesFolder.'fatal.php'))
			@include $templatesFolder.'fatal.php';
		else
			echo '<!DOCTYPE HTML>
			<html><div class="ui-error"><strong>Fatal Error:</strong><br />
			<head><title>Fatal Error</title></head>
			<body>
			<p style="width: 600px;">An error occured while trying to display this page and could not recover.
			Additionally, while processing this error, the server was unable to find the proper error page.
			Support has been notified of this error and will fix it as soon as possible.<br />
			Sorry for the inconvenience.<br /><br />
			Timesules Staff</p></div>
			<!-- Developer Debug Info
			'.print_r($error, true).'
			-->
			</body>
			</html>';
	}
}
register_shutdown_function('timesulesShutdown');
?>