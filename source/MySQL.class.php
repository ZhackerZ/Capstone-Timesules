<?php
if(!defined('IN_TIMESULES'))
	exit;

/**
 * MySQL handler class: Handles database connections and queries.
 *
 * @author Tyler Hadidon
 * @version 3.0
 * @copyright 2011
 */
class SQL {

	/** Houses the database connection identifier */
	private $connection = FALSE;
	/** Holds the prefix as defined in the constructor */
	private $prefix = "";
	/** Stores the debugging stauts */
	private $debug = false;

	/** Holds query information:<br>Query Text, Success status, Number of rows returned or effected, Time, and Errors */
	private $qInfo = Array();
	/** The last mysql_query result */
	private $lresult = "";
	/** The last query sent */
	private $lquery = FALSE;

	/**
	 * Constructs a new MySQL Object. Connection information can be input as individual arguments
	 * or as an array with keys named the same as the arguments below.
	 * Ex: Array("host"=>"localhost", "user"=>"root", "pass"=>"", "db"=>"MyDB", "prefix"=>"pre_", "debug"=>FALSE);
	 *
	 * @param String $host - The address to the MySQL server (<code>"localhost"</code>)
	 * @param String $user - The server username (<code>"root"</code>)
	 * @param String $pass - The server password (<code>""</code>)
	 * @param String $db - The database to select (<code>""</code>)
	 * @param String $prefix - A prefix to add before table names (<code>""</code>)<br><br>
	 * Ex: If set to <code>"pre_"</code> then tables should be named <code>"pre_users"</code>,
	 * <code>"pre_log"</code>, <code>"pre_posts"</code>, etc.
	 * @param Boolean $debug - Puts the class into debugging mode if set to true (<code>FALSE</code>).<br>
	 * Alternativly you can define <code>SQL_DEBUG</code> as <code>TRUE</code> for the same effect.
	 * @throws SQLException
	 */
	public function connect($host = "localhost", $user = "mcouncil3", $pass = 'TimesulesSp15', $db = "timesules", $prefix = "", $debug = FALSE) {
		// First see if we are passing in an array
		if(is_array($host))
			extract($host, EXTR_OVERWRITE);

		$this->connection = @mysql_connect($host, $user, $pass);

		// Check connection
		if($this->connection === FALSE)
			throw new SQLException("Failed to connect to the MySQL server. ".$this->error(), SQLException::MYSQL_CONNECT);

		// Select database
		if(@mysql_select_db($db, $this->connection) !== TRUE)
				throw new SQLException("Failed to select MySQL database: \"{$db}\". Please check that the database exists.", SQLException::MYSQL_SELECT_DB);

		// Set the prefix and debugging
		$this->prefix = $prefix;
		$this->debug = ((defined('SQL_DEBUG') && SQL_DEBUG == TRUE) || $debug)?TRUE:FALSE;
	}

	/**
	 * Executes a MySQL query and handles the result.
	 *
	 * @param String $query - The SQL Query to execute
	 * @param Boolean|String $debug - Debug this query (<code>FALSE</code>)<br>
	 * If <code>"now"</code> is passed, debugging information is printed right after the execution.<br>
	 * If <code>TRUE</code> is passed, debugging information will be printed once the page
	 * has finished loading.
	 * @return The <code>MySQL_ResourceID</code> of results or <code>TRUE</code> on success. Otherwise
	 * <code>FALSE</code>.
	 * @throws SQLException
	 */
	public function query($query, $debug = FALSE) {
		$this->connect();
		// Check that we have a connection
		if($this->connection === FALSE)
			throw new SQLException("No Connection", SQLException::MYSQL_NO_CONNECT);

		// Save query text
		$this->lquery = $query;

		// Do the query
		$s = microtime(true);
		$this->lresult = @mysql_query($query, $this->connection);
		$e = microtime(true);
		$time = $e-$s;

		// Check debugging options
		if($debug === "now")
			echo "SQL-DEBUG: \"{$query}\"<br />Error:{$this->error()}";
		else if($debug === TRUE || $this->debug === TRUE) {
			$this->qInfo[] = Array(
				"query"=>$query,
				"time"=>$time,
				"rows"=>$this->rows(),
				"success"=>(($this->lresult!==FALSE)?TRUE:FALSE),
				"error"=>$this->error()
			);
		}

		return $this->lresult;
	}

