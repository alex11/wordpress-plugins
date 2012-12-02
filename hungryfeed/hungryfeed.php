<?php
/*
Plugin Name: HungryFEED
Plugin URI: http://verysimple.com/products/hungryfeed/
Description: HungryFEED displays RSS feeds on a page or post using Shortcodes.	Respect!
Version: 1.6.0
Author: VerySimple
Author URI: http://verysimple.com/
License: GPL2
*/

define('HUNGRYFEED_VERSION','1.6.0');
define('HUNGRYFEED_DEFAULT_CACHE_DURATION',3600);
define('HUNGRYFEED_DEFAULT_CSS',"h3.hungryfeed_feed_title {}\np.hungryfeed_feed_description {}\ndiv.hungryfeed_items {}\ndiv.hungryfeed_item {margin-bottom: 10px;}\ndiv.hungryfeed_item_title {font-weight: bold;}\ndiv.hungryfeed_item_description {}\ndiv.hungryfeed_item_author {}\ndiv.hungryfeed_item_date {}");
define('HUNGRYFEED_DEFAULT_JS',"<script type=\"text/javascript\">\n// Custom Javascript here...\n</script>");
define('HUNGRYFEED_DEFAULT_HTML',"<div class=\"hungryfeed_item\">\n<h3><a href=\"{{permalink}}\">{{title}}</a></h3>\n<div>{{description}}</div>\n<div>Author: {{author}}</div>\n<div>Posted: {{post_date}}</div>\n</div>");
define('HUNGRYFEED_DEFAULT_ERROR_TEMPLATE',"<div style=\"margin:5px 0px 5px 0px;padding:10px;border: solid 1px red; background-color: #ff6666; color: black;\">\n{{error}}\n</div>");
define('HUNGRYFEED_DEFAULT_CACHE_LOCATION',ABSPATH . 'wp-content/cache');
define('HUNGRYFEED_DEFAULT_FEED_FIELDS','title,description');
define('HUNGRYFEED_DEFAULT_ITEM_FIELDS','title,description,author,date');
define('HUNGRYFEED_DEFAULT_LINK_ITEM_TITLE',1);
define('HUNGRYFEED_DEFAULT_ENABLE_WIDGET_SHORTCODES',0);
define('HUNGRYFEED_DEFAULT_ENABLE_TEMPLATE_SHORTCODES',0);
define('HUNGRYFEED_DEFAULT_ENABLE_EDITOR_BUTTON',1);
define('HUNGRYFEED_DEFAULT_DATE_FORMAT','F j, Y, g:i a');

$HUNGRYFEED_BAD_DATA_CHARS = array('#','&;','`','|','*','?','<','>','^','(',')','{','}','$','\',',',', "\x0A", "\xFF");

/**
 * import supporting libraries
 */
include_once(plugin_dir_path(__FILE__).'settings.php');
include_once(plugin_dir_path(__FILE__).'libs/utils.php');

add_shortcode('hungryfeed', 'hungryfeed_display_rss');
add_filter('query_vars', 'hungryfeed_queryvars' );

// only enable widget shortcode processing if specified in the settings
if (get_option('hungryfeed_enable_widget_shortcodes',HUNGRYFEED_DEFAULT_ENABLE_WIDGET_SHORTCODES))
{
	// saw this recommended but is it still necessary in current versions?
	// if (function_exists('shortcode_unautop')) add_filter('widget_text', 'shortcode_unautop');

	add_filter('widget_text', 'do_shortcode' );  // tell wordpress to look for shortcodes in widget text

	/* these probably should never be enabled, perhaps in the case of a private site...? */
	//add_filter( 'comment_text', 'shortcode_unautop');
	//add_filter( 'comment_text', 'do_shortcode' );
	//add_filter( 'the_excerpt', 'shortcode_unautop');
	//add_filter( 'the_excerpt', 'do_shortcode');
}

// handle any post-render intialization
add_action('init', 'hungryfeed_init');


/**
 * Fired on initialization.  Allows initialization to occur after page render.
 * Currently this is used only to register the MCE editor button
 */
