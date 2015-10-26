<?php

namespace home;

require_once 'include/modules/config.php';

Class Password {

	private $config;
	private $page = "Home / Change Password";
	function __construct() {
		$this->config = new \Config();
	}

	public function CHANGE_PASSWORD() {
		session_start();
		if(isset($_SESSION['username'])  && isset($_SESSION['user_id'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_changing_password() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}

	private function update_password($current, $new, $confirm ) {
		
		if($new == $confirm && $this->config->audit->entropy($current) >= 18) {
		
			$query = $this->config->db->exec(" UPDATE `monitoring_users`
												SET `password` = '".md5($new)."',
												`updated_datetime` = NOW(),
												`updated_by` = '".$_SESSION['username']."@".$this->config->request['ip-address']."'
												WHERE `id` = ".base64_decode($_SESSION['user_id'])."
														AND	`password` = '".md5($current)."';");
									
			if($this->config->db->count($query) > 0) {
				return 200;
			}
			else {
				return 404;
			}
		}
		else {
			return 405;
		}
	
	}

	private function get_template_for_changing_password() {
	
		return '
			<div class="form-panel">
	    	 		  
					'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->update_password($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])): '').'
	
			
                     <form class="form-horizontal style-form" action="" method="post">
	
                      	<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Current Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" maxlength="100" type="password" placeholder="Current Password" name="current_password">
                              </div>
                        </div>
						
						<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">New Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" maxlength="100" type="password" placeholder="New Password" name="new_password">
                              </div>
                        </div>
						
						<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Confirm Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" maxlength="100" type="password" placeholder="Confirm Password" name="confirm_password">
                              </div>
                        </div>
	
	    		 		
	    				<div class="form-group">
                              <div class="col-sm-12">
	    						<button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Change Password</button>
                              </div>
                        </div>
	
                      </form>
                  </div>		';
			
	}
		
	
	
	

}