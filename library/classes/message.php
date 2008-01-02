<?php
/**
 * class Message
 * 
 * @package Tarski
 * @since 2.1
 */
class Message {
	
	function Message($name=false, $message=false) {
		if($name && $message) {
			$this->add($name, $message);
		}
		if(get_tarski_var('debug')) {
			$this->add('debug', array('file' => 'debug.php'));
		}
	}
	
	function add($name, $message) {
		$this->$name = $message;
	}
	
	function remove($message) {
		unset($this->$message);
		return;
	}
	
	function output() {
		foreach ( $this as $name => $message ) {
			echo "<p id=\"message-$name\" class=\"message\">$message</p>";
		}
		
		$this->clean();
	}
	
	function clean() {
		unset($this);
		return;
	}
}

?>