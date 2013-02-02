<?php

/*
	Author: JB Braendel (Jbudone)
	Date: February 7th, 2011
	File: index.php
	Desc: Spider Crawler for the market.android site
	

*/

	// Requirements
	require_once('system/controller.php');
	header('Content-Type: text/html, charset='.constant('ENCODING_SET'));



	///////////
	///
	///
	///   INDEX
	///
	/// Index for the Market.Android spider crawler
	/////////////////////////////////
	/////////////////////////////////
	
	
	
	?>
    <html>
    <head>
    <meta http-equiv="Content-Type"  
     content="text/html; charset=<?php echo constant('ENCODING_SET'); ?>">
     <style type="text/css">
	 	body {
			white-space:pre;	
		}
     </style>
    </head>
    <body>
    <?php
	$controller=new Controller();
	$controller->start();
	?>
    </body>
    <?php




/* End of File -- index */