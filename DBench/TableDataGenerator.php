<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_TableDataGenerator {
	
	protected $table;
	protected $fieldsSources;
	protected $rowsNumber = 0;
	protected $dependsOnBuilders = array ();
	protected $buildInProgress;
	protected $builtRowsNumber = 0;
	protected $lastSetRowsNumber = 0;

	public function __construct($table, $rowsNumber = 0) {
		$this->table = $table;
		$this->setRowsNumberToBuild($rowsNumber);
	}

	public function getFieldsNames() {
		return array_keys($this->fieldsNames);
	}

	public function addField($field, DBench_DataSource $source) {
		$this->fieldsSources[$field] = $source;
	}

	public function generateRow() {
		$row = array ();
		foreach($this->fieldsSources as $field => $source) {
			$row[$field] = $source->getValue();
		}
		return $row;
	}

	public function setRowsNumberToBuild($rowsNumberToBuild) {
		$this->rowsNumber += $rowsNumberToBuild;
	}

	public function getRowsNumber() {
		return $this->rowsNumber;
	}

	public function getTable() {
		return $this->table;
	}

	public function getBuildersDependsOn() {
		return $this->dependsOnBuilders;
	}

	public function addDependsOnTableBuilder(DBench_TableDataGenerator $dependsOnBuilder) {
		$this->dependsOnBuilders[] = $dependsOnBuilder;
	}

	public function getBuiltRowsNumber() {
		return $this->builtRowsNumber;
	}

	public function getLastBuiltRowsNumber() {
		return $this->lastSetRowsNumber;
	}

	public function buildRows($rowsNumber) {
		if(!$this->buildInProgress && !$this->isBuilt()) {
			$this->lastSetRowsNumber = $this->builtRowsNumber;
		}
		
		$this->buildInProgress = true;
		
		$newRows = array ();
		$newRowsNumber = $this->rowsNumber - $this->builtRowsNumber > $rowsNumber ? $rowsNumber : $this->rowsNumber - $this->builtRowsNumber;
		for($i = 0; $i < $newRowsNumber; $i++) {
			$newRows[] = $this->generateRow();
		}
		$this->builtRowsNumber += $newRowsNumber;
		
		if($this->isBuilt()) {
			$this->buildInProgress = false;
		}
		return $newRows;
	}

	public function isBuilt() {
		return $this->builtRowsNumber >= $this->rowsNumber;
	}
}