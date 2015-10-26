<?php

namespace auth;

require_once 'include/modules/config.php';

Class Logout {
	
	private $config;
	
	function __construct() {
		$this->config = new \Config();
	}
	
	public function LOGOUT() {
		session_start();
		session_unset();
		session_destroy();
		$this->config->f3->reroute('@login');
		
	}
	
}

?>
