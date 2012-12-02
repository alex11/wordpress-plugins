=== Plugin Name ===
Contributors: verysimple
Donate link: http://verysimple.com/products/hungryfeed/
Tags: inline,embed,rss,feed,reader,feed reader,page,rss import,rss include,simplepie,inline rss,rss feed,feed reader,rss reader,inline feed reader,embed feed,inline rss feed
Requires at least: 2.8
Tested up to: 3.4.1
Stable tag: trunk

HungryFEED embeds and displays RSS feeds inline on your pages, posts or sidebar using Shortcodes.

== Description ==

HungryFEED allows you to embed and display an RSS feed inline on your posts, pages or sidebar
by adding a Shortcode.  Usage is easy, just use the following shortcode:

[hungryfeed url="http://verysimple.com/feed/"]

= Features =

* Uses WordPress Shortcodes to embed RSS feeds on any page, post or sidebar widget
* Has a variety of parameters to filter and format the feed
* Relies on WordPress built-in SimplePie for processing RSS data
* Fixes characters in URLs that may get mangled when editing in Visual mode
* Caches feeds and allows configuration of the cache expiration
* Outputs clean, HTML for easy styling with a CSS configuration setting
* Allows you to customize the HTML using templates
* Allows filtering of items in the RSS feed based on keywords
* Allows feed pagination

== Installation ==

Automatic Installation:

1. Go to Admin - Plugins - Add New and search for "hungryfeed"
2. Click the Install Button
3. Click 'Activate'

Manual Installation:

1. Download hungryfeed.zip (or use the WordPress "Add New Plugin" feature)
2. Unzip and upload 'hungryfeed' folder to your '/wp-content/plugins/' directory
3. To support caching, ensure the directory 'wp-content/cache' exists and is writable.
4. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. HungryFEED hungry for RSS feeds!
2. HungryFEED editor button assists you with the Shortcode for your page or post
3. The RSS feed is pulled, cached and displayed inline

== Frequently Asked Questions ==

= What is HungryFEED? =

HungryFEED is a plugin that includes an RSS feed within the content of a page or post

= 1. The main title and/or description of the feed is displaying, how can I hide that? =

To hide the feed title you can use the feed_items parameter to specify which feed fields you wish to appear like so: feed_items=""

= 2. I keep getting the verysimple.com feed instead of my own, what's the problem? =

The verysimple feed is used as the default value if no URL is provided, or WordPress can't read the URL parameter due to either a syntax error in the shortcode, or a special character in the feed url.  Here are some known causes:

