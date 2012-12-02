=== Plugin Name ===
Contributors: spitzohr
Tags: buddypress
Requires at least: 2.9.1 / 1.2.4
Tested up to: 3.2.1 / 1.5
Stable Tag: 1.5

== Description ==

This plugin will check automatically for new activities and comments and add
them to the activity stream.

The JavaScript file "refresh.js" will send every 10 seconds an ajax request to
wp-load.php to get the new activities. If there are new activities, they will be
prepend to the activity list. Comments will appear in stream mode.

While developing we noticed performance problems on our test server (very old
WAMP). So we changed the refresh rate to 60 seconds. On our new server we
haven't those problems.

== Installation ==

1. Copy rs-buddypress-activity-refresh to /wp-content/plugins/
2. In the Wordpress Admin panel, visit the plugins page and Activate the plugin.

== Frequently Asked Questions ==

= Why are the comments in stream mode? =

New activities will appear at top of the activity list. Comments will also be
added on top in stream mode.

= How to format new activities =

New activities and comments have the css class "new-update".

Example:
`.new-update { background-color: #ffff00; }`

= How to change the refresh rate? =

You can change the refresh rate on the admin page.
Settings &gt; RS Buddypress Activity Refresh

== Screenshots ==

1. New comments will appear in stream mode.
2. If you reload the page, the comments will be displayed in thread mode
3. Admin Page to change the Refresh Rate

== Upgrade Notice ==

If upgrading the plugin, all your changes would be deleted.

== Changelog ==
= 1.5 =

* Replacing hook "bp_init" with "after_setup_theme". Now it works with Buddypress 1.5

= 1.2 =

* Adding jquery.timeago.js plugin to update the "since"-values
* Changing the Ajax loop to include group comments

= 1.1.4 =

* Reset the page title when the activity list was refreshed by user

= 1.1.3 =

* Move the Admin Page to the Buddypress Menu

= 1.1.2 =

* The javascript add the count of new entries to the page title

= 1.1 =

* break the loop, when the current id is less than the last id. All other
  entries already in the list or older.
* changing javascript to send the last_id instead of list with all ids
* doublecheck in javascript if the activity id already exists before adding
  entry
* adding translations german/english

= 1.0.7 =

* Adding Admin Page to set Refresh Rate

= 1.0.6 =

* Bug Fixing

= 1.0.5 =

* code cleaning

= 1.0.4 =

* Initial stable release