	/**
	 * Selects data from a MySQL table.
	 *
	 * @param String $table - The table to select from
	 * @param String $fields - Table fields to return (<code>"*"</code>)
	 * @param String $args - Query arguments (<code>""</code>)
	 * @param Boolean|String $debug - Debug this query (<code>FALSE</code>)<br>
	 * If <code>"now"</code> is passed, debugging information is printed right after the execution.<br>
	 * If <code>TRUE</code> is passed, debugging information will be printed once the page
	 * has finished loading.
	 * @return The number of rows selected or <code>FALSE</code> if query failed.
	 */
	public function select($table, $fields = "*", $args = "", $debug = FALSE) {
		// Create query
		$query = "SELECT {$fields} FROM `{$this->prefix}{$table}`";

		// check if we have a WHERE or similar clause
		if($args != "")
			$query .= ' '.$args;

		// Do the dirty work
		if($this->query($query, $debug)!==FALSE)
			return $this->rows();
		else
			return FALSE;
	}

	/**
	 * Inserts a new row of data.
	 *
	 * @param String $table - The table to insert into
	 * @param String|Array $vals - The values to insert or an array of values with colums as the key:<br />
	 * <code>"'value1','value2'"</code><br />
	 * <code>Array("column1"=>"value1", "column2"=>"value2");</code>
	 * @param Boolean|String $debug - Debug this query (<code>FALSE</code>)<br>
	 * If <code>"now"</code> is passed, debugging information is printed right after the execution.<br>
	 * If <code>TRUE</code> is passed, debugging information will be printed once the page
	 * has finished loading.
	 * @return The last inserted ID or <code>TRUE</code> if not an auto-increaser table. Otherwise,
	 * <code>FALSE</code> if query failed.
	 */
	public function insert($table, $vals, $debug = FALSE) {
		// Are we using the array option?
		if(is_array($vals)) {
			// Get my cols and values
			$cols = "`".implode("`,`", array_keys($vals))."`";
			$values = "";

			// Check the values for SQL statements (NULL and NOW())
			foreach($vals as $val) {
				// If NULL, add NULL
				if(is_null($val))
					$values .= 'NULL,';
				// If "NOW()"
				// TODO: IS this safe? It should be fine..
				else if($val === 'NOW()')
					$values .= 'NOW(),';
				else
					$values .= "'$val',";
			}
			$values = substr($values, 0, -1); // remove last comma (,)

			$query = "INSERT INTO `{$this->prefix}{$table}` ({$cols}) VALUES ({$values})";
		} else
			$query = "INSERT INTO `{$this->prefix}{$table}` VALUES ({$vals})";

		// Do the dirty work
		if($this->query($query, $debug)!== FALSE) {
			$id = $this->lastID();
			return ($id) ? $id : TRUE;
		} else
			return FALSE;
	}

	/**
	 * Updates one or more columns in a MySQL table.
	 *
	 * @param String $table - The table to update
	 * @param List|Array $set - The SET string or an array of values with colums as the key to update.<br />
	 * Ex: <code>"`col1`='new1',`col2`='new2'"</code><br />
	 * <code>Array("col1"=>"new1", "col2"=>"new2");</code>
	 * @param String $arg - The ending arguments
	 * @param Boolean|String $debug - Debug this query (<code>FALSE</code>)<br>
	 * If <code>"now"</code> is passed, debugging information is printed right after the execution.<br>
	 * If <code>TRUE</code> is passed, debugging information will be printed once the page
	 * has finished loading.
	 * @return The number of rows the update affected or <code>FALSE</code> if error occured
	 */
	public function update($table, $set, $arg = "", $debug = FALSE) {
		// Are we using an array?
		if(is_array($set)) {
			$setStr = "";
			foreach($set as $col=>$val) {
				$setStr .= "`{$col}`='{$val}',";
			}
			$setStr = substr($setStr, 0, -1); // remove last comma (,)

			$query = "UPDATE `{$this->prefix}{$table}` SET {$setStr}";
		} else
			$query = "UPDATE `{$this->prefix}{$table}` SET {$set}";

		// Add ending arguments
		if($arg != "")
			$query .= " ".$arg;

		// Do the dirty work
		if($this->query($query, $debug)!== FALSE)
			return $this->rows();
		else
			return FALSE;
	}

	/**
	 * Deletes one or more rows from $table
	 *
	 * @param String $table - The table to delete from
	 * @param String $arg - The deleting WHERE clause.
	 * <b>WARNING:</b> If left blank, the table will truncate!<br />
	 * Ex: <code>$sql->delete("test", "`col1`='value1'");</code>
	 * @param Boolean|String $debug - Debug this query (<code>FALSE</code>)<br>
	 * If <code>"now"</code> is passed, debugging information is printed right after the execution.<br>
	 * If <code>TRUE</code> is passed, debugging information will be printed once the page
	 * has finished loading.
	 * @return The number of rows deleted or <code>FALSE</code> if error occured
	 */
	public function delete($table, $arg = "", $debug = FALSE) {
		$query = "DELETE FROM `{$this->prefix}{$table}`";

		// Is there a WHERE clause?
		if($arg !=  "")
			$query .= " WHERE {$arg}";

		if($this->query($query, $debug)!== FALSE)
			return $this->rows();
		else
			return FALSE;
	}

