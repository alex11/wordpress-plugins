=== FV Gravatar Cache ===
Contributors: FolioVision
Tags: gravatar,avatar,cache
Requires at least: 2.7
Tested up to: 3.1.2
Stable tag: trunk

Speeds up your website by making sure the gravatars are stored on your website and not loading from the gravatar server.

== Description ==

There's one problem with Gravatars. They are very slow to load. Each page with comments on them makes one call per comment to the Gravatar server. While a single call takes only a 100ms, on a page with hundreds of comments, we are talking about major slowdowns. Page loads can take 20 seconds and more.

This plugin solves this problem by:

* caching gravatars with Wordpress cron job.. 

* caching gravatars on comment submission

* maintaining a single copy of the default gravatar instead of downloading it again and again for all the email addresses with no gravatar associtated

[Installation guide](http://foliovision.com/seo-tools/wordpress/plugins/fv-gravatar-cache/installation)

[Support and more information](http://foliovision.com/seo-tools/wordpress/plugins/fv-gravatar-cache)

== Changelog ==

= Version 0.3.3 =

* Bugfix for empty gravatar in cache

= Version 0.3.2 =

* Bug fix for Wordpress 3.1 admin bar
* Bug fix for blank gravatar

= Version 0.3.1 =

* Better detection of missing/default gravatars

= Version 0.3 =

* First public release

= Version 0.2 =

* Added cron support

= Version 0.1 =

* Works only for logged in users

== Frequently Asked Questions ==

= Gravatars are not caching properly =

Hit the "Empty Cache" button to clear the cache database and clear out the cache directory by hand (you can see the path at the very top of the Settings screen). Then hit the "Run Cron Now" button, it will refresh a couple of gravatars, so you can check the cache directory again, to see if it's filling up correctly. "Current Cron offset:" will increment, so you can track the progess and see how the cron is running. You can also turn on "Debug mode" and check log.txt afterwards.

= Generated (Itenticon, Wavatar and MonsterID) gravatars are not working correctly! =

These types of gravatars are not currently supported. Drop us a note and we might add this feature in next release.

= I want to have bigger gravatars on author profile pages. Your plugin supports only one gravatar size. Is there any workaround? =

You can turn edit your template to turn off the FV Gravatar Cache just when needed, add this code right before that section in your Wordpress template php file:

&lt;?php global $FV_Gravatar_Cache; remove_filter( 'get_avatar', array( &$FV_Gravatar_Cache, 'GetAvatar' ) ); ?&gt;

In case you need the FV Gravatar Cache running later in the template, just bring it back with:

&lt;?php global $FV_Gravatar_Cache; add_filter( 'get_avatar', array( &$FV_Gravatar_Cache, 'GetAvatar' ) ); ?&gt;

= I don't see the default gravatar on my options page! =

If you selected empty gravatar in wp-admin -> Settings -> Discussion, then it's ok. Otherwise try to resave the options and it should appear.

== Installation ==

You can use the built in installer and upgrader, or you can install the plugin
manually.

After installing, make sure you visit the plugin settings to make sure the plugin works correctly. You will see a check if the cache directory is writable and you will have to set the gravatar size (plugin also uses autodetection mechanism, which might come handy).

== Screenshots ==

1. FV Gravatar Cache screen