function hungryfeed_init()
{

	// register the MCE editor plugin if necessary
	if ( current_user_can('edit_posts') || current_user_can('edit_pages') )
	{
		if ( get_user_option('rich_editing') == 'true' && get_option('hungryfeed_enable_editor_button',HUNGRYFEED_DEFAULT_ENABLE_EDITOR_BUTTON))
		{
			add_filter("mce_external_plugins", "hungryfeed_register_mce_plugin");
			add_filter('mce_buttons', 'hungryfeed_register_mce_buttons');
		}
	}
}

/**
 * Register the HungryFEED MCE Editor Plugin
 * @param array $plugin_array
 * @return array
 */
function hungryfeed_register_mce_plugin($plugin_array)
{
	$plugin_array['hungryfeed'] = plugins_url('/hungryfeed/scripts/editor_plugin.js');
	return $plugin_array;
}

/**
 * Add the HungryFEED button to the MCE Editor
 * @param array $buttons
 * @return array
 */
function hungryfeed_register_mce_buttons($buttons)
{
	array_push($buttons, "hungryfeedButton");
	return $buttons;
}

/**
 * Displays the RSS feed on the page
 * @param unknown_type $params
 */
function hungryfeed_display_rss($params)
{
	// if simplepie isn't installed then we can't continue
	if (!hungryfeed_include_simplepie()) return "";

	// read in all the possible shortcode parameters
	$url = hungryfeed_val($params,'url','http://verysimple.com/feed/');
	$force_feed = hungryfeed_val($params,'force_feed','0');
	$xml_dump = hungryfeed_val($params,'xml_dump','0');
	$show_data = hungryfeed_val($params,'show_data','0');
	$decode_url = hungryfeed_val($params,'decode_url','1');
	$max_items = hungryfeed_val($params,'max_items',0);
	$template_id = hungryfeed_val($params,'template',0);
	$date_format = hungryfeed_val($params,'date_format',HUNGRYFEED_DEFAULT_DATE_FORMAT);
	$allowed_tags = hungryfeed_val($params,'allowed_tags','');
	$strip_ellipsis = hungryfeed_val($params,'strip_ellipsis',0);
	$filter = hungryfeed_val($params,'filter','');
	$filter_out = hungryfeed_val($params,'filter_out','');
	$link_target = hungryfeed_val($params,'link_target','');
	$page_size = hungryfeed_val($params,'page_size',0);
	$order = hungryfeed_val($params,'order','');
	$truncate_description = hungryfeed_val($params,'truncate_description',0);

	$feed_fields = explode(",", hungryfeed_val($params,'feed_fields',HUNGRYFEED_DEFAULT_FEED_FIELDS));
	$item_fields = explode(",", hungryfeed_val($params,'item_fields',HUNGRYFEED_DEFAULT_ITEM_FIELDS));
	$link_item_title = hungryfeed_val($params,'link_item_title',HUNGRYFEED_DEFAULT_LINK_ITEM_TITLE);


	// fix weirdness in the url due to the wordpress visual editor
	if ($decode_url) $url = html_entity_decode($url);

	// the target code for any links in the feed
	$target_code = ($link_target) ? "target='$link_target'" : "";

	// buffer the output.
	ob_start();

	// output the custom css and javascript
	echo "<style>\n" .  get_option('hungryfeed_css',HUNGRYFEED_DEFAULT_CSS) . "\n</style>\n";
	echo get_option('hungryfeed_js',HUNGRYFEED_DEFAULT_JS) . "\n";

	// catch any errors that simplepie throws
	set_error_handler('hungryfeed_handle_rss_error');
	$feed = new SimplePie();

	// instruct simplepie not to bother sorting for certain sort types
	if ($order == "none" || $order == "random") $feed->enable_order_by_date(false);

	$cache_duration = get_option('hungryfeed_cache_duration',HUNGRYFEED_DEFAULT_CACHE_DURATION);
	if ($cache_duration)
	{
		$feed->enable_cache(true);
		$feed->set_cache_duration($cache_duration);
		$feed->set_cache_location(HUNGRYFEED_DEFAULT_CACHE_LOCATION);
	}
	else
	{
		$feed->enable_cache(false);
	}

	$feed->set_feed_url($url);

	// @HACK: SimplePie adds this weird shit into eBay feeds
	$feed->feed_url = str_replace("%23038;","",$feed->feed_url);

	if ($force_feed) $feed->force_feed(true);

	if (!$feed->init())
	{
		hungryfeed_fatal("SimplePie reported: " . $feed->error,"HungryFEED can't get feed.  Don't be mad at HungryFEED.");

		if ($xml_dump)
		{
			// this will cause messed up output since simplepie outputs xml headers
			// but there seems to be no other way to get the raw xml back out for debuggin

			echo "\n\n\n<!-- BEGIN DEBUG OUTPUT FROM FEED at $feed->feed_url -->\n\n\n";

			$feed->xml_dump = true;
			$feed->init();

			echo "\n\n\n<!-- END DEBUG OUTPUT FROM FEED -->\n\n\n";

		}

		$buffer = ob_get_clean();
		return $buffer;
	}

	// restore the normal wordpress error handling
	restore_error_handler();

	if (in_array("title",$feed_fields)) echo '<h3 class="hungryfeed_feed_title">' . $feed->get_title() . "</h3>\n";
	if (in_array("description",$feed_fields)) echo '<p class="hungryfeed_feed_description">' . $feed->get_description() . "</p>\n";

	echo "<div class=\"hungryfeed_items\">\n";

	$counter = 0;
	$template_html = "";

	if ($template_id == "1" || $template_id == "2" || $template_id == "3")
	{
		$template_html = get_option('hungryfeed_html_'.$template_id,HUNGRYFEED_DEFAULT_HTML);
	}

	$allowed_tags = $allowed_tags
		? ('<' . implode('><',explode(",",$allowed_tags)) . '>')
		: '';

	$items = $feed->get_items();

	if ($order == "reverse")
	{
		$items = array_reverse($items);
	}
	else if ($order == "random")
	{
		shuffle($items);
	}

	$pages = array();
	$page_num = 1;

	if ($page_size)
	{
		// array chunk used for pagination
		$pages = array_chunk($items, $page_size);

		// grab the requested page from the querystring, make sure it's legit
		global $wp_query;
		if (isset($wp_query->query_vars['hf_page']))  $page_num = $wp_query->query_vars['hf_page'];
		if (is_numeric($page_num) == false || $page_num < 1 || $page_num > count($pages)  ) $page_num = 1;
	}
	else
	{
		$pages[] = $items;
		$page_num = 1;
	}

	$num_pages = count($pages);

	// filters is a pip-delimited value
	$filters = $filter ? explode("|",$filter) : array();
	$filters_out = $filter_out ? explode("|",$filter_out) : array();

	$item_index = ($page_num-1) * $page_size;

	foreach ($pages[$page_num-1] as $item)
	{
		// flatten the author into a string
		$author = $item->get_author();
		$author_name = ($author ? $author->get_name() : '');

		$title = $item->get_title();
		$description = $item->get_description();

			// if any filters were specified, then only show the feed items that contain the filter text
		if (count($filters))
		{
			$match = false;
			$item_will_be_included = false;

			foreach($filters as $f)
			{
				if (stripos($description,$f) !== false || stripos($title,$f) !== false)
				{
					$match = true;
					break;
				}
			}

			if (!$match)
			{
				// didn't match the filter, exit the foreach loop
				continue;
			}
		}


		// if any filters were specified, then only show the feed items that contain the filter text
		if (count($filters_out))
		{
			$match = false;
			foreach($filters_out as $fo)
			{
				if (stripos($description,$fo) !== false || stripos($title,$fo) !== false)
				{
					$match = true;
					break;
				}
			}

			if ($match)
			{
				// did match the filter_out, exit the foreach loop
				continue;
			}
		}

		// if we made it this far then the item will be included in the output
		$counter++;

		if ($allowed_tags) $description = strip_tags($description,$allowed_tags);

		if ($truncate_description) $description = hungryfeed_truncate($description,$truncate_description, array('ending' => '...', 'exact' => true, 'html' => true) );

		if ($strip_ellipsis) $description = str_replace(array('[...]','...'),array('',''),$description);

		if ($target_code) $description = str_replace('<a ','<a '.$target_code.' ',$description);

		if ($max_items > 0 && $counter > $max_items) break;

			// either use a template, or the default layout
		if ($template_html)
		{
			// flatten these
			$enclosure = $item->get_enclosure();
			$enclosure_link = $enclosure ? $enclosure->get_link() : "";

			$category_label = "";
			$cdelim = "";
			$categories = $item->get_categories();
			if ($categories)
			{
				foreach ($categories as $category)
				{
					$category_label .= $cdelim . $category->get_label();
					$cdelim = ", ";
				}
			}

			$source = $item->get_source();
			$source_title = $source ? $source->get_title() : "";
			$source_permalink = $source ? $source->get_permalink() : "";

			// for some reason simplepie doesn't always get the source
			if (!$source)
			{
				try	{
					// TODO: why doens't try/catch suppress notice here?
					$source_title = @$item->data['child']['']['source'][0]['data'];
					$source_permalink = @$item->data['child']['']['source'][0]['attribs']['url'];
				} catch (Exception $ex) {}
			}

			$item_index++;

			$item_values = array(
				'index' => $item_index,
				'index_'. $item_index => true,
				'id' => $item->get_id(),
				'feed_title' => $feed->get_title(),
				'feed_description' => $feed->get_description(),
				'permalink' => $item->get_permalink(),
				'title' => $title,
				'description' => $description,
				'author' => $author_name,
				'post_date' => $item->get_date($date_format),
				'source_title' => $source_title,
				'source_permalink' => $source_permalink,
				'latitude' => $item->get_latitude(),
				'longitude' => $item->get_longitude(),
				'category' => $category_label,
				'enclosure' => $enclosure_link,
				'data' => $item->data
			);

			// allow pass-through variables from the shortcode
			$rss_values = array_merge($params,$item_values);


			echo hungryfeed_merge_template($template_html,$rss_values);
		}
		else
		{
			echo "<div class=\"hungryfeed_item\">\n";
				if (in_array("title",$item_fields))
					echo $link_item_title
						? '<div class="hungryfeed_item_title"><a href="' . $item->get_permalink() . '" '. $target_code .'>' . $title . "</a></div>\n"
						: '<div class="hungryfeed_item_title">' . $title . '</div>';
				if (in_array("description",$item_fields))
					echo '<div class="hungryfeed_item_description">' . $description . "</div>\n";
				if ($author_name && in_array("author",$item_fields))
					echo '<div class="hungryfeed_item_author">Author: ' . $author_name . "</div>\n";
				if ($item->get_date() && in_array("date",$item_fields))
					echo '<div class="hungryfeed_item_date">Posted: ' . $item->get_date($date_format) . "</div>\n";
			echo "</div>\n";
		}

		if ($show_data)
		{
			echo "<div class='hungryfeed_item_data'><textarea style='width: 400px; height: 100px;'>"
				. (print_r($item->data,1))
				. "</textarea></div>";
		}
	}

	echo "</div>\n";


	if ($page_size)
	{
		echo "<p class=\"hungryfeed_pagenav\"><span>Viewing page $page_num of $num_pages</span>";

		if ($page_num > 1) echo "<span>|</span><span><a href=\"". hungryfeed_create_url(array("hf_page" => $page_num - 1)) . "\">Previous Page</a></span>";
		if ($page_num < $num_pages) echo "<span>|<span><a href=\"". hungryfeed_create_url(array("hf_page" => $page_num + 1)) . "\">Next Page</a></span>";

		echo "</p>";
	}

	// flush the buffer and return
	$buffer = ob_get_clean();
	return $buffer;
}


