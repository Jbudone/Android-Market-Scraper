<?php

/*
	Author: JB Braendel (Jbudone)
	Date: February 13, 2011
	File: spider.php
	Desc: The spider section of market.android's crawler
	

*/


	// Requirements
	require_once('database.php');


	///////////
	///
	///
	///   SPIDER
	///
	/// All the spider functionality
	/////////////////////////////////
	/////////////////////////////////



	class Spider {
		
		
		public $db=NULL;
		public $url_index='https://play.google.com/store/apps';
		public $url=NULL;
		public $src=NULL;
		public $XPath=NULL;
		public $XDoc=NULL;
		
		
		function __construct() {
			
			$this->db=db_init();
		}
		
		function __destruct() {
			
			mysql_close($this->db);
		}
		
		
		//
		//  FUNCTIONALITY
		//////////////////
		
		
		
		// mineLinks: Mine all the categories from the front page (subcategories, NOT the `games` and `applications` parents)
		public function mineCats() {
			
			// Load up the index
			$this->url=$this->url_index;
			$this->loadPage($this->url);
			$this->loadXPath();
			
			// Grab each of the category/links
			$item='//li[@class="category-subitem "]/a';
			$cats=$this->XPath->query($item.'|'.$item.'/@href',$this->XDoc);
			$eCategory=array();
			foreach ($cats as $cat) {
				$eCategory[$cat->nodeName]=$cat->nodeValue;
				if (isset($eCategory['a']) and isset($eCategory['href']))
				{
					// Get the Category out of the href
					$href=$eCategory['href'];
					$matches=array();
					preg_match('/\/([A-Z_-]+)$/', $href, $matches);
					if (!$matches or empty($matches) or count($matches)<2)
					{
						if (constant('EXIT_ON_ERROR')) {
							echo '<b>Warning</b>: Error grabbing the category from "<i>'.$href.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error';
							exit;	
						}
						else {
							continue;
						}
					}
					$href=$matches[1];
					
					// Insert this category into the db
					db_insertCat($eCategory['a'],$href);
					unset($eCategory);
					$eCategory=array();
				}
			}
		}
		
		
		// crawlCat: Crawls through a category recursively loading each page (until there's no pages left)
		//
		//    @category: array(id => the id of the category in the db
		//						name => name of the category
		//						cat => the cat name/id)
		//    @url: The url page to be scraping from (this provides the free/paid section), NOTE: this does NOT include the start/num parameters
		//    @start: (url param) What number to start from
		//    @num: (url param) Num to include 
		public function crawlCat($category,$url,$start=0,$num=12) {
			
			// Load up the page
			$this->url=$url.'&start='.$start.'&num='.$num;
			$this->loadPage($this->url);
			$this->loadXPath();
			
			
			// Grab each of the apps
			//   -- title, company, icon, rating, price
			$item='//div[@class="snippet snippet-medium"]';
			$apps=$this->XPath->query($item.'//div[@class="thumbnail-wrapper goog-inline-block"]//img/@src|'.$item.'//div[@class="thumbnail-wrapper goog-inline-block"]//span/@title|'.$item.'//a[@class="title"]|'.$item.'//a[@class="title"]/@href|'.$item.'//div[@class="attribution"]/a|'.$item.'//div[@class="buy-border"]//a/@data-docprice',$this->XDoc);
			$count=0;
			$eElement=array();
			foreach ($apps as $app) {
				
				if (isset($eElement[$app->nodeName]) and $app->nodeName=='a')
					$eElement['company']=$app->nodeValue;
				else
					$eElement[$app->nodeName]=$app->nodeValue;
					
				if (isset($eElement['src']) and 
					isset($eElement['title']) and 
					isset($eElement['a']) and 
					isset($eElement['company']) and
					isset($eElement['data-docprice'])) {
						
						
						
						
						// Regex our href-link id (/details?id=com.herocraft.game.majesty)
						$href=$eElement['href'];
						$matches=array();
						preg_match('/\?id=([0-9a-zA-Z\Q._-\E]+)$/', $href, $matches);
						if (!$matches or empty($matches) or count($matches)<2)
						{
							if (constant('EXIT_ON_ERROR')) {
								echo '<b>Warning</b>: Error grabbing the link id from "<i>'.$href.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error.. &lt;url:<u>'.$url.'</u>&gt;';
								exit;	
							}
							else {
								continue;
							}
						}
						$href=$matches[1];
						
						
						
						// Regex our rating
						$rating=$eElement['title'];
						$matches=array();
						preg_match('/([0-9\Q.\E]+)/', $rating, $matches);
						if (!$matches or empty($matches) or count($matches)<2)
						{
							if (constant('EXIT_ON_ERROR')) {
								echo '<b>Warning</b>: Error grabbing the rating from "<i>'.$rating.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error.. &lt;url:<u>'.$url.'</u>&gt;';
								exit;	
							}
							else {
								continue;
							}
						}
						$rating=$matches[1];
						
						
						
						// Regex our price
						$price=$eElement['data-docprice'];
						$matches=array();
						preg_match('/((Free)|(FREE)|(free)|\$([0-9\Q.\E]+))/', $price, $matches);
						if (!$matches or empty($matches) or count($matches)<2)
						{
							if (constant('EXIT_ON_ERROR')) {
								echo '<b>Warning</b>: Error grabbing the price from "<i>'.$price.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error.. &lt;url:<u>'.$url.'</u>&gt;';
								exit;	
							}
							else {
								continue;
							}
						}
						$price=$matches[2];
						if ($price=='free')
							$price='0';
						
						
						
						db_insertApp($category['id'],$eElement['a'],$href,$eElement['src'],$eElement['company'],$rating,$price);	
						/*echo 'href: '.$href.'<br/>';
						echo 'rating: '.$rating.'<br/>';
						echo 'price: '.$price.'<br/>';
						print_r($eElement);*/
						unset($eElement);
						$eElement=array();
						$count++;
						
					}
			}
			
			
			
			// Check for next page
			if ($count>=$num) {
				if (constant('TEST_CAT_ONEPAGE')===TRUE)
					return;
					
				$this->crawlCat($category,$url,$start+$num,$num);
			}
			else
			{
				//echo 'Count: '.$count. ' on page <a href="'.$this->url.'">here</a>';
			}
		}
		
		
		
		
		// scrapeApp: Scrape a given url of an app, and update it in the db
		//
		//	  @appid
		//    @url
		function scrapeApp($appid,$url) {
			
			// Load up the page
			$this->url=$url;
			$this->loadPage($this->url);
			$this->loadXPath();
			
			
			// XQuery the variables
			//   -- description, developer's url, updated date, version #, required version #, whatsnew, permissions, screenshots*, reviews*
			$description='';
			$devurl='';
			$updated='';
			$version='';
			$required='';
			$whatsnew='';
			$permissions='';
			
			
			// Description
			$description=$this->XPath->query('//div[@class="doc-description toggle-overflow-contents"]',$this->XDoc);
			$description=$description->item(0)->nodeValue;
			
			// Developers URL
			$devurl=$this->XPath->query('//div[@class="doc-overview"]//a[@rel="nofollow"]/@href',$this->XDoc);
			$devurl=$devurl->item(0)->nodeValue;
			
			// meta data..
			$meta=$this->XPath->query('//div[@class="doc-metadata"]',$this->XDoc);
			$meta=$meta->item(0)->nodeValue;
			
			// Regex our update-date
			$matches=array();
			preg_match('/(January|February|March|April|May|June|July|August|September|October|November|December) [0-9]{1,2}, [0-9]{4}/', $meta, $matches);
			if (!$matches or empty($matches) or count($matches)<1)
			{
				if (constant('EXIT_ON_ERROR')) {
					echo '<b>Warning</b>: Error grabbing the date from "<i>'.$meta.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error... &lt;appid:<a href="'.$url.'" target="_blank"><u>'.$appid.'</u></a>&gt;';
					exit;	
				}
				else {
					//continue;
				}
			}
			else
				$updated=$matches[0];
			
			
			
			// Regex our version
			$matches=array();
			preg_match('/Version:([a-zA-Z0-9\Q.-_\E]+)/', $meta, $matches);
			if (!$matches or empty($matches) or count($matches)<2)
			{
				if (constant('EXIT_ON_ERROR')) {
					echo '<b>Warning</b>: Error grabbing the version from "<i>'.$meta.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error.. &lt;appid:<u>'.$appid.'</u>&gt;';
					exit;	
				}
				else {
					//continue;
				}
			}
			else
				$version=$matches[1];
			
			
			
			// Regex our required version
			$required='';
			$matches=array();
			preg_match('/Requires Android:([0-9\Q.\E]+)/', $meta, $matches);
			if (!$matches or empty($matches) or count($matches)<2)
			{
				if (constant('EXIT_ON_ERROR')) {
					echo '<b>Warning</b>: Error grabbing the required version from "<i>'.$meta.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error.. &lt;appid:<u>'.$appid.'</u>&gt;';
					exit;	
				}
				else {
					//continue;
				}
			}
			else
				$required=$matches[1];
			
			
			
			// Whats New section
			$whatsnew=$this->XPath->query('//div[@class="doc-whatsnew-container"]',$this->XDoc);
			$whatsnew=$whatsnew->item(0)->nodeValue;
			
			
			// Permissions section
			$permissions=$this->XPath->query('//div[@class="doc-specs-container"]',$this->XDoc);
			$permissions=$permissions->item(0)->nodeValue;
			
			
			
			
			
			
			
			
			
			
			
			//
			//
			//  SCREENSHOTS
			////////////////////
			
			$eScreenshots=$this->XPath->query('//div[@class="doc-screenshot-section"]//img[@class="doc-screenshot-img"]/@src',$this->XDoc);
			$screenshots=array();
			foreach($eScreenshots as $screenshot) {	
				array_push($screenshots,$screenshot->nodeValue);
			}
			
			
			
			
			
			//
			//
			//  REVIEWS
			////////////////////
			
			// Title, Rating, Reviewer, Date, Review
			
			$item='//div[@class="doc-reviews-list"]//div[@class="doc-review"]';
			$eReviews=$this->XPath->query($item.'/h4|'.$item.'//span[@class="ratings"]/@title|'.$item.'//span[@class="doc-review-author"]/strong|'.$item.'//span[@class="doc-review-date"]|'.$item.'/p',$this->XDoc);
			$reviews=array();
			$uReview=array();
			foreach($eReviews as $review) {	
			
				// Push the field into our review info, as long as there's no overlapping of fields
				$overlap=isset($uReview[$review->nodeName]);
				if (!$overlap)
					$uReview[$review->nodeName]=$review->nodeValue;
					
				if ($overlap or (isset($uReview['h4']) and
					isset($uReview['title']) and
					isset($uReview['strong']) and
					isset($uReview['span']) and
					isset($uReview['p']))) {
						
						// Regex the Rating
						$rating=$uReview['title'];
						$matches=array();
						preg_match('/([0-9\Q.\E]+)/', $rating, $matches);
						if (!$matches or empty($matches) or count($matches)<2)
						{
							if (constant('EXIT_ON_ERROR')) {
								echo '<b>Warning</b>: Error grabbing the rating from "<i>'.$rating.'</i>" .. please talk to the author, <b>Jbudone</b>, about this error.. &lt;appid:<u>'.$appid.'</u>&gt;';
								exit;	
							}
							else {
								continue;
							}
						}
						$rating=$matches[1];
						
						array_push($reviews,array('title'=>$uReview['h4'],
													'rating'=>$rating,
													'author'=>$uReview['strong'],
													'date'=>$uReview['span'],
													'review'=>$uReview['p']));
						unset($uReview);
						$uReview=array();
						
						// Add our almost-overlapped field
						if ($overlap) {
							$uReview[$review->nodeName]=$review->nodeValue;
						}
					}
			}
			
			
			/*echo 'Description: '.$description.'<br/>';
			echo 'Developers URL: '.$devurl.'<br/>';
			echo 'Last Updated: '.$updated.'<br/>';
			echo 'Version: '.$version.'<br/>';
			echo 'Required Version: '.$required.'<br/>';
			echo 'Whats New: '.$whatsnew.'<br/>';
			echo 'Permissions: '.$permissions.'<br/>';
			print_r($screenshots);
			print_r($reviews);*/
			db_updateApp($appid,$description,$devurl,$screenshots,$updated,$version,$required,$reviews,$whatsnew,$permissions);
		}
		
		
		
		//
		// RAW FUNCTIONALITY
		/////////////////
		
		
		// loadXPath: Loads the XPath for the given source code
		//   NOTE: $this->src  MUST  be set first
		private function loadXPath() {
			
			// Load XML/XPath for the src
			libxml_use_internal_errors(TRUE);
			$this->XDoc = new DOMDocument('1.0', constant('ENCODING_SET'));
			$this->XDoc->loadHTML('<html>'.$this->src.'</html>');
			libxml_clear_errors();  // NOTE: Use this otherwise memory leak!!
			$this->XPath = new DOMXPath($this->XDoc);
		}
		
		
		// loadPage: Loads a url's source code into $this->src
		private function loadPage($url) {
			
			
			// Connect
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: text/xml; charset='.constant('ENCODING_SET') ));
			$this->src = utf8_decode(curl_exec($ch));
			curl_close($ch);
		}
		
	}








/* End of File - spider.php */