	/**
	 * Escapes data so it is able to be placed into the database and by default strips slashes if Magic Quotes is on.
	 *
	 * @param String $data - The data to escape
	 * @param Boolean $strip - If set to TRUE, will call <code>stripslashes</code> beforehand
	 * @return The escaped string safe for placement in database.
	 */
	public function escape($data, $strip = NULL) {
		// Check if null for default
		if(is_null($strip))
			$strip = get_magic_quotes_gpc();

		// If $data is an array, stripslashes and mysql_real_escape_string are not recersive, so...
		if(is_array($data)) {
			$new = Array();
			foreach($data as $key=>$value) {
				$new[$key] = $this->escape($value, $strip);
			}
			return $new;
		}

		// Check for stripslashes
		if($strip)
			$data = stripslashes($data);

		return mysql_real_escape_string($data, $this->connection);
	}

	/**
	 * Fetch row from a result.
	 *
	 * @param MySQL_Constant $type - Type of array to return: <code>MYSQL_ASSOC</code>, <code>MYSQL_NUM</code>,
	 * or <code>MYSQL_BOTH</code>. (<code>MYSQL_BOTH</code>)
	 * @param MySQL_ResourceID $result - The resource ID to fetch from. (<code>$this->lresult</code>)
	 * @return Array of values for the row or FALSE if failed to fetch row.
	 */

	// public function fetch($type = MYSQL_BOTH, $result = NULL) {
	public function fetch($type = MYSQL_ASSOC, $result = NULL) {
		if($result === NULL)
			$result = $this->lresult;

		$row = @mysql_fetch_array($result, $type);
		return ($row)?$row:FALSE;
	}

	/**
	 * Fetch all rows from a result. Resets the data pointer to 0, so it returns ALL rows, not just from the last
	 * mysql_fetch_array() call.
	 *
	 * @param MySQL_Constant $type - Type of array to return: <code>MYSQL_ASSOC</code>, <code>MYSQL_NUM</code>,
	 * or <code>MYSQL_BOTH</code>. (<code>MYSQL_BOTH</code>)
	 * @param MySQL_ResourceID $result - The resource ID to fetch from. (<code>$this->lresult</code>)
	 * @return Array of values for the row or FALSE if failed to fetch row.
	 */
	public function fetchAll($type = MYSQL_ASSOC, $result = NULL) {
		$ret = Array();
		@mysql_data_seek(0);
		while(($row = $this->fetch($type, $result)) != FALSE) {
			$ret[] = $row;
		}

		return $ret;
	}

	/**
	 * Returns the number of rows selected OR affected by last result.
	 *
	 * @return The number of rows selected or affected by previous query. -1 if previous query failed.
	 */
	public function rows() {
		if($this->lresult === TRUE)
			return @mysql_affected_rows($this->connection);
		else if($this->lresult !== FALSE)
			return @mysql_num_rows($this->lresult);
		else
			return -1;
	}

	/**
	 * Gets the previously inserted ID on this connection.
	 *
	 * @return The previously inserted <code>AUTO_INCREMENT</code> ID
	 */
	public function lastID() {
		return @mysql_insert_id($this->connection);
	}

	/**
	 * Gets the last <code>mysql_error message</code> for connection <code>$con</code>.
	 *
	 * @param <b>MySQL Connection Link</b> <b>$con</b> - A MySQL connection.
	 * Defaults to $this->connection
	 * @return The last <code>mysql_error</code> message.
	 */
	public function error($con = NULL) {
		if($con === NULL)
			$con = $this->connection;

		// If the connection failed (FALSE), then call without argument.
		if($con === FALSE)
			return @mysql_error();
		else
			return @mysql_error($con);
	}