/** @var array private var used by hungryfeed_parse_dom_query */
$hungryfeed_merge_template_documents = array();

/**
 * Returns the results of the dom query using phpquery
 * @link http://code.google.com/p/phpquery/
 * @param string $html to parse
 * @param string $selector query
 * @return string
 */
function hungryfeed_parse_dom_query($html, $selector, $method = "text", $attr = "")
{
	// only include phpQuery if selectors are used so it isn't unecessarily loaded
	include_once(plugin_dir_path(__FILE__).'libs/phpQuery-onefile.php');

	global $hungryfeed_merge_template_documents;

	// cache this because it is expensive and we may have multiple selectors for one template
	if ( !array_key_exists($html,$hungryfeed_merge_template_documents) )
	{
		$hungryfeed_merge_template_documents[$html] = phpQuery::newDocument($html);
	}

	$pq = $hungryfeed_merge_template_documents[$html];
	$result = phpQuery::pq($selector, $pq->documentID);

	switch ($method)
	{
		case "html":
			$output = $result->html();
			break;
		case "text":
			$output = $result->text();
			break;
		case "attr":
			$output = $result->attr($attr);
			break;
	}

	// phpQuery::unloadDocuments();

	return $output;
}

/** @var array private var used by hungryfeed_merge_template */
$hungryfeed_merge_template_values = null;

