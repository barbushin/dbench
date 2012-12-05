<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_DbDataBuilder {
	
	protected $db;
	protected $tablesBuilders = array ();
	protected $bulkInsertCount;
	protected $debugCallback;
	protected $buildTimeLimit;

	public function __construct(DBench_Db_Abstract $db, $bulkInsertCount = 100, $buildTimeLimit = 0) {
		$this->db = $db;
		$this->bulkInsertCount = $bulkInsertCount;
		$this->buildTimeLimit = $buildTimeLimit;
	}

	public function setDebugCallback($callback) {
		if(!is_callable($callback)) {
			throw new Exception('Callback is not callable');
		}
		$this->debugCallback = $callback;
	}

	protected function debug($message, $showEveryThreeSeconds = false) {
		static $lastShow;
		
		if($showEveryThreeSeconds) {
			if(time() - $lastShow < 3) {
				return;
			}
			$lastShow = time();
		}
		
		if($this->debugCallback) {
			call_user_func($this->debugCallback, $message);
		}
	}

	public function addTableBuilder(DBench_TableDataGenerator $tableBuilder) {
		$this->tablesBuilders[] = $tableBuilder;
	}

	public function build() {
		$oldTimeLimit = ini_get('max_execution_time');
		set_time_limit($this->buildTimeLimit);
		
		$this->buildTableBuilder();
		foreach($this->tablesBuilders as $tableBuilder) {
			$this->buildTableBuilder($tableBuilder);
		}
		
		set_time_limit($oldTimeLimit);
	}

	protected function buildTableBuilder(DBench_TableDataGenerator $tableBuilder = null) {
		static $dependedBuildersQueue;
		if(!$tableBuilder) {
			$dependedBuildersQueue = array ();
			return;
		}
		
		$dependedBuildersQueue[spl_object_hash($tableBuilder)] = $tableBuilder;
		
		foreach($tableBuilder->getBuildersDependsOn() as $dependsTableBuilder) {
			if(!$dependsTableBuilder->isBuilt()) {
				if(!isset($dependedBuildersQueue[spl_object_hash($dependsTableBuilder)])) {
					$dependedBuildersQueue[spl_object_hash($dependsTableBuilder)] = $dependsTableBuilder;
					$this->buildTableBuilder($dependsTableBuilder);
				}
			}
		}
		
		$this->debug('Build data for table "' . $tableBuilder->getTable() . '"');
		
		$totalRowsBuilt = 0;
		$startTime = microtime(true);
		
		while( $newRows = $tableBuilder->buildRows($this->bulkInsertCount)) {
			$this->generateTableDataInDb($tableBuilder->getTable(), $newRows);
			$totalRowsBuilt += count($newRows);
			$timeLeft = ceil(($tableBuilder->getRowsNumber() - $tableBuilder->getBuiltRowsNumber()) * ((microtime(true) - $startTime) / $totalRowsBuilt));
			if($timeLeft) {
				$this->debug('Built ' . $tableBuilder->getBuiltRowsNumber() . ' rows. Timeleft ' . $timeLeft, true);
			}
		}
		$this->debug('Built ' . $tableBuilder->getBuiltRowsNumber() . ' rows');
	}

	protected function generateTableDataInDb($table, $dataRows) {
		$this->db->multiInsert($table, $dataRows);
	}
}