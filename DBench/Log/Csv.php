<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

class DBench_Log_Csv {
	
	protected $outputFilepath;
	protected $logRows;
	protected $separator;

	public function __construct($outputFilepath, $separator = ';') {
		$this->separator = $separator;
		$this->setOutputFilepath($outputFilepath);
	}

	public function setOutputFilepath($outputFilepath) {
		$outDir = dirname($outputFilepath);
		if(!is_writable($outDir)) {
			chmod($outDir, 0777);
			if(!is_writable($outDir)) {
				throw new Exception('Have no permissions to create file ' . $outputFilepath);
			}
		}
		if(is_file($outputFilepath)) {
			unlink($outputFilepath);
		}
		$this->outputFilepath = $outputFilepath;
	}

	protected function quote($string) {
		return str_replace($this->separator, '\\' . $this->separator, $string);
	}

	public function logRow($row) {
		$this->logRows[] = $row;
	}

	protected function getCsvLineByRow($row) {
		return implode($this->separator, array_map(array($this, 'quote'), $row)) . "\n";
	}

	public function save() {
		if($this->logRows) {
			$fields = array_keys(reset($this->logRows));
			$logData = $this->getCsvLineByRow($fields);
			foreach($this->logRows as $row) {
				$logData .= $this->getCsvLineByRow($row);
			}
			file_put_contents($this->outputFilepath, $logData);
		}
	}
}