<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Db_PostgreSQL extends DBench_Db_Abstract {
	
	protected $connection;
	
	const NAME_QUOTES = '"';
	const VALUE_QUOTES = "'";

	protected function connect($host, $user, $password, $database, $persistent) {
		$connectionString = "host=$host dbname=$database user=$user password=$password";
		if($persistent) {
			$this->connection = @pg_pconnect($connectionString);
		}
		else {
			$this->connection = @pg_connect($connectionString);
		}
		
		if(!$this->connection) {
			throw new Exception('Unable connect to DB');
		}
	}

	public function quote($string, $withQuotes = true) {
		if(!is_scalar($string) && !is_null($string) && (!is_object($string) || !method_exists($string, '__toString'))) {
			throw new Exception('Trying to quote "' . gettype($string) . '". Value: "' . var_export($string, true) . '"');
		}
		return $withQuotes ? "'" . pg_escape_string($string) . "'" : pg_escape_string($string);
	}

	public function quoteName($name, $withQuotes = true) {
		if(!is_scalar($name)) {
			throw new Exception('Trying to quote "' . gettype($name) . '" as name. Value: "' . var_export($name, true) . '"');
		}
		if(!preg_match('/^[\d\w_]+$/', $name)) {
			throw new Exception('Wrong name "' . $name . '" given to quote');
		}
		return $withQuotes ? '"' . $name . '"' : $name;
	}

	public function sqlSelectByFilter($table, array $fieldsEquals, $fields = null) {
		return 'SELECT ' . ($fields ? implode(',', $this->quoteNames($fields)) : '*') . ' FROM ' . $this->quoteName($table) . ' WHERE ' . implode(' AND ', $this->quoteEquals($fieldsEquals));
	}

	public function getLastInsertId($result = null) {
		return pg_last_oid();
	}

	public function sqlInsert($table, array $data) {
		return 'INSERT INTO ' . $this->quoteName($table) . ' (' . $this->quoteNames(array_keys($data)) . ') VALUES (' . implode(', ', $this->quoteArray($data)) . ')';
	}

	public function sqlMultiInsert($table, array $rowsData) {
		$bulks = array();
		foreach($rowsData as $rowData) {
			$bulks[] = implode(', ', $this->quoteArray($rowData));
		}
		return 'INSERT INTO ' . $this->quoteName($table) . ' (' . implode(',', $this->quoteNames(array_keys(reset($rowsData)))) . ') VALUES (' . implode('),(', $bulks) . ')';
	}

	public function query($sql) {
		$result = @pg_query($this->connection, $sql);
		if(!$result) {
			throw new Exception('SQL query: ' . $sql . ' Failed with error: ' . pg_last_error($this->connection));
		}
		return $result;
	}

	public function fetchResultToArray($result) {
		$rows = array();
		while($row = pg_fetch_assoc($result)) {
			$rows[] = $row;
		}
		return $rows;
	}
}