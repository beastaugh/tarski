<?php
/**
 * class Message
 * 
 * @package Tarski
 * @since 2.1
 */
class Message {
	
	function init() {
		$messages = new Message;

		apply_filters('tarski_messages', $messages);

		if ( is_a($messages, 'Message') ) {
			$messages->output();
			$messages->clean();
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
			echo "<p id=\"tarski-message-$name\" class=\"tarski-message\">$message</p>";
		}
	}
	
	function clean() {
		unset($this);
		return;
	}
}

?>