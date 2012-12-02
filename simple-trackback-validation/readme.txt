=== Simple Trackback Validation ===
Contributors: Michael_
Donate link: http://sw-guide.de/donation/
Tags: trackback, validation, spam, anti-spam, protection 
Requires at least: 2.0
Tested up to: 2.9
Stable tag: 2.1

Performs a simple but very effective test on all incoming trackbacks in order to stop trackback spam.

== Description ==

Simple Trackback Validation Plugin performs a simple but very effective test on all incoming trackbacks in order to stop trackback spam.

**How it works:**

When a trackback is received, this plugin

1. checks if the IP address of the trackback sender is equal to the IP address of the webserver the trackback URL is referring to. This reveals almost every spam trackback (more than 99%) since spammers do usually use bots which are not running on the machine of their customers.

2. retrieves the web page located at the URL included in the trackback. If the page doesn’t a link to your blog, the trackback is considered to be spam. Since most trackback spammers do not set up custom web pages linking to the blogs they attack, this simple test will quickly reveal illegitimate trackbacks. Also, bloggers can be stopped abusing trackback by sending trackbacks with their blog software or webservices without having a link to the post.

There are several options available and you also can enable logging to get all actions, which are performed by the plugin, logged. Also, you can select how to treat spam trackbacks (do not save in the database or mark as spam or place into moderation) and several other stuff.

Please visit [the official website](http://sw-guide.de/wordpress/simple-trackback-validation-plugin/ "Simple Trackback Validation") for further details and the latest information on this plugin.

== Installation ==

See on [the official website](http://sw-guide.de/wordpress/simple-trackback-validation-plugin/ "Simple Trackback Validation").


== Frequently Asked Questions ==

= Where can I get more information? =

Please visit [the official website](http://sw-guide.de/wordpress/simple-trackback-validation-plugin/ "Simple Trackback Validation") for the latest information on this plugin.