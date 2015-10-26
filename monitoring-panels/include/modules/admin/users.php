<?php

namespace admin;

require_once 'include/modules/config.php';

Class Users {
	
	private $config;
	private $page = "Admin Settings / Users";
	
	function __construct() {
		$this->config = new \Config();
	}
	
	public function ADD_USERS() {
		session_start();
		if(isset($_SESSION['username']) && $_SESSION['user_type'] == 'ADMIN') {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_adding_users() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function SHOW_USERS() {
		session_start();
		if(isset($_SESSION['username']) && $_SESSION['user_type'] == 'ADMIN') {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_showing_users() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function EDIT_USERS() {
		session_start();
		if(isset($_SESSION['username'])  && isset($_GET['id']) && $_SESSION['user_type'] == 'ADMIN') {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_editing_users($_GET['id']) );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	private function set_account($username, $password, $type) {

		if(!$this->is_account_exists($username)) {
	
			$encryptedpassword = md5($password);
	
			$query = $this->config->db->exec(" INSERT INTO `monitoring_users` (`username`, `password`,`user_type`, `active`, `created_datetime`, `created_by`)
										VALUES ( '$username', '$encryptedpassword', '$type', 'Y' ,NOW(), '".$_SESSION['username']."@".$this->config->request['ip-address']."' );");
	
			if($this->config->db->count($query) > 0) {
				$this->config->db->exec(" INSERT INTO `monitoring_user_profile` (`user_id`, `created_datetime`, `created_by`) VALUES ( (SELECT `id` FROM `monitoring_users` WHERE `username` = '$username'), NOW(), '".$_SESSION['username']."@".$this->config->request['ip-address']."' );");
				return 200;
			}
			else {
				return 404;
			}
	
		}
		else {
			return 603;
		}
	
	}
	
	private function is_account_exists($name) {
			$query = $this->config->db->exec("SELECT `id` FROM `monitoring_users` WHERE `username` = '$name';");
			if($this->config->db->count($query) > 0) {
				return True;
			}
			else {
				return False;
			}

	}
	
	private function get_users() {
		$list = '';
		$count = 1;
		$query = $this->config->db->exec("SELECT 
												`username`, 
												`user_type`, 
												`active`,
												`id`
											FROM `monitoring_users`
											ORDER by 2,4;");
	
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
				$list .= '	<tr>
								<td data-title="Sr.#">'.$count++.'</td>
								<td data-title="User">'.$result['username'].'<a href="'.$this->config->site.'admin-settings/edit/users?id='.base64_encode($result['id']).'"></a></td>
								<td data-title="Type">'.$result['user_type'].'</td>
								<td data-title="Active">'.($result['active'] == 'Y' ? '<span class="fa fa-check fa-lg"></span>': '<span class="fa fa-times fa-lg"></span>').'</td>
							</tr>';
			}
			return $list;
		}
		else {
			return False;
		}
	
	}

	private function update_user($password, $active, $type, $id ) {
	
		$query = $this->config->db->exec(" UPDATE `monitoring_users`
												SET ".($password != '' ? "`password` = '".md5($password)."'," : "")."
												`user_type` = '$type',
												`active` = '$active',
												`updated_datetime` = NOW(),
												`updated_by` = '".$_SESSION['username']."@".$this->config->request['ip-address']."'
												WHERE `id` = ".base64_decode($id).";");
	
				if($this->config->db->count($query) > 0) {
					return 200;
				}
				else {
					return 404;
				}
	
	}
	
	private function get_template_for_adding_users() {
		
	    return '
                      <!-- BASIC FORM ELELEMNTS -->
	    		<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li  class="active"><a href="'.$this->config->site.'admin-settings/add/users"><b>Add User</b></a></li>
					      <li><a href="'.$this->config->site.'admin-settings/show/users">Show Users</a></li>
	    				</ul>
						<br/>
	    		'.(
	    				isset($_POST['submit'])  
	    				&& isset($_POST['username']) && $_POST['username'] != '' 
	    				&& isset($_POST['password']) && $_POST['password'] != '' 
	    				
	    				? $this->config->theme->get_alert($this->set_account($_POST['username'], $_POST['password'], $_POST['type']))
	    			
	    				: ( isset($_POST['submit'])  
	    					|| isset($_POST['username']) && $_POST['username'] == '' 
	    					|| isset($_POST['password']) && $_POST['password'] == ''
	    						
	    					? $this->config->theme->get_alert(404)
							: ''
	    					)
				).'
	    			<form class="form-horizontal style-form" action="" method="post">
                          
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Username</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" placeholder="Username" name="username">
                              </div>
                        </div>
                          
	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="password" placeholder="Password" name="password">
                              </div>
                        </div>
	    		
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Select User Type</label>
                              <div class="col-sm-10">
				    				<select class="form-control" id="focusedInput" name="type">
									  <option value="ADMIN">Administrator</option>
									  <option value="USER">User</option>
									</select>
								</div>
                        </div>
	    		
	    					  		
	    				<div class="form-group">
                              <div class="col-sm-12">
                                   <button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Add User</button>
                              </div>
                        </div>
	    		
                      </form>
                  </div>		';
		
	}
	
	private function get_template_for_editing_users($id) {
		
		$query = $this->config->db->exec("SELECT
												`username`,
												`user_type`,
												`active`
											FROM `monitoring_users`
											WHERE `id` = ".base64_decode($id).";");
		
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
					return '
                      <!-- BASIC FORM ELELEMNTS -->
	    			<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'admin-settings/add/users">Add User</a></li>
					      <li><a href="'.$this->config->site.'admin-settings/show/users">Show Users</a></li>
					      <li  class="active"><a href="'.$this->config->site.'admin-settings/edit/users"><b>Edit User</b></a></li>
	    				</ul>
						<br/>
	    		'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->update_user($_POST['password'], $_POST['active'], $_POST['type'], $id)): '').'
	    	
	    
                      	<form class="form-horizontal style-form" action="" method="post">
	
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Username</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="disabledInput" type="text" placeholder="'.$result['username'].'" disabled>
                              </div>
                        </div>
	
	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control"  type="password" placeholder="Password" name="password">
                              </div>
                        </div>
	   					
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Select User Type</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="type">
									  '.($result['user_type'] == 'ADMIN' ? '<option value="ADMIN" selected>Administrator</option><option value="USER">User</option>': '<option value="ADMIN">Administrator</option><option value="USER" selected>User</option>').'
									</select>
								</div>
                        </div>
	  
						<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Active</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="active">
									  	'.($result['active'] == 'Y' ? '<option value="Y" selected>Activate</option><option value="N">Deactivate</option>': '<option value="Y">Activate</option> <option value="N" selected>Deactivate</option>').'
									</select>
								</div>
                        </div>
							
	    				<div class="form-group">
                              <div class="col-sm-12">
                                   <button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Edit User</button>
                              </div>
                        </div>
	   
                      </form>
                  </div>		';
					}
			}
			else {
					$this->config->f3->reroute('@admin_user_show');
		}
	
	}
	
	private function get_template_for_showing_users() {
		return '
                      <!-- BASIC FORM ELELEMNTS -->
			
					<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'admin-settings/add/users">Add User</a></li>
					      <li class="active"><a href="'.$this->config->site.'admin-settings/show/users"><b>Show Users</b></a></li>
	    				</ul>
						<br/>
	                      <section id="no-more-tables">
                              <table id="anchor" class="table table-bordered table-hover table-striped table-responsive table-condensed cf">
                                  <thead class="cf">
                              <tr>
					      		  <th>Sr.#</th>
                                  <th>User</th>
					      		  <th>Type</th>
                                  <th>Active</th>
                              </tr>
                              </thead>
                              <tbody>
                              '.$this->get_users().'
                              </tbody>
                          </table>
                          </section>
					</div>
					';
	}
	
}