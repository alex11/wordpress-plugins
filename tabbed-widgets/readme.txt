=== Tabbed Widgets ===
Contributors: kasparsd
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=kaspars%40konstruktors%2ecom&item_name=Tabbed%20Widgets%20Plugin%20for%20WordPress&no_shipping=1&no_note=1&tax=0&currency_code=EUR&lc=LV&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: widget, widgets, tabs, tabbed widgets, accordion, sidebar, ui
Requires at least: 2.9
Tested up to: 3.1
Stable tag: 1.3.1

Create tab and accordion type widgets without writing a single line of code.

== Description ==

Tabbed interfaces can save a lot of vertical space and make your website look less cluttered. 
Accordion type tabs are particularly useful if you want to have longer tab titles or more tabs that 
wouldn't otherwise fit into the given horizontal width.

= Features: =

*	Use other widgets for tab content and specify custom tab titles.
*	Make tabs rotate in a set interval, which makes them more noticeable and prominent.
*	Set a random start tab on each page load so that all tabbed content gets equal exposure.
*	Make unlimited number of tabbed widgets that can be then used as regular widgets under ‘Design’ › ‘Widgets’.

= Why are Tabbed Widgets better than your theme's built-in tabs? =

With Tabbed Widgets you can use *any widget* inside the tabbed interface and you are no longer limited to what the theme designer had in mind.

= Tabbed Widget design services =

Tabbed widgets created by this plugin have very little CSS applied by default because every theme is very different.

Therefore, I offer [Tabbed Widget design customization service](http://konstruktors.com/blog/projects-services/wordpress-plugins/tabbed-accordion-widgets/#service).


== Installation ==

1.	Search for "Tabbed Widgets" in ‘Plugins’ › ‘Add New’. Install it.

2.	Under ‘Design’ › ‘Widgets’ drag a new "Tabbed Widget" (from the list of Available Widgets) into a sidebar where you want it to appear.

3.	Widgets that have configuration settings *must* be placed in the 'Invisible Sidebar Area' before they will appear in the drop-down menu.


== Changelog ==

*	**1.3.1** (Jan 24, 2011) -- Revert to local jQuery UI core, widgets, accordion and tabs hosting. Bundle everything in one file.
*	**1.3** (Jan 23, 2011) -- Bug fixes. Use Google CDN hosted jQuery UI. 
*	**1.2** (Jan 23, 2011) -- Bug fixes.
*	**1.1** (Jan 23, 2011) -- Added support for WP 3.1, updated accordion lib, new default CSS for Twenty Ten, simplified base CSS for all other themes, improved performance (use inline JS vars instead of another WP call).
*	**0.9** (Jun 6, 2010) -- CSS fixes for Twenty Ten and WP 3.0 in general.
*	**0.9** (Apr 10, 2010) -- Add IDs to tab links, so that one can style each tab individualy (use background images, for example).
*	**0.84** (Dec 17, 2009) -- List of available widgets was also trying to list inactive widgets, which caused errors on some setups. Now we include only the active widgets (placed inside any of the sidebars).
*	**0.83** (Dec 14, 2009) -- Another fix of javascript variables.
*	**0.82**: Fixed empty javascript variables, updated readme.txt and faq.
*	**0.81**: Added support for 2.8+; Updated Javascript, simplified interface.
*	**0.76** and **0.77**: Bug fix: Selected start tab was not opened for accordion type widgets.
*	**0.74**: Fixed active widget list creation error for PHP 4.3.6 users. It turned out that $this->tabbed_widget_content doesn't get passed around from class init to child functions. Could be a WP issue as well.
*	**0.73**: Bug fix: removed the extra ob_end_clean which was clearing the widget titles from the settings page drop-down selection.
*	**0.72**: Bug fix: recursion error for PHP 5.2 users due to non-strict `==` comparison in `wp-includes/widgets.php` line 266. Stripped self from the array of active widgets.
*	**0.71**: Bug fix: content under 'Edit' section of dashboard was disappearing due to widget titles being ob_started too early.
*	**0.7**: New feature: choose any start tab. Improved widget drop-down selection with exact widget titles. Added an invisible sidebar (widgetized area) for placing and configuring widgets that are going to be used inside the tabbed widgets. Adding automatic rotation stop also for regulat type tabs.
*	**0.2**: New feature: if a user clicks on a link inside a tab, that tab will be automatically set to open on the next page load. This is a significant usability improvement.
*	**0.1x**: Various bug fixes.
*	**0.1**: Initial public release.

== Upgrade Notice ==

= 1.3.1 =
Revert to local jQuery UI hosting. Includes jQuery UI Widgets, which was missing in 1.3.

= 1.3 =
Bug fixes. Use Google CDN hosted jQuery UI.

= 1.1 =
Added support for WP 3.1 and improved performance.

= 0.9 =
Fixes CSS for Twenty Ten and WordPress 3.0 in general.


== Frequently Asked Questions ==

Post your questions in [WordPress support forum](http://wordpress.org/tags/tabbed-widgets?forum_id=10).

= Widget X doesn't appear in the drop-down selection =

It is most likely that the widget must be configured before it can be used -- 
place it in the 'Invisible Sidebar Area', refresh the Widget 
admin page and it should appear in the drop-down selection.


== Screenshots ==

1. Tab layout on Twenty Ten
2. Accordion layout on Twenty Ten
3. Tabbed Widgets settings
