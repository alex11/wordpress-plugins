=== Plugin Name ===
Contributors: pjgalbraith
Donate link: http://www.pjgalbraith.com/2011/08/recent-posts-plus/
Tags: posts, recent, recent posts, widget, post-plugins
Requires at least: 2.9.0
Tested up to: 3.3
Stable tag: trunk

An advanced version of the WordPress Recent Posts widget, allows display of thumbnails, post excerpt, author, comment count, and more.

== Description ==

An advanced version of the WordPress Recent Posts widget allowing increased customization.

Features Include:

* Display post thumbnails, with customizable size.
* Display post excerpt, author, comment count, and more. 
* Provides options to trim the number of characters in the title and excerpt.
* Override the post order to order by; date modified, title, post ID, random, comment count etc.
* Exclude or include specific posts, authors, tags, or categories.
* Also includes a simple template parser so you can override the default output making custom styling easy.

== Installation ==

1. Upload `recent-posts-plus.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the widget to your sidebar in 'Appearance/Widgets' in WordPress

== Frequently Asked Questions ==

See documentation at http://www.pjgalbraith.com/2011/08/recent-posts-plus/ and existing questions and answers at http://www.pjgalbraith.com/2011/08/recent-posts-plus/#comments

== Screenshots ==

1. Widget options screen

== Changelog ==

= 1.0.10 =
* Fixed excerpt ellipsis being added to the title incorrectly

= 1.0.9 =
* Added option to customize the ellipsis within the Widget Output Template using `{ELLIPSIS}...{/ELLIPSIS}` tag
* The output of more template tags is now available when using PHP within the Widget Output Template
* Other minor tweaks

= 1.0.8 =
* Fixed tags being broken by truncation
* Note: links are being stripped again but formatting should be same as v 1.0.4
* Widget admin javascript is only being added when needed
* Added code to prevent direct script access

= 1.0.7 =
* Added option to display post author's avatar. Using `{AUTHOR_AVATAR}` tag
* Added ability to add raw PHP code to the widget output template option

= 1.0.6 =
* Link and paragraph tags no longer stripped from excerpt. Now matches output of v 1.0.4

= 1.0.5 =
* Added ability to display custom fields i.e. meta-data
* Added option to truncate title and excerpt by number of words instead of just chars
* Added custom excerpt trimming function to avoid issues caused by WordPress' overly simplistic excerpt function
* Minor localization fixes
* Small reduction in DB queries

= 1.0.4 =
* Added ability to override the date format inline, so you can have multiple date formats
* Changed the default date format to "M j"

= 1.0.3 =
* Fixed issue with limiting excerpt chars

= 1.0.2 =
* Fixed issue with thumbnail size input field width in Google Chrome
* Fixed issue when adding a new widget instance, expert options wouldn't toggle on
* Added licence
* Added readme
* Added screenshot

= 1.0.1 =
* Prevented htmlspecialchars from being encoded twice

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.10 =
Fixed excerpt ellipsis being added to the title incorrectly

= 1.0.9 =
Added option to customize the ellipsis within the Widget Output Template

= 1.0.8 =
Fixed tags being broken by truncation, plus other small fixes.

= 1.0.7 =
Added ability to display author avatar and use raw php in output template.

= 1.0.6 =
Link and paragraph tags no longer stripped from excerpt. Now matches output of v 1.0.4

= 1.0.5 =
Added custom field tag, ability to truncate by words, and other fixes. See documentation for more info.

= 1.0.4 =
Added new inline date formatting. See documentation for more info.

= 1.0.3 =
Fixed issue with limiting excerpt chars.

= 1.0.2 =
Fixes issues with widget options panel.

= 1.0.1 =
Fixes rendering issue.

= 1.0.0 =
Initial release.

== Documentation ==

See documentation at http://www.pjgalbraith.com/2011/08/recent-posts-plus/