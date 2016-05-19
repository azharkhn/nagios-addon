<?php

require_once 'themes/theme.php';
require_once 'include/fatfree/db/sql.php';
require_once 'include/fatfree/auth.php';
require_once 'include/fatfree/web/geo.php';
require_once 'include/fatfree/markdown.php';
require_once 'include/fatfree/log.php';
require_once 'include/fatfree/audit.php';
require_once 'include/fatfree/magic.php';
require_once 'include/fatfree/smtp.php';


class Config {
		
		public $request, 
			   $theme, 
			   $db, 
			   $md, 
			   $f3,
			   $log,
			   $geo,
			   $audit,
			   $smptp,
			   $site = 'http://localhost/monitoring-panels/'; 
		
		private $dbinfo = array(
								'dbhost' => 'localhost',
								'dbuser' => 'root',
								'dbpass' => '<password>',
								'dbport' => 3306,
								'dbname' => '<database>'
														);
		private $EmailInfo = array(
								'host' => '',
								'user' => '',
								'pass' => '',
								'port' => 465,
								'scheme' =>	'ssl'
						);
						
	function __construct() {
		
		global $f3;
		$this->f3 = $f3;
		$this->log = new Log('error.log');
		$this->db = new \DB\SQL('mysql:host='.$this->dbinfo['dbhost'].';port='.$this->dbinfo['dbport'].';dbname='.$this->dbinfo['dbname'],$this->dbinfo['dbuser'],$this->dbinfo['dbpass']);
		
		$this->smtp = new SMTP ( $this->EmailInfo['host'], $this->EmailInfo['port'], $this->EmailInfo['scheme'], $this->EmailInfo['user'], $this->EmailInfo['pass'] );
		$this->smtp->set('Errors-to', '');
		$this->smtp->set('From', '');
		$this->smtp->set('CC', '');
		$this->smtp->set('In-Reply-To', '');	
		
		$this->geo = \Web\Geo::instance();
		$this->md = \Markdown::instance();
		$this->audit = \Audit::instance();
		$this->theme = new theme();	
		$this->theme->set_siteURL($this->site);

		$this->request['ip-address'] = $this->get_remote_address();
		
	}
	
	private function get_remote_address() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
}
