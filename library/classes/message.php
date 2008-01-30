<?php
/**
 * class Message
 * 
 * @package Tarski
 * @since 2.1
 */
class Message {
	
	function init() {
		global $plugin_page, $pagenow;
		
		if ( $pagenow == 'index.php' || $plugin_page == 'tarski-options' ) {
			$messages = new Message;

			apply_filters('tarski_messages', $messages);

			if ( check_input($messages, 'object', 'Message') ) {
				$messages->output();
				$messages->clean();
			}
		} else {
			return;		
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