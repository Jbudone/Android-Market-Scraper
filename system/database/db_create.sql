######
###
###  DATABASE
### create the database here
###############
###############


## CREATE
CREATE DATABASE IF NOT EXISTS marketandroid;
USE marketandroid;



## 
## TABLES
############



## CATEGORIES
CREATE TABLE IF NOT EXISTS `categories` (
	id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	cat VARCHAR(255) NOT NULL UNIQUE ) ENGINE=InnoDB CHARACTER SET utf8;
	
	
## APPS
CREATE TABLE IF NOT EXISTS `apps` (
	id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	categoryid INT UNSIGNED NOT NULL,
	title TINYTEXT NOT NULL,
	url TINYTEXT NOT NULL,
	icon TINYTEXT NOT NULL,
	company TINYTEXT NOT NULL,
	rating DECIMAL(4,2) NOT NULL DEFAULT 00.00,
	price DECIMAL(4,2) NOT NULL DEFAULT 00.00,
	description TEXT NOT NULL,
	devurl TINYTEXT NOT NULL,
	updated TINYTEXT NOT NULL,
	version TINYTEXT NOT NULL,
	reqversion TINYTEXT NOT NULL,
	whatsnew TEXT NOT NULL,
	permissions TEXT NOT NULL,
	FOREIGN KEY (categoryid) REFERENCES `categories` (id) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
	

## SCREENSHOTS
CREATE TABLE IF NOT EXISTS `screenshots` (
	id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appid INT UNSIGNED NOT NULL,
	url TINYTEXT NOT NULL,
	FOREIGN KEY (appid) REFERENCES `apps` (id) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB;
	

## REVIEWS
CREATE TABLE IF NOT EXISTS `reviews` (
	id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	appid INT UNSIGNED NOT NULL,
	title TINYTEXT NOT NULL,
	rating DECIMAL(4,2) NOT NULL DEFAULT 00.00,
	reviewer TINYTEXT NOT NULL,
	date TINYTEXT NOT NULL,
	review TEXT NOT NULL,
	FOREIGN KEY (appid) REFERENCES `apps` (id) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;