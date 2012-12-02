<?php
/* IMPORTANT: edit settings in bp-oembed-config.php - no need to edit this file */
global $bp_oembed;

require_once(dirname( __FILE__ ) . '/bp-oembed-config.php');

// oembed for activity updates
if($bp_oembed['activity_updates'])
	add_filter('bp_get_activity_content_body','ray_bp_oembed', 9);

// oembed for activity comments
if($bp_oembed['activity_comments'])
	add_filter('bp_get_activity_content','ray_bp_oembed', 9);

// oembed for forum posts
if($bp_oembed['forum_posts'])
	add_filter('bp_get_the_topic_post_content','ray_bp_oembed', 9);

// whitelist hyperlinks
$bp_oembed['whitelist'][] = '<a ';
$bp_oembed['whitelist'][] = '">';
$bp_oembed['whitelist'][] = '<a>';

// whitelist BP domain
$bp_oembed['whitelist'][] = parse_url(get_bloginfo('wpurl'), PHP_URL_HOST);

/* really stop editing! */

function ray_bp_oembed($content) {
	global $bp_oembed;

	// WP(MU) 2.9 oEmbed check
	if(!function_exists(wp_oembed_get))
		return $content;

	// match URLs - could use some work
//	preg_match_all( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $content, $matches );
	preg_match_all('`.*?((http|https)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i', $content, $matches);

	// debug regex
	// print_r($matches[0]);

	// if there are no links to parse, return $content now!
	if(empty($matches[0]))
		return $content;

	$whitelist = $bp_oembed['whitelist'];

	for($i=0;$i<count($matches[0]);$i++) {
		$url = $matches[0][$i];

		// check url with whitelist, if url matches any whitelist item, skip from parsing
		foreach ($whitelist as $whitelist_item) {
			if (strpos($url,$whitelist_item) !== false) { 
				continue 2;
			}
		}

		$cachekey = '_oembed_' . md5($url);

		// grab oEmbed cache depending on BP component
		// not pretty! only looking for activity updates or forum posts ATM
		if(!bp_get_activity_id() && bp_forums_is_installed_correctly()) {
			$cache = bb_get_postmeta(bp_get_the_topic_post_id(), $cachekey);
		}
		else {
			$cache = bp_activity_get_meta( bp_get_activity_id(), $cachekey);		
		}

		// cache check - no oEmbed, but cached result, skip rest of loop
		if ( $url === $cache ) {
			continue;
		}

		// cache check - yes oEmbed
		if ( !empty($cache) ) {
			$replace = apply_filters( 'embed_oembed_html', $cache, $url, $attr );
		}
		// if no cache, let's start the show!
		else {
			// process url to oEmbed
			$oembed = wp_oembed_get($url); // returns true if link is oEmbed
			//$oembed = file_get_contents("http://autoembed.com/api/?url=".urlencode($url));

			if ($oembed) {
				$replace = apply_filters( 'embed_oembed_html', $oembed, $url, $attr );
				$replace = str_replace('
','',$replace); // fix Viddler line break in <object> tag
			}
			else {
				$replace = $url;
				// unlike WP's oEmbed, I cache the URL if not oEmbed-dable!
				// the URL is more useful in the DB than a string called {{unknown}} ;)
			}

			// save oEmbed cache depending on BP component
			// the same "not prettiness!"
			if(!bp_get_activity_id() && bp_forums_is_installed_correctly())
				bb_update_postmeta(bp_get_the_topic_post_id(), $cachekey, $replace);
			else
				bp_activity_update_meta( bp_get_activity_id(), $cachekey, $replace );
		}

		$content = str_replace($url, $replace, $content);
	}

	return $content;
}
?>