/**
 * Replaces
 * @param string template
 * @param array key/value pair
 */
function hungryfeed_merge_template($template, $values)
{
	// first look for any of the select or data tags
	global $hungryfeed_merge_template_values;
	$hungryfeed_merge_template_values = $values;

	// this regex looks for "select" tags
	$template = preg_replace_callback('!{{select(([^}])+)}}!', 'hungryfeed_merge_select_template_callback', $template);

	// this regex looks for "data" tags
	$template = preg_replace_callback('!{{data(([^}])+)}}!', 'hungryfeed_merge_data_template_callback', $template);

	// mustache handles the rest
	include_once(plugin_dir_path(__FILE__).'libs/Mustache.php');

	// we have to pass in this PRAGMA option so that html is not escaped
	// @TODO this is marked in Mustache as experimental. optionally use html_entity_decode instead
	$options = array( 'pragmas'=> array(Mustache::PRAGMA_UNESCAPED=>true) );

	$m = new Mustache( $template, $values, null, $options);
	$result = $m->render();

	if (get_option('hungryfeed_enable_template_shortcodes',HUNGRYFEED_DEFAULT_ENABLE_TEMPLATE_SHORTCODES))
	{
		$result = do_shortcode($result);
	}

	return $result;

}

/**
 * process the "select" tags
 * @param array $matches
 */
