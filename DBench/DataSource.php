<?php

/**
 * @see http://code.google.com/p/dbench
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */

abstract class DBench_DataSource {
	abstract public function getValue();

	public function __toString() {
		return (string)$this->getValue();
	}
}