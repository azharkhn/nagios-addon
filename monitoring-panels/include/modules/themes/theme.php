<?php


class theme {
	
	public $site_url;
	
	public function set_siteURL($url) {
		$this->site_url = $url;
	}

//+====================================================================================================================+
	private function get_header () {
		
?>		
		<!DOCTYPE html>
			<html lang="en">
			  <head>
			    <meta charset="utf-8">
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <meta name="description" content="">
			    <meta name="author" content="Dashboard">
			    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
			
			    <title>VOOPs Monitor</title>
			
			    <link href="<?php echo $this->site_url;?>include/assets/css/bootstrap.css" rel="stylesheet">
			    <link href="<?php echo $this->site_url;?>include/assets/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" />
			    <link href="<?php echo $this->site_url;?>include/assets/css/style.css" rel="stylesheet">
			    <link href="<?php echo $this->site_url;?>include/assets/css/style-responsive.css" rel="stylesheet">
			    <link href="<?php echo $this->site_url;?>include/assets/css/table-responsive.css"" rel="stylesheet">
			    
			    <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
			    <script type="text/javascript" src="<?php echo $this->site_url;?>include/assets/angularjs/angular.min.js"></script>
			    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			    <script type="text/javascript" >
								$(document).ready(function() {

									$('#anchor tr').click(function() {
										var href = $(this).find("a").attr("href");
										if(href) {
											window.location = href;
										}
									});

								});
				</script>
			    
			    	
			  </head>
			
			  <body>

<?php 
	}	

//+====================================================================================================================+
	function get_LoginScreen() {

		$this->get_header();
?>
	 <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->

	  <div id="login-page">
	  	<div class="container">
	  	
		      <form class="form-login" action="" method="post">
		        <h2 class="form-login-heading"><i class="fa fa-bar-chart"></i><b> VoIP & SMS Monitor</b></h2>
		        <div class="login-wrap">
		            <input type="text" class="form-control" placeholder="username" name="username" autofocus>
		            <br>
		            <input type="password" class="form-control" placeholder="password" name="password">
		            <label class="checkbox">
		                <span class="pull-right">
		                    <a data-toggle="modal" href="login#myModal"> Forgot Password?</a>
		
		                </span>
		            </label>
		            <button class="btn btn-theme btn-block" type="submit" name="login"><i class="fa fa-sign-in"></i> SIGN IN</button>
		           
		            
		    
		
		        </div>
		
		          <!-- Modal -->
		          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
		              <div class="modal-dialog">
		                  <div class="modal-content">
		                      <div class="modal-header">
		                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                          <h4 class="modal-title">Forgot Password ?</h4>
		                      </div>
		                      <div class="modal-body">
		                          <p>Enter your e-mail address below to reset your password.</p>
		                          <input type="text" name="email" placeholder="Email" name="email"  autocomplete="off" class="form-control placeholder-no-fix">
		
		                      </div>
		                      <div class="modal-footer">
		                          <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
		                          <button class="btn btn-theme" type="submit" name="send">Submit</button>
		                      </div>
		                  </div>
		              </div>
		          </div>
		          <!-- modal -->
		
		      </form>	  	
		      <!--  
		      				<br>
							<div class="alert alert-danger alert-dismissable">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  <b>Error!</b> You have entered a wrong username/password.
							</div>
							
-->
	  	</div>
	  </div>
	 
<?php 
		
		
		$this->get_footer();
		
	}
	
