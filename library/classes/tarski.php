<?php
/**
 * class Tarski
 * 
 * @package Tarski
 * @since 2.0.5
 */
class Tarski {

	var $debug;
	var $files;
	
	function set_defaults() {
		$this->debug = false;
		$this->files = array();
	}
	
	function get_var($var) {
		return $this->$var;
	}
	
	function set_var($var, $value) {
		if(!is_array($this->files))
			$this->set_defaults();
		
		if(in_array($var, $this->files))
			$this->set_file($var, $value);
		else
			$this->$var = $value;
	}
	
	function set_file($var, $value) {
		if(!is_array($this->files))
			$this->set_defaults();
		
		if((in_array($var, $this->files)) && file_exists($value))
			$this->$var = $value;
	}
	
}

function get_tarski_var($var) {
	global $tarski;
	
	if(!is_object($tarski)) {
		$tarski = new Tarski;
		$tarski->set_defaults();
	}
	
	return $tarski->get_var($var);
}

?>