* Using curly quotes instead of straight quotes (ie Ó vs  ")  See http://en.wikipedia.org/wiki/Quotation_mark_glyphs
* Certain special characters in the feed url must be url encoded.  Here are some known characters and their replacement:  "=%22 [=%5B  ]=%5D  (see http://www.w3schools.com/tags/ref_urlencode.asp)
* Copy/pasting the URL into the shortcode will sometimes create HTML instead of plain text.  Use the WordPress editor's HTML View to view the raw HTML source code of your post and make sure the URL parameter is plain text and not HTML code

= 3. I'm getting the error that wp-content/cache/ does not exist or is not writeable, what is wrong? =

In order to use caching, you must have a folder in wp-content called "cache" which is writable by the web server.

To do this, first reate an empty folder in /wp-content/ called "cache" if it does not already exist.
Next set permissions of /wp-content/cache/ to 755 or 777 (as necessary on your particular server).
Finally, open /wp.config.php and insert the following code anywhere below existing definitions:
define('ENABLE_CACHE', TRUE);

If you are not able to do create this folder, you can optionally go to Settings->HungryFEED and set the Cache Duration to 0.
However, it is strongly recommended that you do have this directory to enable caching because otherwise Wordpress will make a
new request to the RSS content provider every time any visitor views your page.  This makes your site slower because it
has to retrive the RSS content every time.  It could also be considered bad etiquette to continually hit your
content provider's feed.

= 4. How do I put double quotes into a feed url? =

To put double-quotes into a feed url, enter %22 instead of the double-quote.  This is necessary because the url parameter in the Shortcode is already double-quoted and so you have to use some other character.

= 5. HungryFEED won't read my feed, what do I do? =

First, enter the feed URL in your browser and see if it displays correctly.  If the feed appears to be valid, you may try using some of the debug parameters to investigate.  If you still cannot figure out the problem, you can submit a ticket for support (See the Technical Support section above)

= 6. How can I get a feed to appear in a sidebar widget? =

Shortcodes are not enabled in Widgets by default.  However as of HungryFEED 1.3.9 there is an option to enable Shortcode processing in Widgets on the HungryFEED settings page.  Be aware that this will enable Shortcode for all plugins, not just HungryFEED.  WordPress currently does not support selectively enabling Shortcode for a single plugin, so it is an all-or-nothing option.

= 7. Where do I go for support? =

Documentation is available on the plugin homepage at http://wordpress.org/tags/hungryfeed and questions may be posted on the support forum at http://wordpress.org/tags/hungryfeed

== Upgrade Notice ==

= 1.6.0 =
* supress notice on systems when rss feed has no source

== Changelog ==

= 1.6.0 =
* supress notice on systems when rss feed has no source

= 1.5.9 =
* fixed bug where templates containing html are htmlescaped

= 1.5.8 =
* parameters in shortcodes are passed through to the templates

= 1.5.7 =
* templates now use Mustache template engine
* added ability to enable shortcodes in templates
* added field for custom javascript
* added index_#, feed_title and feed_description template tags

= 1.5.6 =
* added order="none" parameter to not sort feed items

= 1.5.5 =
* fixed incorrect item count when using filter and max_items together

= 1.5.4 =
* further adjustments to error reporting

= 1.5.3 =
* improved error reporting in combination with other plugins

= 1.5.2 =
* added additional info to error checking for easier debugging

= 1.5.1 =
* added setting to customize the error output

= 1.5.0 =
* error message is displayed if data expression contains illegal characters

= 1.4.9 =
* fixed bug with "price" being displayed

= 1.4.8 =
* added ability to pull any field using the "data" variable in the template to pull in raw feed data
* added option in the shortcode wizard for "show_data" to view the raw feed data

= 1.4.7 =
* fixed bug with feeds that have no category

= 1.4.6 =
* added a button to the post/page editor to provide a GUI for creating shortcodes
* added index tag to be used in templates
* category tag in template now displays comma-delimited list if there are multiple categories

= 1.4.5 =
* added filter_out parameter to exclude items with certain keywords

= 1.4.4 =
* added order parameter to allow reverse or random order

= 1.4.3 =
* description can now be truncated using truncate_description parameter

= 1.4.2 =
* description can now be truncated using truncate_description parameter

= 1.4.1 =
* multiple filter words can now be specified, separated by a pipe | char
* Added attr selector in order to get attributes of selected elements, such as an image src
* added additional properties to the feed: soure, enclosure, id, category
* updated settings page with documentation

= 1.4.0 =
* Added feature to templates for transforming the RSS description field using phpquery selectors
* Filter parameter is now case-insensitive

= 1.3.9 =
* Added option to enable shortcodes in widgets

= 1.3.8 =
* HOTFIX pagination re-enabled

= 1.3.7 =
* HOTFIX disabled pagination feature while investing problem with permalinks

= 1.3.6 =
* Added page_size parameter for pagination (beta) and link_target for target in feed links

= 1.3.6 =
* Added page_size parameter for pagination (beta) and link_target for target in feed links

= 1.3.5 =
* Added filter parameter to include only items containing specified text

= 1.3.4 =
* Added feature for stripping ellipsis from rss description using parameter strip_ellipsis

= 1.3.3 =
* Added feature for stripping html from rss description using parameter allowed_tags

= 1.3.2 =
* Bug fix for unexpected T_OBJECT_OPERATOR

= 1.3.1 =
* Removed donation button and replaced it with a link to SmileTrain.  Happy Holidays!

= 1.3.0 =
* Added custom template settings that can be used to fully customize the output of the feed

= 1.2.1 =
* Fix to directory structure to make plugin work with wordpress installer

= 1.2.0 =
* Added parameter item_link_title to enable/disable the post title link
* Added parameter date_format to format the post date

= 1.1.0 =
* Improved feed reading when special characters get mangled by visual editor
* Improved error reporting when feeds cannot be read
* Awesome hungry monster logo added

= 1.0.0 =
*  Initial Release