	/** Prints MySQL queries stored in the $this->qInfo array. */
	private function listQueries() {
		$isAjax = implode("|", headers_list());
		if(count($this->qInfo) <= 0 || strpos($isAjax, "json") !== FALSE)// && $this->debug !== TRUE)
			return;

			/*$this->qInfo[] = Array(
				"query"=>$query,
				"time"=>$time,
				"rows"=>$this->rows(),
				"success"=>(($this->lresult!==FALSE)?TRUE:FALSE),
				"error"=>$this->error()
			);*/

		// Calculate total time and total number of queries
		$count = count($this->qInfo);

		$time = 0;
		foreach($this->qInfo as $val) {
			$time += $val["time"];
		}

		echo "<script type='text/javascript'>
function toggleSQLDebugTable(obj) {
	var table = document.getElementById('SQLDebugTable');
	if(table.style.display == 'none') {
		obj.innerHTML = '^';
		table.style.display = 'block';
	} else {
		obj.innerHTML = 'V';
		table.style.display = 'none';
	}
}
</script>
<div style='text-align: center;cursor: pointer;width: 20px;height: 20px;position: absolute;top: 8px;left: 8px;z-index:10000;' onmouseup='toggleSQLDebugTable(this);'>V</div>
<table id='SQLDebugTable' cellspacing='0' cellpadding='3' style='background-color:#F3F8FA;border:1px solid #000;position: absolute;top: 20px;left: 0px;z-index:10000;display:none;'>
<tr><th colspan='3'>SQL Debug</th></tr>
<tr><td>Number of Queries:</td><td colspan='2'>{$count}</td></tr>
<tr><td>Total Query Time:</td><td colspan='2'>{$time}</td></tr>
<tr><th>Rows</th><th>Time</th><th>Query</th></tr>";

		foreach($this->qInfo as $info) {
			// Set colors: Green for success, Yellow if no rows returned/affected, Red on failure
			if($info["success"] == TRUE && $info["rows"] > 0)
				$color = "00C000";
			else if($info["success"] == TRUE)
				$color = "E0E000";
			else
				$color = "C00000";
			$info["query"] = nl2br($info["query"]);

			echo "<tr class='ui-success'><td>{$info["rows"]}</td><td>{$info["time"]}</td><td>{$info["query"]}</td></tr>\n";

			if($info["success"] != TRUE)
				echo "<tr style='background-color: #{$color};'><td>Error Response:</td><td colspan='2'>{$info["error"]}</td></tr>\n";
		}

		echo '<tr><td colspan="3"></td></tr></table>';
	}

	/**
	 * <p>Turn on or off SQL Debugging for all queries beyond this call; But note, if debugging was previously
	 * turned on and queries were made, the debugging information will still be printed at the end of
	 * execution, but queries from this point forward will not be listed.</p>
	 *
	 * @param Boolean $debug - If set to TRUE, debuging is turned on and every query from here on will
	 * be logged and printed at the end.
	 */
	public function setDebugging($debug) {
		$this->debug = ($debug === TRUE)?TRUE:FALSE;
	}

	public function setPrefix($nPrefix) { $this->prefix = $nPrefix; }

	public function getConnection() { return $this->connection; }
	public function getPrefix() { return $this->prefix; }
	public function isDebugging() { return $this->debug; }
	public function getQueryInfo() { return $this->qInfo; }
	public function getLastResult() { return $this->lresult; }
	public function getLastQuery() { return $this->lquery; }

	/** Called when the class is destructed. */
	public function __destruct() {
		$this->listQueries();

		if($this->connection)
			@mysql_close($this->connection);
	}
}

/**
 * SQLException class used for custom Exception handling.
 * @author Tyler Hadidon
 */
class SQLException extends Exception {
	const MYSQL_CONNECT = 300;
	const MYSQL_SELECT_DB = 301;
	const MYSQL_NO_CONNECT = 302;
	const MYSQL_QUERY_ERROR = 303;
	const MYSQL_ERROR = 500; // Generic Error

	/**
	 * Constructs a new SQLException.
	 *
	 * @param String $message - The message of the exception
	 * @param SQLException_Constant $code - The custom exception code
	 */
	public function __construct($message, $code = SQLException::MYSQL_ERROR) {

		// Setup defaults
		switch($code) {
		case SQLException::MYSQL_CONNECT:
		case SQLException::MYSQL_SELECT_DB:
		case SQLException::MYSQL_NO_CONNECT:
		case SQLException::MYSQL_QUERY_ERROR:
			// Code this fine!
			break;

		// Number not found, set to generic error
		default:
			$code = SQLException::MYSQL_ERROR;
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
		case SQLException::MYSQL_CONNECT:
			return "MySQL Connect Error";
		case SQLException::MYSQL_SELECT_DB:
			return "MySQL Select Database Error";
		case SQLException::MYSQL_NO_CONNECT:
			return "No MySQL Connection";
		case SQLException::MYSQL_QUERY_ERROR:
			return "MySQL Query Error";
		default:
			return "MySQL Error";
		}
	}
}
?>