<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */

class DBench_Db_MySQL extends DBench_Db_Abstract {

	const NAME_QUOTES = '`';
	const VALUE_QUOTES = "'";

	protected $connection;

	protected function connect($host, $user, $password, $database, $persistent) {
		if($persistent) {
			$this->connection = @mysql_pconnect($host, $user, $password);
		}
		else {
			$this->connection = @mysql_connect($host, $user, $password);
		}

		if(!$this->connection) {
			throw new Exception('Unable connect to DB');
		}

		if(!mysql_select_db($database, $this->connection)) {
			throw new Exception('Unable to select DB');
		}
	}

	public function quote($string, $withQuotes = true) {
		if(!is_scalar($string) && !is_null($string) && (!is_object($string) || !method_exists($string, '__toString'))) {
			throw new Exception('Trying to quote "' . gettype($string) . '". Value: "' . var_export($string, true) . '"');
		}
		return $withQuotes ? self::VALUE_QUOTES . mysql_real_escape_string($string, $this->connection) . self::VALUE_QUOTES : mysql_real_escape_string($string, $this->connection);
	}

	public function quoteName($name, $withQuotes = true) {
		if(!is_scalar($name)) {
			throw new Exception('Trying to quote "' . gettype($name) . '" as name. Value: "' . var_export($name, true) . '"');
		}
		if(!preg_match('/^[\d\w_]+$/', $name)) {
			throw new Exception('Wrong name "' . $name . '" given to quote');
		}
		return $withQuotes ? self::NAME_QUOTES . $name . self::NAME_QUOTES : $name;
	}

	public function sqlSelectByFilter($table, array $fieldsEquals, $fields = null) {
		return 'SELECT ' . ($fields ? implode(',', $this->quoteNames($fields)) : '*') . ' FROM ' . $this->quoteName($table) . ' WHERE ' . implode(' AND ', $this->quoteEquals($fieldsEquals));
	}

	public function getLastInsertId($result = null) {
		$lastId = mysql_insert_id($result);
		return $lastId ? $lastId : mysql_insert_id();
	}

	public function sqlInsert($table, array $data) {
		return 'INSERT INTO ' . $this->quoteName($table) . ' (' . $this->quoteNames(array_keys($data)) . ') VALUES (' . implode(', ', $this->quoteArray($data)) . ')';
	}

	public function sqlMultiInsert($table, array $rowsData) {
		$bulks = array ();
		foreach($rowsData as $rowData) {
			$bulks[] = implode(', ', $this->quoteArray($rowData));
		}
		return 'INSERT INTO ' . $this->quoteName($table) . ' (' . implode(',', $this->quoteNames(array_keys(reset($rowsData)))) . ') VALUES (' . implode('),(', $bulks) . ')';
	}

	public function query($sql) {
		$result = mysql_query($sql, $this->connection);
		if(!$result) {
			throw new Exception('SQL query: ' . $sql . ' Failed with error: ' . mysql_error($this->connection));
		}
		return $result;
	}

	public function fetchResultToArray($result) {
		$rows = array ();
		while( $row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		return $rows;
	}
}