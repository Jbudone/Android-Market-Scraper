<?php

/*
	Author: JB Braendel (Jbudone)
	Date: February 13, 2011
	File: controller.php
	Desc: Controller for the spider
	

*/


	// Requirements
	ini_set('max_execution_time',0);
	
	require_once('database.php');
	require_once('spider.php');



	///////////
	///
	///
	///   CONTROLLER
	///
	/// Control over the spider done here
	/////////////////////////////////
	/////////////////////////////////



	class Controller {
	
		public $db=NULL;
		
		
		function __construct() {
			
			
			// Clear the DB to prepare it for the spider
			$this->db=db_init();
			if (constant('DB_CLEAR_ON_START')) {
				db_source('db_clear.sql');
			}
		}
		
		function __destruct() {
			
			unset($this->db);
		}
		
		
		// Start our controller
		function start() {
			
			
			$spider=new Spider();
			
			
			// Scrape all the Categories
			$spider->mineCats();
			$categories=db_getCats();
			foreach ($categories as $category) {
				
				// PAID				
				$url='https://market.android.com/details?id=apps_topselling_paid&cat='.$category['cat'];
				$spider->crawlCat($category,$url);
				
				
				
				// FREE				
				$url='https://market.android.com/details?id=apps_topselling_free&cat='.$category['cat'];
				$spider->crawlCat($category,$url);
				
				
				if (constant('TEST_CAT_SINGLE')===TRUE)
					break;
			}
			
			
			
			// Scrape the Apps
			if (constant('TEST_APP_LIMIT')>0)
				$query="SELECT * FROM `apps` LIMIT ".constant('TEST_APP_LIMIT');
			else
				$query="SELECT * FROM `apps`";
			$result=mysql_query($query,$this->db);
			while($row=mysql_fetch_assoc($result)) {
				$url='https://market.android.com/details?id='.$row['url'];
				$spider->scrapeApp($row['id'],$url);
			};
			
			
			
			
			// Print out the Listing
			$query="SELECT * FROM `apps`";
			$result=mysql_query($query,$this->db);
			while($row=mysql_fetch_assoc($result)) {
				print_r($row);	
			}
		}
		
		
		
		function scrape_Categories() {
			
		}
		
		
		function scrape_Apps() {
			
		}
		
	}











/* End of File - controller.php */