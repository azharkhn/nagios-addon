<?php

namespace monitor;

require_once 'include/modules/config.php';

Class Services {
	
	private $config;
	private $service = "http://192.168.100.76:8888/monitor?query=";
	private $page = "Nagios Monitoring / Services";
	
	function __construct() {
		$this->config = new \Config();
	}
	
	public function ADD_SERVICES() {
		session_start();
		if(isset($_SESSION['username'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_adding_services() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function SHOW_SERVICES() {
		session_start();
		if(isset($_SESSION['username'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_showing_services() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function EDIT_SERVICES() {
		session_start();
		if(isset($_SESSION['username'])  && isset($_GET['id'])) {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_editing_services($_GET['id']) );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	public function SHOW_SERVICES_EDRS() {
		session_start();
		if(isset($_SESSION['username']) && $_SESSION['user_type'] == 'ADMIN') {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_showing_services_edrs() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
	private function email($title) {
		$_title = str_replace("-","_",$title);
		
		$message = "";
		
		$this->config->smtp->set('To', '"Vopium NOC" <noc@vopium.com>');
		$this->config->smtp->set('Subject', 'New Monitoring Service for Nagios');
		
		if($this->config->smtp->send($message)) {
			return 'Y';
		}
		else { 
			return 'N';
		}
	}
	
	private function set_service($db, $partner, $title, $sqlquery, $unit, $alert, $warning, $critical, $details) {

		if(!$this->is_service_exists($title)) {
			
			if( ($title != '' && $sqlquery != '' && $alert != '' && $warning != '' && $critical != '') && ($alert > $warning && $warning > $critical) || ($alert < $warning && $warning < $critical) ) {
				
				$title = strtolower(str_replace(" ","-",$title));
				
				$query = $this->config->db->exec(" INSERT INTO `monitoring_thresholds` (`db_id`, `vendor`, `title`, `query`, `unit`, `alert`, `warning`, `critical`, `description`, `active`, `notification`, `created_datetime`, `created_by`)
											VALUES ( '$db', '$partner', ".$this->config->db->quote($title).", ".$this->config->db->quote($sqlquery).", '$unit', '$alert', '$warning', '$critical', ".$this->config->db->quote($details)." , 'Y', '".$this->email($title)."' ,NOW(), '".$_SESSION['username']."@".$this->config->request['ip-address']."' );");
		
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
	
	private function is_service_exists($title) {
			$query = $this->config->db->exec("SELECT `id` FROM `monitoring_thresholds` WHERE `title` = '$title';");
			if($this->config->db->count($query) > 0) {
				return True;
			}
			else {
				return False;
			}

	}
	
	private function httpGet($param) {
			$url = $this->service.$param;
		    $ch = curl_init();  
		 
		    curl_setopt($ch,CURLOPT_URL,$url);
		    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		 
		    $output = curl_exec($ch);
		 
		    curl_close($ch);
		    return $output;
	}
	
	private function get_services() {
		$list = '';
		$count = 1;
		$query = $this->config->db->exec("SELECT `th`.`id`, `db`.`name`, `th`.`title` as title, `th`.`vendor`, `th`.`alert`, `th`.`warning`, `th`.`critical`, `th`.`unit`, `th`.`query`, `th`.`description`, `th`.`active`
											FROM `monitoring_thresholds` th, `monitoring_db` db 
											WHERE `db`.`id` = `th`.`db_id` ORDER BY 1 DESC;");
	
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
				$list .= '	<tr>
								<td data-title="Sr.#">'.$count++.'</td>
								<td data-title="Title">'.$result['title'].'<a href="'.$this->config->site.'nagios-monitoring/edit/services?id='.base64_encode($result['id']).'"</a></td>
								<td data-title="Database">'.$result['name'].'</td>
								<td data-title="Partner">'.$result['vendor'].'</td>
								<td data-title="Alert">'.$result['alert'].'</td>
								<td data-title="Warning">'.$result['warning'].'</td>
								<td data-title="Critical">'.$result['critical'].'</td>
								<td data-title="Unit">'.$result['unit'].'</td>
								<td data-title="Details">'.$result['description'].'</td>
								<td data-title="Active">'.($result['active'] == 'Y' ? '<span class="fa fa-check fa-lg"></span>': '<span class="fa fa-times fa-lg"></span>').'</td>
								<td data-title="Service Response">'.$this->httpGet($result['title']).'</td>
							</tr>';
			}
			return $list;
		}
		else {
			return False;
		}
	
	}
	
	private function get_services_edrs() {
		$list = '';
		$count = 1;
		$query = $this->config->db->exec("SELECT `title`, `vendor`, `notification`, `created_datetime`, `created_by`, `updated_datetime`, `updated_by`
											FROM `monitoring_thresholds`
											ORDER BY `id` DESC;");
	
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
				$list .= '	<tr>
								<td data-title="Sr.#">'.$count++.'</td>
								<td data-title="Title">'.$result['title'].'</td>
								<td data-title="Partner">'.$result['vendor'].'</td>
								<td data-title="Created DateTime">'.$result['created_datetime'].'</td>
								<td data-title="Created By">'.$result['created_by'].'</td>
								<td data-title="Updated Datetime">'.$result['updated_datetime'].'</td>
								<td data-title="Updated By">'.$result['updated_by'].'</td>
								<td data-title="Notified">'.($result['notification'] == 'Y' ? '<span class="fa fa-check fa-lg"></span>': '<span class="fa fa-times fa-lg"></span>').'</td>
							</tr>';
			}
			return $list;
		}
		else {
			return False;
		}
	
	}

	private function update_service($db, $partner, $sqlquery, $unit, $alert, $warning, $critical, $details, $active, $id ) {
	
		if($sqlquery != '' && $warning != '' && $alert != '' && $critical != '') {
			$query = $this->config->db->exec(" UPDATE `monitoring_thresholds`
												SET 
												`db_id` = '$db',
												`vendor` = ".$this->config->db->quote($partner).",
												`query` = ".$this->config->db->quote($sqlquery).",
												`alert` = '$alert',
												`warning` = '$warning',
												`critical` = '$critical',
												`unit` = '$unit',
												`description` = ".$this->config->db->quote($details).",
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
	
	private function get_databases( $id = '') {
		$query = $this->config->db->exec("SELECT * from `monitoring_db` WHERE `active` = 'Y';");
		$list = '';
		
		if($this->config->db->count($query) > 0) {
			
			foreach($query as $result) {
				if($id == $result['id']) {
					$list .= '<option value="'.$result['id'].'" selected>'.$result['name'].'</option>';
				}
				else {
					$list .= '<option value="'.$result['id'].'">'.$result['name'].'</option>';
				}
			}
			return $list;	
		}
		
	}
	
	private function get_template_for_adding_services() {
		
	    return '
                      <!-- BASIC FORM ELELEMNTS -->
	    		<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li  class="active"><a href="'.$this->config->site.'nagios-monitoring/add/services"><b>Add Services</b></a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/services">Show Services</a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/services/edrs">EDRs</a></li>
	    				</ul>
						<br/>
	    		'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->set_service($_POST['database'], $_POST['partner'], $_POST['title'], $_POST['query'], $_POST['unit'], $_POST['alert'], $_POST['warning'], $_POST['critical'], $_POST['details'] )): '' ).'
	    			<form class="form-horizontal style-form" action="" method="post">
						
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Select Database</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="database">
									'.$this->get_databases().'
	    							</select>
								</div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Title</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" maxlength="50" type="text" placeholder="Title" name="title">
                              </div>
                        </div>
	
	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Partner</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" maxlength="50" type="text" value="Vopium" name="partner">
                              </div>
                        </div>
                                  		
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">MySQL Query</label>
                              <div class="col-sm-10">
                                  <textarea class="form-control" id="focusedInput"  type="text" placeholder="SELECT query only." name="query"></textarea>
                              </div>
                        </div>
	   					
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Details</label>
                              <div class="col-sm-10">
                                  <textarea class="form-control"  type="text" placeholder="Description" name="details"></textarea>
                              </div>
                        </div>
                        
                        <div class="form-group">
                              <label class="col-sm-1 col-sm-1 control-label">Alert</label>
                              <div class="col-sm-2">
                                  <input class="form-control" id="focusedInput"  type="number" placeholder="Alert Value" name="alert">
                              </div>
								
							<label class="col-sm-1 col-sm-1 control-label">Warning</label>
                              <div class="col-sm-2">
                                  <input class="form-control" id="focusedInput"  type="number" placeholder="Warning Value" name="warning">
                              </div>
							
							<label class="col-sm-1 col-sm-1 control-label">Critical</label>
                              <div class="col-sm-2">
                                  <input class="form-control" id="focusedInput"  type="number" placeholder="Critical Value" name="critical">
                              </div>
                        </div>
					    
					    <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Select Unit</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="unit">
									  <option value="%" selected>Percentage</option>
									  <option value="hr">Hours</option>
                                  	  <option value="min">Minutes</option>
                                  	  <option value="sec">Seconds</option>
                                  	  <option value="EUR">Euro</option>
                                  	  <option value="">No-Unit</option>
									</select>
								</div>
                        </div>
                        	
	    				<div class="form-group">
                              <div class="col-sm-12">
                                   <button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Add Service</button>
                              </div>
                        </div>
	   
                      </form>
                  </div>			';
		
	}
	
	private function get_template_for_editing_services($id) {
		
		$query = $this->config->db->exec("SELECT `th`.`id`, `th`.`db_id`, `db`.`name`, `th`.`title` as title, `th`.`vendor`, `th`.`alert`, `th`.`warning`, `th`.`critical`, `th`.`unit`, `th`.`query`, `th`.`description`, `th`.`active`
											FROM `monitoring_thresholds` th, `monitoring_db` db 
											WHERE `db`.`id` = `th`.`db_id` AND `th`.`id` = ".base64_decode($id).";");
		
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
					return '
                      <!-- BASIC FORM ELELEMNTS -->
	    			<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'nagios-monitoring/add/services">Add Service</a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/services">Show Services</a></li>
					      <li  class="active"><a href="'.$this->config->site.'nagios-monitoring/edit/services"><b>Edit Services</b></a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/services/edrs">EDRs</a></li>
	    				</ul>
						<br/>
						
	    		'.(isset($_POST['submit']) ? $this->config->theme->get_alert($this->update_service($_POST['database'], $_POST['partner'], $_POST['query'], $_POST['unit'], $_POST['alert'], $_POST['warning'], $_POST['critical'], $_POST['details'], $_POST['active'], $id)): '').'
	    	
	    
                      	<form class="form-horizontal style-form" action="" method="post">
						
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Select Database</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="database">
									'.$this->get_databases($result['db_id']).'
	    							</select>
								</div>
                        </div>
	    				
	    				<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Title</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="disabledInput" maxlength="50" type="text" placeholder="'.$result['title'].'" disabled>
                              </div>
                        </div>
	
	    		 		<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Partner</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" maxlength="50" type="text" value="'.$result['vendor'].'" name="partner">
                              </div>
                        </div>
                                  		
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">MySQL Query</label>
                              <div class="col-sm-10">
                                  <textarea class="form-control" id="focusedInput" type="text" name="query">'.$result['query'].'</textarea>
                              </div>
                        </div>
	   					
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Details</label>
                              <div class="col-sm-10">
                                  <textarea class="form-control"  type="text" placeholder="Description" name="details">'.$result['description'].'</textarea>
                              </div>
                        </div>
                        
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Alert</label>
                              <div class="col-sm-10">
                                  <input class="form-control"  id="focusedInput" type="number" value="'.$result['alert'].'" name="alert">
                              </div>
                        </div>
                        
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Warning</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput"  type="number" value="'.$result['warning'].'" name="warning">
                              </div>
                        </div>
                        
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Critical</label>
                              <div class="col-sm-10">
                                  <input class="form-control" id="focusedInput" type="number" value="'.$result['critical'].'" name="critical">
                              </div>
                        </div>
                        
                        <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Select Unit</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="unit">
									  <option value="%" '.($result['unit'] == "%" ? 'selected' : '').'>Percentage</option>
									  <option value="hr" '.($result['unit'] == "hr" ? 'selected' : '').'>Hours</option>
                                  	  <option value="min" '.($result['unit'] == "min" ? 'selected' : '').'>Minutes</option>
                                  	  <option value="sec" '.($result['unit'] == "sec" ? 'selected' : '').'>Seconds</option>
                                  	  <option value="EUR" '.($result['unit'] == "EUR" ? 'selected' : '').'>Euro</option>
                                  	  <option value="" '.($result['unit'] = "" ? 'selected' : '').'>Select Unit</option>
									</select>
								</div>
                        </div>
                    
						<div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label">Active</label>
                              <div class="col-sm-10">
				    				<select class="form-control" name="active">
									  	<option value="Y" '.($result['active'] == "Y" ? 'selected' : '').'>Activate</option>
                                  	  	<option value="N" '.($result['active'] == "N" ? 'selected' : '').'>Deactivate</option>
									</select>
								</div>
                        </div>
							
	    				<div class="form-group">
                              <div class="col-sm-12">
                                   <button type="submit" class="btn btn-lg btn-theme btn-block" name="submit">Edit Service</button>
                              </div>
                        </div>
	   
                      </form>
                  </div>		';
					}
			}
			else {
					$this->config->f3->reroute('@monitor_services_show');
		}
	
	}
	
	private function get_template_for_showing_services() {
		return '
                      <!-- BASIC FORM ELELEMNTS -->
			
					<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'nagios-monitoring/add/services">Add Service</a></li>
					      <li class="active"><a href="'.$this->config->site.'nagios-monitoring/show/services"><b>Show Services</b></a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/services/edrs">EDRs</a></li>
	    				</ul>
						<br/>
	                      <section id="no-more-tables">
                              <table id="anchor" class="table table-bordered table-hover table-striped table-responsive table-condensed cf">
                                  <thead class="cf">
                              <tr>
					      		  <th>Sr.#</th>
					      		  <th>Title</th>
                                  <th>Database</th>
					      		  <th>Partner</th>
					      		  <th>Alert</th>
					      		  <th>Warning</th>
					              <th>Critical</th>
					      	      <th>Unit</th>
					              <th>Details</th>
					      		  <th>Active</th>
					      		  <th>Service Response</th>
					      		
                              </tr>
                              </thead>
                              <tbody>
                              '.$this->get_services().'
                              </tbody>
                          </table>
                          </section>
					</div>
					';
	}
	
	private function get_template_for_showing_services_edrs() {
		
		return '
                      <!-- BASIC FORM ELELEMNTS -->
		
					<div class="form-panel">
	    	 		   	<ul class="nav nav-tabs">
	      				  <li><a href="'.$this->config->site.'nagios-monitoring/add/services">Add Service</a></li>
					      <li><a href="'.$this->config->site.'nagios-monitoring/show/services">Show Services</a></li>
					      <li class="active"><a href="'.$this->config->site.'nagios-monitoring/show/services/edrs"><b>EDRs</b></a></li>
	    				</ul>
						<br/>
	                      <section id="no-more-tables">
                              <table id="anchor" class="table table-bordered table-hover table-striped table-responsive table-condensed cf">
                                  <thead class="cf">
                              <tr>
					      		  <th>Sr.#</th>
					      		  <th>Title</th>
                                  <th>Partner</th>
					      		  <th>Created DateTime</th>
					      		  <th>Created By</th>
					              <th>Updated Datetime</th>
					      	      <th>Updated By</th>
					              <th>Notified</th>
					          </tr>
                              </thead>
                              <tbody>
                              '.$this->get_services_edrs().'
                              </tbody>
                          </table>
                          </section>
					</div>
					';
	}
	
}
