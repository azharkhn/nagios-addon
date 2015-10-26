<?php

namespace monitor;

require_once 'include/modules/config.php';

Class Databases {
	
	private $config;
	private $page = "Nagios Monitoring / Databases";
	
	function __construct() {
		$this->config = new \Config();
	}
	
	public function ADD_DATABASES() {
		session_start();
		if(isset($_SESSION['username'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_adding_databases() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function SHOW_DATABASES() {
		session_start();
		if(isset($_SESSION['username'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_showing_databases() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function EDIT_DATABASES() {
		session_start();
		if(isset($_SESSION['username'])  && isset($_GET['id'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_editing_databases($_GET['id']) );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	private function set_database($name, $host, $username, $password, $port, $database) {

		if(!$this->is_database_exists($name)) {
			if( $name != '' && $host != '' && $username != '' && $password != '' && $port != '' & $database != '') {
				$encryptedpassword = $password;
				
				$query = $this->config->db->exec(" INSERT INTO `monitoring_db` (`name`, `hostname`, `username`, `password`,`port`, `database`, `active`, `created_datetime`, `created_by`)
											VALUES ( '$name', '$host', '$username', '$encryptedpassword', '$port', '$database', 'Y' , NOW(), '".$_SESSION['username']."@".$this->config->request['ip-address']."' );");
		
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
		else {
			return 603;
		}
	
	}
	
	private function is_database_exists($name) {
			$query = $this->config->db->exec("SELECT `id` FROM `monitoring_db` WHERE `name` = '$name';");
			if($this->config->db->count($query) > 0) {
				return True;
			}
			else {
				return False;
			}

	}
	
	private function get_databases() {
		$list = '';
		$count = 1;
		$query = $this->config->db->exec("SELECT * FROM `monitoring_db` ORDER by 1 DESC;");
	
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
				$list .= '	<tr>
								<td data-title="Sr.#">'.$count++.'</td>
								<td data-title="Name">'.$result['name'].'<a href="'.$this->config->site.'nagios-monitoring/edit/databases?id='.base64_encode($result['id']).'"></a></td>
								<td data-title="Host Address">'.$result['hostname'].'</td>
								<td data-title="Username">'.$result['username'].'</td>
								<td data-title="Database">'.$result['database'].'</td>
								<td data-title="Port">'.$result['port'].'</td>
								<td data-title="Active">'.($result['active'] == 'Y' ? '<span class="fa fa-check fa-lg"></span>': '<span class="fa fa-times fa-lg"></span>').'</td>
							</tr>';
			}
			return $list;
		}
		else {
			return False;
		}
	
	}

	private function update_database($host, $username, $password, $port, $database, $active, $id ) {
	
			if ( $host != '' && $username != '' && $password != '' && $port != '' && $database != '') {	
				$query = $this->config->db->exec(" UPDATE `monitoring_db`
												SET
												`hostname` = '$host',
												`username` = '$username',
												`password` = '".base64_encode($password)."',
												`port` = '$port',
												`database` = '$database',
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
			else {
				return 405;
			}
	
	}
	
	private function get_template_for_adding_databases() {
		
	    return '
                      <!-- BASIC FORM ELELEMNTS -->
	    		<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li  class="active"><a href="'.$this->config->site.'nagios-monitoring/add/databases"><b>Add Database</b></a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/databases">Show Databases</a></li>
	    				</ul>
						<br/>
	    		'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->set_database($_POST['name'], $_POST['host'], $_POST['username'], $_POST['password'], $_POST['port'], $_POST['database'])) :  '').'
	    			<form class="form-horizontal style-form" action="" method="post">
                          
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Name</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" maxlength="50" placeholder="Unique Name of Database" name="name">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Host Address</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" maxlength="50" placeholder="Host Address i.e. localhost etc." name="host">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Username</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" maxlength="50" placeholder="Username" name="username">
                              </div>
                        </div>
                          
	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="password" maxlength="100" placeholder="Password" name="password">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Database</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" maxlength="50" placeholder="Database Name" name="database">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Port</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="number" min="1" max="65535" value="3306" name="port">
                              </div>
                        </div>
	    		
	    				<div class="form-group">
                              <div class="col-sm-12">
                                   <button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Add Database</button>
                              </div>
                        </div>
	    		
                      </form>
                  </div>		';
		
	}
	
private function get_template_for_editing_databases($id) {
		
		$query = $this->config->db->exec("SELECT * FROM `monitoring_db` WHERE `id` = ".base64_decode($id).";");
		
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
					return '
                      <!-- BASIC FORM ELELEMNTS -->
	    			<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'nagios-monitoring/add/databases">Add Database</a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/databases">Show Databases</a></li>
					      <li  class="active"><a href="'.$this->config->site.'nagios-monitoring/edit/databases"><b>Edit Database</b></a></li>
	    				</ul>
						<br/>
	    		'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->update_database($_POST['host'], $_POST['username'], $_POST['password'], $_POST['port'], $_POST['database'], $_POST['active'], $id)): '').'
	    	
	    
                      	<form class="form-horizontal style-form" action="" method="post">
	
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Name</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="disabledInput" type="text" maxlength="50" placeholder="'.$result['name'].'" disabled>
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Host Address</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" maxlength="50" value="'.$result['hostname'].'" name="host">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Username</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text"  maxlength="50" value="'.$result['username'].'" name="username">
                              </div>
                        </div>
                          
	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Password</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="password" maxlength="100" value="'.$result['password'].'" name="password">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Database</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="text" maxlength="50" value="'.$result['database'].'" name="database">
                              </div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Port</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="number" min="1" max="65535" value="'.$result['port'].'" name="port">
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
                                   <button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Edit Database</button>
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
	
	private function get_template_for_showing_databases() {
		return '
                      <!-- BASIC FORM ELELEMNTS -->
			
					<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'nagios-monitoring/add/databases">Add Database</a></li>
					      <li class="active"><a href="'.$this->config->site.'nagios-monitoring/show/databases"><b>Show Database</b></a></li>
	    				</ul>
						<br/>
	                      <section id="no-more-tables">
                              <table id="anchor" class="table table-bordered table-hover table-striped table-responsive table-condensed cf">
                                  <thead class="cf">
                              <tr>
					      		  <th>Sr.#</th>
                                  <th>Name</th>
					      		  <th>Host Address</th>
                                  <th>Username</th>
					      		  <th>Port</th>
					      		  <th>Database</th>
					      		  <th>Active</th>
                              </tr>
                              </thead>
                              <tbody>
                              '.$this->get_databases().'
                              </tbody>
                          </table>
                          </section>
					</div>
					';
	}
	
}