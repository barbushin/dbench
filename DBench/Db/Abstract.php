<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

abstract class DBench_Db_Abstract {
	
	const NAME_QUOTES = '`';
	const VALUE_QUOTES = "'";

	public function __construct($host, $user, $password, $database, $persistent = true) {
		$this->connect($host, $user, $password, $database, $persistent);
	}

	abstract protected function connect($host, $user, $password, $database, $persistent);

	/**************************************************************
	 QUOTERS
	 **************************************************************/
	
	abstract protected function quote($value, $withQuotes = true);

	abstract protected function quoteName($value, $withQuotes = true);

	public function quoteArray(array $values, $withQuotes = true) {
		foreach($values as &$value) {
			$value = $this->quote($value, $withQuotes);
		}
		return $values;
	}

	public function quoteNames(array $names) {
		foreach($names as &$name) {
			$name = $this->quoteName($name);
		}
		return $names;
	}

	public function quoteEquals(array $fieldsValues, $implode = false) {
		$equals = array();
		foreach($fieldsValues as $field => $value) {
			$equals[] = $this->quoteName($field) . '=' . $this->quote($value);
		}
		return $equals;
	}

	/**************************************************************
	 SQL
	 **************************************************************/
	
	abstract public function sqlInsert($table, array $data);

	abstract public function sqlMultiInsert($table, array $rowsData);

	abstract public function sqlSelectByFilter($table, array $fieldsEquals, $fields = null);

	/**************************************************************
	 REQUESTS
	 **************************************************************/
	
	abstract public function query($sql);

	public function fetch($sql) {
		return $this->fetchResultToArray($this->query($sql));
	}

	abstract public function fetchResultToArray($result);

	abstract public function getLastInsertId($result = null);

	public function insert($table, array $data) {
	}

	public function multiInsert($table, array $rowsData, $lastIdsRequired = false) {
		if($lastIdsRequired) {
			$lastIds = array();
			foreach($rowsData as $data) {
				$lastIds[] = $this->insert($table, $data);
			}
			return $lastIds;
		}
		else {
			$result = $this->query($this->sqlMultiInsert($table, $rowsData));
		}
	}
}