function hungryfeed_merge_select_template_callback($matches)
{
	global $hungryfeed_merge_template_values;

	$key = "select" . $matches[1];
	// echo "<div>called for ".$key."<div/>";

	if (substr($key,0,13) == 'select(html).' || substr($key,0,13) == 'select(text).')
	{
		// this is a dom query of the description field
		$value = hungryfeed_parse_dom_query(
			$hungryfeed_merge_template_values['description'],
			substr($key,13),
			substr($key,7,4)
		);
	}
	elseif (substr($key,0,12) == 'select(attr:')
	{
		$endpos = strpos($key,")");

		$value = hungryfeed_parse_dom_query(
			$hungryfeed_merge_template_values['description'],
			substr($key,$endpos+2),
			"attr",
			substr($key,12,$endpos-12)
		);

	}

	return $value;
}

/**
 * process the "data" tags
 * @param array $matches
 */
function hungryfeed_merge_data_template_callback($matches)
{
	global $hungryfeed_merge_template_values;

	$key = "data" . $matches[1];

	// we are expecting a value in the format data['child']['http://test.com']['somevar']['0']['data']
	$data = $hungryfeed_merge_template_values['data'];

	global $HUNGRYFEED_BAD_DATA_CHARS;

	$safeKey = str_replace($HUNGRYFEED_BAD_DATA_CHARS,'',$key);

	if ($safeKey == $key)
	{
		$varname = '$' . $safeKey;
		$value = eval("return $varname;");
	}
	else
	{
		$value = 'data expression contains illegal characters';
	}

	return $value;
}

/**
 * registration for queryvars used by hungryfeed.  this registers any
 * querystring variables that hungryfeed requires so that wordpress will
 * process them
 *
 * @param array original array of allowed wordpress query vars
 * @return array $qvars with extra allowed vars added to the array
 */
function hungryfeed_queryvars( $qvars )
{
	$qvars[] = 'hf_page';  // used by pagination
 	return $qvars;
}