	function set_PageTemplate($heading, $contents) {

		$this->get_header();
		?>
		<section id="container" >
      <!-- **********************************************************************************************************************************************************
      TOP BAR CONTENT & NOTIFICATIONS
      *********************************************************************************************************************************************************** -->
      <!--header start-->
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->
            <a href="<?php echo $this->site_url.'home/login-details'; ?>" class="logo"><i class="fa fa-bar-chart"></i><b> VoIP & SMS Monitor</b></a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
            
                <!--  notification end -->
            </div>
            <div class="top-menu">
            	<ul class="nav pull-right top-menu">
                    <li><a class="logout" href="<?php echo $this->site_url.'logout'; ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
            	</ul>
            </div>
        </header>
      <!--header end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              
              	 <p class="centered"><a href="<?php echo $this->site_url.'home/profile'; ?>"><i class="fa fa-user fa-5x"></i> </a></p>
              	 <h5 class="centered">USER</h5>

            	  <li class="sub-menu">
                      <a href="#" >
                          <i class="fa fa-cog"></i>
                          <span>Home</span>
                          <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="sub">
                          <li><a  href="<?php echo $this->site_url.'home/edit/profile'; ?>"><i class="fa fa-pencil-square-o"></i> Update Profile</a></li>
                          <li><a  href="<?php echo $this->site_url.'home/edit/password'; ?>"><i class="fa fa-pencil-square-o"></i> Change Password</a></li>
                      </ul>
                  </li>
                 <?php if($_SESSION['user_type'] == 'ADMIN') {?>
                  <li class="sub-menu">
                      <a href="#" >
                          <i class="fa fa-cog"></i>
                          <span>Admin Settings</span>
                          <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="sub">
                          <li><a  href="<?php echo $this->site_url.'admin-settings/users'; ?>"><i class="fa fa-pencil-square-o"></i> User Management</a></li>
                          <li><a  href="<?php echo $this->site_url.'admin-settings/edrs'; ?>"><i class="fa fa-pencil-square-o"></i> EDRS</a></li>
                      </ul>
                  </li>
       			  <?php } ?>
       			 
       			  <li class="sub-menu">
                      <a href="#" >
                          <i class="fa fa-cog"></i>
                          <span>Nagios Monitoring</span>
                          <i class="fa fa-caret-down"></i>
                      </a>
                      <ul class="sub">
                          <li><a  href="<?php echo $this->site_url.'nagios-monitoring/services'; ?>"><i class="fa fa-pencil-square-o"></i> Services</a></li>
                          <li><a  href="<?php echo $this->site_url.'nagios-monitoring/databases'; ?>"><i class="fa fa-pencil-square-o"></i> Databases</a></li>
                      </ul>
                  </li>
       			 
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper site-min-height">
          	<h3><i class="fa fa-angle-right"></i> <?php echo $heading; ?> </h3>
          	<div class="row mt">
          		<div class="col-lg-12">
          		<?php echo $contents; ?>
          		</div>
          	</div>
			
		</section> <!--/wrapper -->
      </section><!-- /MAIN CONTENT -->

      <!--main content end-->
      <!--footer start-->
      <footer class="site-footer">
          <div class="text-center">
              2016 - <a href="http://www.onesconsultants.com">ONES Consultants A/S</a>
              <a href="#" class="go-top">
                  <i class="fa fa-angle-up"></i>
              </a>
          </div>
      </footer>
      <!--footer end-->
  </section>
		<?php
		
		$this->get_footer();
	}
	
	private function get_footer () {
?>		
		<!-- js placed at the end of the document so the pages load faster -->
		<script src="<?php echo $this->site_url;?>include/assets/js/jquery.js"></script>
		<script src="<?php echo $this->site_url;?>include/assets/js/bootstrap.min.js"></script>
		<script src="<?php echo $this->site_url;?>include/assets/js/jquery-ui-1.9.2.custom.min.js"></script>
				<script src="<?php echo $this->site_url;?>include/assets/js/jquery.ui.touch-punch.min.js"></script>
						<script class="include" type="text/javascript" src="<?php echo $this->site_url;?>include/assets/js/jquery.dcjqaccordion.2.7.js"></script>
								<script src="<?php echo $this->site_url;?>include/assets/js/jquery.scrollTo.min.js"></script>
										<script src="<?php echo $this->site_url;?>include/assets/js/jquery.nicescroll.js" type="text/javascript"></script>
		
		
										<!--common script for all pages-->
										<script src="<?php echo $this->site_url;?>include/assets/js/common-scripts.js"></script>
		
										<!--script for this page-->
		
										<script>
										//custom select box
		
		$(function(){
		$('select.styled').customSelect();
		});
		
		</script>
		</div>
		</body>
		</html>

<?php 
	}
	
	public function get_alert( $return_code = 0, $comment = '' ) {
	
		switch ($return_code) {
			case 200:
				return '
							<div class="alert alert-success alert-dismissable">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  <b>Success!</b> Data has been successfully updated. '.$comment.'
							</div>'; 	
				break;
				
			case 404:
				return '
							<div class="alert alert-danger alert-dismissable">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  <b>Error!</b> Data has encountered an Error. '.$comment.'
							</div>';
			
				break;
				
			case 603:
				return '
							<div class="alert alert-warning alert-dismissable">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  <b>Warning!</b> Data has an duplicate Entry. '.$comment.'
							</div>';
						
				break;
				
			default:
				return '
							<div class="alert alert-info alert-dismissable">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  <b>Information!</b> Please verify your entered Record. '.$comment.'
							</div>';
		}
	}
	
	
}
		
?>	
	
