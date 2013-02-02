# Android Market Scraper

A web-spider based approach to scraping the Android App Market

How it Works
=============

Down and simple: its an automated script to connect to the Android app market
and scrape a each and every individual app for all the details it can, then add
it to the database for your viewing pleasures. 

However, since its a webspider, it has to actually load each and every
individual app link in a cURL request, which can obviously take a painfully
long time. This is probably why the Android team came out with the App-Market
API shortly afterwards. That said, its *probably* much more beneficial, faster
and more efficient for you to use another script which implements the API. 
However, if you are still interested in my web-spider approach, feel free to
read on :)

Installation
=============

```````
1) drop this script wherever
2) edit system/config.php to match your settings
	NOTE: there are TWO environments for this, local and online
		this will help for users who want to test things locally
		first, but also have online settings with the same script
3) either run marketandroid.sql or don't, if you don't then just
	set DB_CREATE_ON_NOT_FOUND in the configs to true, and it'll
	do it automatically for you
````````

That's it! Pretty easy :)

Bugs
==============

It can't read Chinese characters yet, those just show up as ???????
Any ideas? Suggestions? Feel free to send me a message

Donations
==============

Donations are always greatly appreciated. Feel free to Paypal me at Jbud@live.ca
..or not, that's cool too I guess
