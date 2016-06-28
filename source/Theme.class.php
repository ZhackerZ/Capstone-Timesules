<?php
if(!defined('IN_TIMESULES'))
	exit;

/**
 * Timesules
 * Theme.class.php
 *
 * This class handles anything that deals with template files
 * and other display characteristics.
 * @author Tyler Hadidon
 * @copyright 2012
 */
class Theme {

	/** Wheather text headers have been sent via the header() function */
	private $headersSent = FALSE;

	/** Stores the templates director */
	private $templatesDir = "./";

	//------------------------------------------------
	// Constructor / Destructor
	//------------------------------------------------

	/**
	 * Called when the theme class is constructed to close all
	 * open output buffers and start a new one.
	 */
	public function Theme() {
		$this->closeBuffers(FALSE);
		ob_start();
	}

	/**
	 * Called when the Theme class is being deleted, causing any open output buffers
	 * to call end_flush().
	 */
	public function __destruct() {
		$this->closeBuffers(TRUE);
	}

	/**
	 * Closes all open output buffers.
	 * @param Boolean $flush - If true, flushes when closed, otherwise
	 * cleans when closed.
	 */
	private function closeBuffers($flush = FALSE) {
		while(ob_get_level() > 0) {
			if($flush === TRUE)
				ob_end_flush();
			else
				ob_end_clean();
		}
	}

	//------------------------------------------------
	// Load
	//------------------------------------------------

	/**
	 * Loads a specified template file.
	 * @param String $page - The name of the template to load
	 * @param Array $data - Data array to be used in the template.
	 * @param Boolean $clearBuffer - Whether to clear the buffer and save its contents as a variable (TRUE)
	 * @return True if file was loaded successfully.
	 */
	public function load($page, $data = "", $clearBuffer = TRUE) {
		global $user, $manager, $sql;
		$theme = $this;

		// Takes array keys and makes them into variables!
		if(is_array($data))
			extract($data, EXTR_OVERWRITE);

		$ob_get_contents = ob_get_contents();
		if($clearBuffer === TRUE) ob_clean();

		$file = FALSE;
		if(file_exists($this->templatesDir.$page.'.php'))
			$file = (include $this->templatesDir.$page.'.php');

		if($file === FALSE) {
			if(preg_match("/<!DOCTYPE(.|\s)*?<head>(.|\s)*?<link.*?href=(\"|').*?\.css(\"|')/", ob_get_contents()) > 0)
				trigger_error("Unable to find {$page} template.", E_USER_WARNING);
			else
				trigger_error("Unable to find {$page} template.", E_USER_ERROR);
			return false;
		}

		return true;
	}

	//------------------------------------------------
	// Utilities
	//------------------------------------------------

	public function headersSent() {
		return $this->headersSent;
	}

	public function setDir($nDir) { $this->templatesDir = $nDir; }
}
?>
