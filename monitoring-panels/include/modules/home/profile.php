<?php

namespace home;

require_once 'include/modules/config.php';

Class Profile {

	private $config;
	private $page = "Home / Profile Settings";

	function __construct() {
		$this->config = new \Config();
	}

	public function EDIT_PROFILE() {
		session_start();
		if(isset($_SESSION['username'])  && isset($_SESSION['user_id'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_editing_profile() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	private function update_profile($first_name, $middle_name, $last_name, $contact_no1, $contact_no2, $email1, $email2 ) {

		if($this->config->audit->email($email1, TRUE) && ($email2 != '' && $email2 != NULL ? $this->config->audit->email($email1, TRUE) : TRUE) ) {
				$query = $this->config->db->exec(" UPDATE `monitoring_user_profile`
												SET `first_name` = '$first_name',
												`middle_name` = '$middle_name',
												`last_name` = '$last_name',
												`contact_no1` = '$contact_no1',
												`contact_no2` = '$contact_no2',
												`primary_email` = '$email1',
												`secondary_email` = '$email2',
												`updated_datetime` = NOW(),
												`updated_by` = '".$_SESSION['username']."@".$this->config->request['ip-address']."'
												WHERE `user_id` = ".base64_decode($_SESSION['user_id']).";");

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

	private function get_template_for_editing_profile() {

		$query = $this->config->db->exec("SELECT `first_name`,
												`middle_name`,
												`last_name`,
												`contact_no1`,
												`contact_no2`,
												`primary_email`,
												`secondary_email`
											FROM `monitoring_user_profile`
												WHERE `user_id` = '".base64_decode($_SESSION['user_id'])."';");

		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
			return '
			<div class="form-panel">
	    	 
				
					'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->update_profile($_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['contact1'], $_POST['contact2'], $_POST['email1'], $_POST['email2'])): '').'
	
					
                     <form class="form-horizontal style-form" action="" method="post">

                      	<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">First Name</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="text" '.($result['first_name'] != '' ? 'value="'.$result['first_name'].'"' : 'placeholder="First Name"').' name="first_name">
                              </div>
                        </div>

	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Middle Name</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="text" '.($result['middle_name'] != '' ? 'value="'.$result['middle_name'].'"' : 'placeholder="Middle Name"').' name="middle_name">
                              </div>
                        </div>
					    
					    <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Last Name</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="text" '.($result['last_name'] != '' ? 'value="'.$result['last_name'].'"' : 'placeholder="Last Name"').' name="last_name">
                              </div>
                        </div>
					    
					     <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Primary Contact #</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="tel" '.($result['contact_no1'] != '' ? 'value="'.$result['contact_no1'].'"' : 'placeholder="+ 92 XXX XXXXXXX"').' name="contact1">
                              </div>
                        </div>
					    
					    <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Secondary Contact #</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="tel" '.($result['contact_no2'] != '' ? 'value="'.$result['contact_no2'].'"' : 'placeholder="+ 92 XXX XXXXXXX"').' name="contact2">
                              </div>
                        </div>
					    
					    <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Primary Email Address</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="email" '.($result['primary_email'] != '' ? 'value="'.$result['primary_email'].'"' : 'placeholder="xyz@vopium.com"').' name="email1">
                              </div>
                        </div>
					      				
						<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Secondary Email Address</label>
                              <div class="col-sm-10">
                                  <input class="form-control" type="email" '.($result['secondary_email'] != '' ? 'value="'.$result['secondary_email'].'"' : 'placeholder="xyz@gmail.com"').' name="email2">
                              </div>
                        </div>
					
	    				<div class="form-group">
                              <div class="col-sm-12">
	    						<button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Save Profile</button>
                              </div>
                        </div>

                      </form>
                  </div>		';
					}
			}
			else {
					$this->config->f3->reroute('@login');
		}

	}

}