<?php

namespace admin;

require_once 'include/modules/config.php';

Class EDRS {
	
	private $config;
	private $page = "Admin Settings / EDRS";
	
	function __construct() {
		$this->config = new \Config();
	}
	
	public function SHOW_EDRS() {
		session_start();
		if(isset($_SESSION['username']) && $_SESSION['user_type'] == 'ADMIN') {
			$this->config->theme->set_PageTemplate($this->page, $this->get_template_for_showing_users() );
		}
		else {
			$this->config->f3->reroute('@login');
		}
	}
	
		
	private function get_edrs( $limit = "") {
		$list = '';
		$query = $this->config->db->exec("SELECT * FROM `monitoring_login_edrs`
											ORDER BY `id` DESC LIMIT 100 ;");
	
		if($this->config->db->count($query) > 0) {
			foreach($query as $result) {
				$list .= '	<tr>
								<td data-title="DateTime">'.$result['created_datetime'].'</td>
								<td data-title="Session-ID">'.$result['session-id'].'</td>
								<td data-title="Username">'.$result['username'].'</td>
								<td data-title="IP Address">'.$result['ip_address'].'</td>
								<td data-title="Country">'.$result['country'].'</td>
								<td data-title="ISO Code">'.$result['country_code'].'</td>
								<td data-title="User Agent">'.$result['user_agent'].'</td>
								<td data-title="Host">'.$result['host'].'</td>
								<td data-title="Method">'.$result['method'].'</td>
								<td data-title="Status">'.$result['status'].'</td>
							</tr>';
			}
			return $list;
		}
		else {
			return False;
		}
	
	}
	
	private function get_template_for_showing_users() {
		
		return '
                      <!-- BASIC FORM ELELEMNTS -->
						
					      <section id="no-more-tables">
                              <table id="anchor" class="table table-bordered table-hover table-striped table-responsive table-condensed cf">
                                  <thead class="cf">
                              <tr>
								  <th>DateTime</th>
                                  <th>Session-ID</th>
					      		  <th>Username</th>
                                  <th>IP Address</th>
								  <th>Country</th>
								  <th>ISO Code</th>
								  <th>User Agent</th>
								  <th>Host</th>
								  <th>Method</th>
								  <th>Status</th>
                              </tr>
                              </thead>
                              <tbody>
                              '.$this->get_edrs("").'
                              </tbody>
                          </table>
                          </section>
					</div>
					';
	}
	
}