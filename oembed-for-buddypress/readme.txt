=== oEmbed for BuddyPress ===
Contributors: r-a-y
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KU38JAZ2DW8TW
Tags: buddypress, oembed, embed
Requires at least: WP 2.9 / WPMU 2.9.1.1 & BuddyPress 1.2
Tested up to: WP 2.9 / WPMU 2.9.1.1 & BuddyPress 1.2
Stable tag: 0.52

The easiest way to share your favorite content from sites like YouTube, Flickr, Hulu and more on your BuddyPress network. 

== Description ==

The easiest way to share your favorite content from sites like YouTube, Flickr, Hulu and more on your BuddyPress network.

oEmbed for BuddyPress utilizes Wordpress' own oEmbed class, so by default, you can share content from the following sites:

* YouTube
* Blip.tv
* Vimeo
* DailyMotion
* Flickr
* Hulu
* Viddler
* Qik
* Revision3
* Photobucket
* Scribd
* Wordpress.tv

How do you use the plugin?  Simple!  Input **any** URL from one of the listed sites above into an activity update or forum post in BuddyPress.

When the update is posted, the URL automagically transforms into the embedded content.


== Installation ==

#### This plugin requires Wordpress 2.9 or Wordpress MU 2.9.1.1 and BuddyPress 1.2 ####

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
1. Login to the Wordpress dashboard and navigate to "Plugins > Installed".  Activate the "oEmbed for BuddyPress" plugin (for WPMU users, activate the plugin on the blog where BuddyPress is activated).


#### Upgrading ####

1. Deactivate the plugin.
1. If you made changes to the config file, make a backup of `bp-oembed-config.php`
1. Overwrite all files for the plugin.
1. Re-add custom settings from `bp-oembed-config.php` (if applicable)
1. Reactivate the plugin.


== Frequently Asked Questions ==

#### What is oEmbed ####

[oEmbed](http://www.oembed.com/) is a simple API that allows a website to display embedded content (such as photos or videos) when a user posts a link to that resource.  It was designed to avoid copying and pasting HTML from the media you wish to embed.


#### What is oEmbed for BuddyPress? ####

oEmbed for BuddyPress utilizes [Wordpress' own oEmbed class](http://codex.wordpress.org/Embeds), so by default, you can share content from the following sites:

* YouTube
* Blip.tv
* Vimeo
* DailyMotion
* Flickr
* Hulu
* Viddler
* Qik
* Revision3
* Photobucket
* Scribd
* Wordpress.tv

The plugin allows you to input **any** URL from one of the listed sites above into an activity update or forum post in BuddyPress.

When the update is posted, the URL automagically transforms into the embedded content.  There is no GUI.

For more information, check out the "Other Notes" tab.


#### What version of BuddyPress do I need to use this plugin? ####

You need at least BuddyPress 1.2, which in turn requires Wordpress 2.9 or Wordpress MU 2.9.1.1.


#### Where's the admin settings page? ####

oEmbed for BuddyPress works transparently in the background.

There is no admin settings page!  This is intentional (at least for now).  All settings can be modified in `bp-oembed-config.php`.

By default, the plugin allows embedding in activity updates, activity comments and forum posts.


#### Then how do I turn off oEmbed for certain BuddyPress components? ####

Open `bp-oembed-config.php` in a text editor.

Let's say you wanted to disable oEmbed for activity comments.

Find the following line:

`$bp_oembed['activity_comments'] = true;`

And change it to:

`$bp_oembed['activity_comments'] = false;`


#### How do I extend Wordpress' oEmbed provider list? ####

By default, you can only embed content from websites listed on Wordpress' internal whitelist. This is to prevent the embedding of malicious content from untrustworthy websites.

To add an oEmbed provider, read the following article for more info:
http://codex.wordpress.org/Embeds#Adding_Support_For_An_oEmbed-Enabled_Site

The other option is you can override Wordpress' internal whitelist and enable *any* site that is oEmbeddable by downloading and activating Viper007Bond's Enable oEmbed Discovery plugin.
**You should only activate the oEmbed Discovery plugin if you trust your user base. You've been warned.**


== Technical info ==

Because oEmbed for BuddyPress checks each link to see if it is oEmbeddable, for performance reasons, each link is cached in the database to reduce redundant oEmbed requests.

The cached entry is either the embed code (if the link is oEmbeddable) or the failed link.

**Whitelist feature**

By default, the plugin *whitelists* hyperlinks and URLs residing on the same domain as BuddyPress.

The cool thing is you can extend the whitelist.

For example, in an activity update, say you type in "http://www.google.com", the plugin will cache that link in the database.  Say you wanted to omit Google.com links from being cached.

Open `bp-oembed-config.php` in a text editor and add the following line to the end:

`$bp_oembed['whitelist'][] = 'google.com';`

This will whitelist all links from google.com.

== Known issues ==

* array_map() warning is displayed - this is a BuddyPress issue, which is fixed in the latest bleeding version of BuddyPress (http://trac.buddypress.org/changeset/2747)

* When a forum post is deleted, the oEmbed forum post cache in bbPress isn't deleted ([appears to be a bbPress issue](http://bbpress.org/forums/topic/does-deleting-a-forum-post-delete-the-related-bb_meta-as-well))

* Hyperlinks with single quotes get mangled (if you're a regex expert, I could use your help!)

eg. `<a href='http://buddypress.org'>BuddyPress</a>`

* Hyperlinking an oEmbeddable link and inputting the same link in plain text will show the oEmbeddable item three times (two times if using anchor text) (not many people will do this)


== Future versions ==

* Implement AutoEmbed.com API fallback if oEmbed fails (will be an option)
* Fix known issues ;)

== Special thanks ==

* [Viper007Bond](http://www.viper007bond.com/) - for creating the WP_oEmbed class
* [BuddyPress.org](http://buddypress.org) - the reason why we can all deploy a social network on Wordpress

== Donate! ==

I'm a regular on the buddypress.org forums.  I spend a lot of my free time helping people - pro bono!

There are a couple of ways you can choose to support me:

* [Fund my work soundtrack!](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KU38JAZ2DW8TW)  Music helps me help you!  A dollar lets me buy a new tune off Amazon MP3, Amie Street or emusic.com!  Or if you're feeling generous, you can help me buy a whole CD!  If you choose to donate, let me know what songs or which CD you want me to listen to! :)
* Rate this plugin
* Spread the gospel of BuddyPress

== Changelog ==

= 0.52 =
* No more plugin folder renaming! (thanks [apeatling](http://buddypress.org/developers/apeatling/))

= 0.51 =
* Added rename plugin folder instructions (*IMPORTANT*)
* Fixed "cannot modify header information" bug (thanks geoffm33 for reporting)
* Moved default, whitelist items out of config to plugin base
* Added BuddyPress domain to whitelist
* Added instructions to extend oEmbed provider list to readme.txt

= 0.5 =
* First version!