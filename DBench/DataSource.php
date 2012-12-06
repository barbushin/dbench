<?php

/**
 * @see https://github.com/barbushin/dbench
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */

abstract class DBench_DataSource {
	abstract public function getValue();

	public function __toString() {
		return (string)$this->getValue();
	}
}