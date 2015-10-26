<?php

namespace auth;

require_once 'include/modules/config.php';

Class Login {
	
	private $config;
	
	function __construct() {
		$this->config = new \Config();
	}
	
	public function LOGIN() {
		$this->config->theme->get_LoginScreen();
		if(isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])) {
			$this->get_login_auth($_POST['username'], $_POST['password']);		
		}
	}
	
	private function get_login_auth($username, $password) {
		$user = new \DB\SQL\Mapper($this->config->db, 'monitoring_users');
		$auth = new \Auth($user, array('id'=>'username', 'pw'=>'password'));
		
		if($auth->login($username, md5($password))) {
			$query = $this->config->db->exec("SELECT `id`,`user_type`, `active` FROM `monitoring_users` WHERE `username` = '".$username."' AND `password` = '".md5($password)."' ;");
		
			session_start();
		
			foreach($query as $user) {
				if($user['active']  != 'Y') {
					$this->config->f3->reroute('@login');
				}
				else {
					$_SESSION['user_type'] = $user['user_type'];
					$_SESSION['username'] = $username;
					$_SESSION['user_id'] = base64_encode($user['id']);
				}
			}
			$this->set_login_status('SUCCESS', $username);
			$this->config->f3->reroute('@home_profile');
		}
		else {
			$this->set_login_status('FAILED' , $username);
			$this->config->f3->reroute('@login');
		}
		
	}
	
	private function set_login_status($status, $username) {
		$ip_address = $this->config->request['ip-address'];
		$loc = $this->config->geo->location($ip_address);
		$this->config->db->exec("INSERT INTO `monitoring_login_edrs` ( `session-id`, `username`, `created_datetime`, `ip_address`, `country`, `country_code`, `user_agent`, `host`, `method`, `status`)
									VALUES ( '".session_id()."', '$username', NOW(), '$ip_address', '".$loc['country_name']."', '".$loc['country_code']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$_SERVER['HTTP_HOST']."', '".$_SERVER['REQUEST_METHOD']."', '$status' )		
								");
			
	}
	
}