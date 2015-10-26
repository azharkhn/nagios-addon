<?php
	
    $f3 = require('include/fatfree/base.php');
    $f3->set('AUTOLOAD','include/modules/');
    $f3->config('routes.cfg');
	$f3->run();	
  
?>

  
 
