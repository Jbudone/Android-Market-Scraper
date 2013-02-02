<?php

/*
	Author: JB Braendel (Jbudone)
	Date: February 13, 2011
	File: database.php
	Desc: The database section of market.android's crawler
	

*/


	// Requirements
	require_once('config.php');


	///////////
	///
	///
	///   DATABASE
	///
	/// All working with the database done here
	/////////////////////////////////
	/////////////////////////////////

	
	
	// Check that we have valid credentials required for running the crawler
	if (!defined('DB_HOST') or
		!defined('DB_USER') or
		!defined('DB_PASS') or
		!defined('DB_DB')) {
			
			echo '<b>Warning</b>: Please enter the database information required in config.php before running this spider!';
			exit;
		}
		
		
	
	// 
	//  SETTINGS
	////////////////
	
	// ....
	
	
	
		
	
	// Initialize the database (for outside usage)	
	$db=db_init();
	
	
	
	
	// 
	// Functionality
	//////////////
	
	
	
	// Setup our db
	function db_init() {
		global $db;
		$db=mysql_connect(constant('DB_HOST'), constant('DB_USER'), constant('DB_PASS'));
		if (!$db)
		{
			echo '<b>Warning</b>: Could not setup the database with the credentials provided in config.php';
			exit;	
		}
		
		
		
		// Select our Database
		if (!mysql_select_db(constant('DB_DB'),$db)) 
		{
			
			// If the Database could not be selected, then either create it or warn the user
			if (constant('DB_CREATE_ON_NOT_FOUND'))
			{
				$result=db_source('db_create.sql');
				if (!$result)
				{
					echo '<b>Warning</b>: Could not run db_create.sql!';
					exit;	
				}
				
				if (!mysql_select_db(constant('DB_DB'),$db))
				{
					echo '<b>Warning</b>: Error Selecting the newly created db from db_create.sql!  Check that you have matching credentials from config.php';
					exit;	
				}
			}
			else
			{
				echo '<b>Warning</b>: Could not find the database "<i>'.constant('DB_DB').'</i>"';
				exit;
			}
		}
		
		
		
		return $db;
	}
	
	
	
	function db_close() {
		mysql_close($db);	
	}
	
	
	// SOURCE one of the files into the db
	//
	//    return: TRUE/FALSE
	function db_source($file) {
		
		$output=NULL;
		$return=NULL;
		$query='mysql -h'.constant('DB_HOST').' -u'.constant('DB_USER').' --password=  < '.constant('DIRECTORY_SYSTEM').'/database/'.$file;
		echo exec($query,$output,$return);
		return !$return;
	}
	
	
	
	
	
	
	
	
	
	///
	/// MAIN FUNCTIONALITY
	/////////////////
	
	
	
	//
	//  Categories
	/////////////////
	
	
	// db_insertCat: Inserts a category link into `categories`
	function db_insertCat($name,$url) {
		
		$db=db_init();
		
		$name=mysql_real_escape_string($name,$db);
		$url=mysql_real_escape_string($url,$db);
		
		$query="INSERT INTO `categories` (name,cat) VALUE('".$name."','".$url."')";
		$result=mysql_query($query,$db);
		return $result;
	}
	
	
	// db_getCats: Retrieves all the categories from `categories`
	function db_getCats() {
	
		$db=db_init();
		
		$uCategories=array();
		$query="SELECT * FROM `categories`";
		$result=mysql_query($query,$db);
		if (!$result or mysql_num_rows($result)<1)
			return FALSE;
		while ($row=mysql_fetch_assoc($result)) {
			array_push($uCategories,$row);	
		}
		
		return $uCategories;
	}
	
	
	
	//
	//  Apps
	/////////////////
	
	
	// db_insertApp: Insert an app into `apps`
	//
	//    @categoryid
	//    @title
	//    @url
	//    @icon
	//    @company
	//    @rating
	//    @price
	function db_insertApp($categoryid, $title, $url, $icon, $company, $rating, $price) {
		
		$db=db_init();
		
		$categoryid=mysql_real_escape_string($categoryid,$db);
		$title=mysql_real_escape_string($title,$db);
		$url=mysql_real_escape_string($url,$db);
		$icon=mysql_real_escape_string($icon,$db);
		$company=mysql_real_escape_string($company,$db);
		$rating=mysql_real_escape_string($rating,$db);
		$price=mysql_real_escape_string($price,$db);
		
		$query="INSERT INTO `apps` (categoryid, title, url, icon, company, rating, price, description, devurl, updated, version, reqversion, whatsnew, permissions) VALUE('".$categoryid."','".$title."','".$url."','".$icon."','".$company."','".$rating."','".$price."', '', '', '', '', '', '', '')";
		echo $query.'<br/>';
		$result=mysql_query($query,$db);
		return $result;
	}
	
	
	// db_updateApp: Updates the remaining fields of the app in `apps`
	//
	//    @appid
	//    @desc
	//    @devurl
	//    @screenshots array( 'url' => _URL_ )
	//    @updated
	//    @version
	//    @reqversion
	//    @reviews array( 'title' => _TITLE_ ,
	//					  'rating' => _RATING_ ,
	//					  'reviewer' => _REVIEWER_ ,
	//					  'date' => _DATE_,
	//					  'review' => _REVIEW_ )
	//    @whatsnew 
	//    @permissions
	function db_updateApp($appid,$desc='',$devurl='',$screenshots=array(),$updated='',$version='',$reqversion='',$reviews=array(),$whatsnew='',$permissions='') {
		
		$db=db_init();
		
		
		// Sanitize our fields
		$desc=mysql_real_escape_string($desc,$db);
		$devurl=mysql_real_escape_string($devurl,$db);
		$updated=mysql_real_escape_string($updated,$db);
		$version=mysql_real_escape_string($version,$db);
		$reqversion=mysql_real_escape_string($reqversion,$db);
		$whatsnew=mysql_real_escape_string($whatsnew,$db);
		$permissions=mysql_real_escape_string($permissions,$db);
		
		foreach ($screenshots as &$screenshot) {
			$screenshot['url']=mysql_real_escape_string($screenshot['url'],$db);	
		}
		
		foreach ($reviews as &$review) {
			
			$review['title']=mysql_real_escape_string($review['title'],$db);	
			$review['rating']=mysql_real_escape_string($review['rating'],$db);	
			$review['author']=mysql_real_escape_string($review['author'],$db);	
			$review['date']=mysql_real_escape_string($review['date'],$db);	
			$review['review']=mysql_real_escape_string($review['review'],$db);	
		}
		
		
		
		
		// Update the App in `apps`
		$query="UPDATE `apps` SET description='".$desc."', devurl='".$devurl."', updated='".$updated."', version='".$version."', reqversion='".$reqversion."', whatsnew='".$whatsnew."', permissions='".$permissions."' WHERE id='".$appid."'";
		$result=mysql_query($query,$db);
		if (!$result)
			return FALSE;
		
		// Insert the Screenshots
		foreach ($screenshots as $ss) {

			$query="INSERT INTO `screenshots` (appid,url) VALUE('".$appid."','".$ss."')";
			mysql_query($query,$db);
		}
		
		// Insert the Screenshots
		foreach ($reviews as $rr) {
			
			$query="INSERT INTO `reviews` (appid,title,rating,reviewer,date,review) VALUE('".$appid."','".$rr['title']."','".$rr['rating']."','".$rr['author']."','".$rr['date']."','".$rr['review']."')";
			mysql_query($query,$db);
		}
		
		return TRUE;
	}
	
	
	
	
	
	
	
/* End of File -- database.php */