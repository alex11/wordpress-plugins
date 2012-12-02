<?php 
/** 
 * ################################################################################
 * UTILITIES
 * ################################################################################
 */

/**
 * Util function returns an array value, if not defined then returns default instead.
 * @param Array $array
 * @param string $key
 * @param variant $default
 */
function hungryfeed_val($arr,$key,$default='')
{
	return isset($arr[$key]) ? $arr[$key] : $default;
}

/**
 * output a fatal error and optionally die
 * 
 * @param string $message
 * @param string $title
 * @param bool $die
 */
function hungryfeed_fatal($message, $title = "", $die = false)
{
	$html = get_option('hungryfeed_error_template',HUNGRYFEED_DEFAULT_ERROR_TEMPLATE);
	
	echo str_replace("{{error}}", trim($title . " " . $message), $html);

	if ($die) die();
}

/**
 * (function borrowed from cakePHP - original documentation below)
 * 
 * Truncates text.
 *
 * Cuts a string to the length of $length and replaces the last characters
 * with the ending if the text is longer than length.
 *
 * ### Options:
 *
 * - `ending` Will be used as Ending and appended to the trimmed string
 * - `exact` If false, $text will not be cut mid-word
 * - `html` If true, HTML tags would be handled correctly
 *
 * @param string  $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param array $options An array of html attributes and options.
 * @return string Trimmed string.
 * @access public
 * @link http://book.cakephp.org/view/1469/Text#truncate-1625
 */
function hungryfeed_truncate($text, $length = 100, $options = array()) {
	$default = array(
		'ending' => '...', 'exact' => true, 'html' => false
	);
	$options = array_merge($default, $options);
	extract($options);

	if ($html) {
		if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		$totalLength = mb_strlen(strip_tags($ending));
		$openTags = array();
		$truncate = '';

		preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
		foreach ($tags as $tag) {
			if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
				if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
					array_unshift($openTags, $tag[2]);
				} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
					$pos = array_search($closeTag[1], $openTags);
					if ($pos !== false) {
						array_splice($openTags, $pos, 1);
					}
				}
			}
			$truncate .= $tag[1];

			$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
			if ($contentLength + $totalLength > $length) {
				$left = $length - $totalLength;
				$entitiesLength = 0;
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
					foreach ($entities[0] as $entity) {
						if ($entity[1] + 1 - $entitiesLength <= $left) {
							$left--;
							$entitiesLength += mb_strlen($entity[0]);
						} else {
							break;
						}
					}
				}

				$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
				break;
			} else {
				$truncate .= $tag[3];
				$totalLength += $contentLength;
			}
			if ($totalLength >= $length) {
				break;
			}
		}
	} else {
		if (mb_strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = mb_substr($text, 0, $length - mb_strlen($ending));
		}
	}
	if (!$exact) {
		$spacepos = mb_strrpos($truncate, ' ');
		if (isset($spacepos)) {
			if ($html) {
				$bits = mb_substr($truncate, $spacepos);
				preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
				if (!empty($droppedTags)) {
					foreach ($droppedTags as $closingTag) {
						if (!in_array($closingTag[1], $openTags)) {
							array_unshift($openTags, $closingTag[1]);
						}
					}
				}
			}
			$truncate = mb_substr($truncate, 0, $spacepos);
		}
	}
	$truncate .= $ending;

	if ($html) {
		foreach ($openTags as $tag) {
			$truncate .= '</'.$tag.'>';
		}
	}

	return $truncate;
}

/**
 * Used in combination with set_error_handler before reading feeds so that any errors can be caught
 * and displayed in a friendly way without freaking out visitors.  to prevent unecessary warnings
 * this will disregard any errors of type E_STRICT, E_DEPRECATED and E_USER_DEPRECATED
 * 
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function hungryfeed_handle_rss_error($errno, $errstr, $errfile, $errline) 
{
	// don't do anything if error reporting is off (the @ is used)
	if (error_reporting() == 0) return;
	
	// these may not be defined depending on the version of php
	if (!defined('E_STRICT')) define('E_STRICT',2048);
	if (!defined('E_DEPRECATED')) define('E_DEPRECATED',8192);
	if (!defined('E_USER_DEPRECATED')) define('E_USER_DEPRECATED',16384);

	// simplepie causes a few errors unfortunately due to illegal static method calls
	// and unfortunately we wind up catching errors in other plugins as well
	$ignore = $errno & (E_STRICT | E_DEPRECATED | E_USER_DEPRECATED);
	if ($ignore == $errno) return true;
	
	hungryfeed_fatal("Error Processing Feed: " . $errstr . " at " . $errfile . " line " . $errline);
	return true;
}

/**
 * Create a pagination/filter URL, appending the specified querystring.  
 * will prepend a ? or & as needed depending on the current url
 * @param array $querystring example: array('var1'=>'val1','var2'=>'val2')
 * @bool preserve any existing querystring parameters
 * @return string
 */
function hungryfeed_create_url($pairs,$preserve_existing = true)
{
	$uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
	
	// need to strip out an previous value
	list($base,$original_qs) = explode("?",$uri,2);
	
	$new_qs = "";
	$delim = "?";
	
	$params = explode("&",$original_qs);
	
	if ($preserve_existing)
	{
		// add existing params that we need to preserve
		foreach ($params as $param)
		{
			list($key,$val) = explode("=",$param,2);
	
			if (!array_key_exists($key,$pairs))
			{
				$new_qs .= $delim . $param;
				$delim = "&";
			}
		}
	}

	// now add any new params necessary
	foreach ($pairs as $key=>$val)
	{
		$new_qs .= $delim . $key . "=" . $val;
		$delim = "&";
	}
	
	return $base . $new_qs;
}

/**
 * include the simplepie class files and return true if successful.  if not
 * successful then an error message will be displayed and false will be returned.
 */
function hungryfeed_include_simplepie()
{
	if (!class_exists('SimplePie'))
	{
		if (file_exists(ABSPATH . WPINC . '/class-simplepie.php'))
		{
			include_once(ABSPATH . WPINC . '/class-simplepie.php');
		}
		else
		{
			hungryfeed_fatal("Please either upgrade to WordPress 3 or else install the "
				."<a href='http://wordpress.org/extend/plugins/simplepie-core'>SimplePie Core plugin</a> "
				."for WordPress.", "HungryFEED can't find SimplePie.  Don't be mad at HungryFEED.");
				
			return false;
		}
	}
	
	return true;
}

?>