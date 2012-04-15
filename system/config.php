<?php

/*
	Author: JB Braendel (Jbudone)
	Date: February 13, 2011
	File: config.php
	Desc: The configuration/environment for the market.android spider
	

*/



	///////////
	///
	///
	///   ENVIRONMENT
	///
	/// Settings for the Spider
	/////////////////////////////////
	/////////////////////////////////
	
	
	
	/// 
	///
	///  ENVIRONMENTS
	////////////////////
	
	$localEnv = array('LOCAL' => array('www.market.android.local', 'market.android.local'));
	$onlineEnv = array('ONLINE' => array('www.tuttoandroid.net','tuttoandroid.net'));
	$environments = array($localEnv, $onlineEnv);
	$defaultEnvironment = 'ONLINE';
	$environment = array();
	
	



	
	///
	///
	///  TESTING SETTINGS
	////////////////////
	
	$environment['BOTH']['TEST_CAT_ONEPAGE']=FALSE; // Set to TRUE to only scrape the first page of any given category
	$environment['BOTH']['TEST_CAT_SINGLE']=TRUE; // Set to TRUE to only scrape through a single category
	$environment['BOTH']['TEST_APP_LIMIT']=0; // Set to a number ABOVE 0 to limit the number of apps that are scraped (0 to turn off)
	
	



	
	///
	///
	///  GENERAL SETTINGS
	////////////////////
	
	
	$environment['BOTH']['DB_CREATE_ON_NOT_FOUND']=TRUE; //  If the database is NOT found, run db_create.sql to create it
	$environment['BOTH']['DB_CLEAR_ON_START']=FALSE; // Clear the database (reset) before beginning the spider
	
	$environment['BOTH']['EXIT_ON_ERROR']=FALSE; // If an error occurs, EXIT the script and warn the user
	
	$environment['BOTH']['ENCODING_SET']='UTF-8';
	
	
	
	
	
	
	///
	///
	///  DIRECTORY STRUCTURE
	////////////////////
	
	$environment['LOCAL']['DIRECTORY_ROOT']='J:\\JStuff\\Work\\Clients\\Mikhael\\TuttoAndroid\\';
	$environment['ONLINE']['DIRECTORY_ROOT']='http://www.tuttoandroid.net/';
	$environment['LOCAL']['DIRECTORY_SYSTEM']=$environment['LOCAL']['DIRECTORY_ROOT'].'system';
	$environment['ONLINE']['DIRECTORY_SYSTEM']=$environment['ONLINE']['DIRECTORY_ROOT'].'system';
	$environment['LOCAL']['SITE_URL']='www.market.android.local';
	$environment['ONLINE']['SITE_URL']='http://www.tuttoandroid.net';
	
	
	
	
	
	
	
	
	///
	///
	///  DATABASE
	////////////////////
	
	$environment['LOCAL']['DB_HOST']='localhost';
	$environment['LOCAL']['DB_USER']='root';
	$environment['LOCAL']['DB_PASS']='';
	$environment['LOCAL']['DB_DB']='marketandroid';
	
	$environment['ONLINE']['DB_HOST']='';
	$environment['ONLINE']['DB_USER']='';
	$environment['ONLINE']['DB_PASS']='';
	$environment['ONLINE']['DB_DB']='';
	
	


	
	///
	///
	///  SETUP ENVIRONMENT
	////////////////////
	
	$serverType=$defaultEnvironment;
	if ($_SERVER)
	{
		foreach($environments as $environment_names) {
			
			foreach ($environment_names as $environment_type => $server_name) {
				if (in_array($_SERVER['SERVER_NAME'], $server_name)) {
					$serverType=$environment_type;
					break;
				}
			}
		}
	}
	
	if ($environment['BOTH'])
		$environment = array_merge($environment[$serverType], $environment['BOTH']);
	else
		$environment=$environment[$serverType];
		
		
	foreach($environment as $key=>$val) {
		define($key,$val);
	}
	
	








/* End Of File:  config.php   */