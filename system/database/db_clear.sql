######
###
###  DATABASE
### clear the database here
###############
###############


USE marketandroid;



## CLEAR each table in the db
DELETE FROM `categories`;
DELETE FROM `apps`;
DELETE FROM `screenshots`;
DELETE FROM `reviews`;

ALTER TABLE `categories` AUTO_INCREMENT=1;
ALTER TABLE `apps` AUTO_INCREMENT=1;
ALTER TABLE `screenshots` AUTO_INCREMENT=1;
ALTER TABLE `reviews` AUTO_INCREMENT=1;