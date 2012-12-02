<?php
/*
Plugin Name: TDO Tags Fixes
Plugin URI: http://thedeadone.net/software/tdo-tag-fixes-wordpress-plugin/
Description: Some fixes and extensions for Wordpress tags such as a tag cloud using only tags found in a specific category amd also tag and category intersection
Version: 0.5
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

// Based on information from...
// http://boren.nu/archives/2007/10/01/taxonomy-intersections-and-unions/

// You can change this if you want. Do not set it to 'tag'. If you want to
// disable this feature, just comment it out.
//
global $tdotf_tag_get_var;
$tdotf_tag_get_var = 'tdo_tag';

// If you have fancy permalinks turned on, you can use this option to use a
// more fancy form of the tdo_tag. The first paramater is the category_slug
// and the second parameter is the tag_slug. If you want to disable this feature
// just comment it out. (It depends on $tdotf_tag_get_var)
//
global $tdotf_fancy_tag_regex;
$tdotf_fancy_tag_regex = 'tdo_cat/(.+)/tdo_tag/(.+)';

// Set to false if you do not wish tag archive titles to be correctly updated
//
global $tdotf_fix_tag_title_auto;
$tdotf_fix_tag_title_auto = true;

// Set to false if you do not wish the built-in tag cloud automatically fixed
//
global $tdotf_fix_tag_cloud_auto;
$tdotf_fix_tag_cloud_auto = true;

// Set to false if you do not wish the category archive title to include tag filter
//
global $tdotf_fix_cat_title_auto;
$tdotf_fix_cat_title_auto = true;

// Enable some debug
//
global $tdotf_enable_debug;
$tdotf_enable_debug = false;

// Do not change anything under this line unless you know what you are doing!
/////////////////////////////////////////////////////////////////////////////

// @TODO: An admin screen
// @TODO: Recursive links in Tag cloud: i.e. go to a category and click on a tag you
//        have a archive with category and tag. Now the tag cloud should only show tags
//        for posts in that archive. If you click on a tag then, it'll take you to a
//        page with posts from that category that are tagged with both tags and so on
//        and so on.
// @TODO: Use fancy permalinks in tdotf_tags and cloud if permalinks enabled (need a
//        more elaborate configuration)

if(isset($tdotf_tag_get_var)) {
  function tdotf_tag_cat_intersect($args)
  {
    global $tdotf_tag_get_var;

    // using "category__and" breaks the query for some reason
    //
    //if(isset($_GET['tdo_cat'])) {
    //  $args->set_query_var('category__and',array($_GET['tdotf_cat']));
    //}

    $tag_query = tdotf_get_tdo_tag_query();

    if(!empty($tag_query) && $tag_query != false) {
       if(strpos($tag_query,',') != FALSE) {
          $tags = split(",",$tag_query);
       } else if(strpos($tag_query,' ') != FALSE) {
          $tags = split(" ",$tag_query);
       } else {
          $tags = array($tag_query);
       }
       $args->set_query_var('tag_slug__and',$tags);
    }
  }
  add_action('parse_request','tdotf_tag_cat_intersect');

  // Amazingly tag OR functionality doesnt work when you include a category. It always treats it like an AND.
  // This hack mods the SQL query to treat the tag1,tag2 intersection as a OR intersection.
  //
  // Perhaps this same method could be extended to do multiple categories and tags AND/OR intersections
  //
  function tdotf_query_filter($where) {
      global $tdotf_tag_get_var,$wpdb,$wp_query;
      $tag_query = tdotf_get_tdo_tag_query();
      if(!empty($tag_query) && $tag_query != false && strpos($tag_query,',')) {
        $tags = split(",",$tag_query);
        $cat =  get_query_var('cat');

        // Grab all posts with these tags
        $sql = "SELECT p.ID FROM $wpdb->posts p INNER JOIN $wpdb->term_relationships tr ON (p.ID = tr.object_id) INNER JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) INNER JOIN $wpdb->terms t ON (tt.term_id = t.term_id)";
        $sql .= " WHERE tt.taxonomy = 'post_tag' AND t.slug IN ('" . implode("', '", $tags) . "')";
        $sql .= " GROUP BY p.ID";
        $post_ids = $wpdb->get_col($sql);

        if(count($post_ids) > 0 ) {

            // Pattern and replacement for query
            $patterns = array( '/'."AND.*$wpdb->posts\.ID.*IN.*(.*)".'/', // if there are some posts in the query, you'll get this
                               '/'."AND.*0.*=.*1".'/' );                  // if no posts in the unmodified query, you'll get this

            // Must add category filter and list of tagged posts
            $replacement = "AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->term_taxonomy.term_id IN ('$cat') ";
            $replacement .= "AND $wpdb->posts.ID IN (" . implode(', ', $post_ids) . ")";

            // Update query
            $where = preg_replace($patterns, $replacement, $where);
        }
      }
      return $where;
  }
  add_filter('posts_where_request','tdotf_query_filter');

  function tdotf_get_tdo_tag_query() {
    global $tdotf_tag_get_var;
    if(isset($_GET[$tdotf_tag_get_var])) {
      return $_GET[$tdotf_tag_get_var];
    } else {
      global $wp;
      if(strpos($wp->matched_query,$tdotf_tag_get_var) !== FALSE) {
        // so your using the fancy method huh?
        // I'm sure there is a better way to do this!
        $params = explode('&',$wp->matched_query);
        foreach($params as $param) {
          $param = explode('=',$param);
          if($param[0] == $tdotf_tag_get_var) {
            return $param[1];
          }
        }
      }
    }
    return false;
  }
}

if( $tdotf_fix_tag_title_auto ) {
  function tdotf_tag_title_filter($tag_name) {
     return tdotf_get_tag_title();
  }
  add_filter('single_tag_title','tdotf_tag_title_filter');
}

function tdotf_tag_title() {
   echo tdotf_get_tag_title();
}

function tdotf_get_tag_title() {
  global $tdotf_tag_get_var;

  $tag = "";

  if(isset($tdotf_tag_get_var) && function_exists('tdotf_get_tdo_tag_query')) {
     $tag = tdotf_get_tdo_tag_query();
  }

  if( ($tag == false || empty($tag)) && is_tag() ) {
     $tag = get_query_var('tag');
  }

  if(!empty($tag) && $tag != false) {
     return tdotf_create_tag_title($tag);
  }

  return "";
}

function tdotf_create_tag_title($tags) {
  $mode_and = false;
  $mode_or = false;
  if(strpos($tags,",") !== FALSE) {
    $tags_in_use = split(",",$tags);
    $mode_or = true;
  } else if(strpos($tags,"+") !== FALSE) {
    $tags_in_use = split("+",$tags);
    $mode_and = true;
  } else if(strpos($tags," ") !== FALSE) {
    $tags_in_use = split(" ",$tags);
    $mode_and = true;
  } else {
    $tags_in_use = array($tags);
  }
  $tags_title = "";
  $count = 0;
  foreach($tags_in_use as $tag_in_use) {
    $tag_obj = get_term_by('slug', $tag_in_use, 'post_tag', OBJECT, 'raw');
    if ( is_wp_error( $tag_obj ) ) {
      $tags_title .= $tag_in_use;
    } else {
      $tags_title .= $tag_obj->name;
    }
    $count++;
    if($count < count($tags_in_use)-1) {
      $tags_title .= ", ";
    } else if($count < count($tags_in_use)) {
      if($mode_or) {
        $tags_title .= " or ";
      } else {
        $tags_title .= " and ";
      }
    }
  }
  return $tags_title;
}

function tdotf_cat_tag_cloud( $args = '') {
  global $tdotf_enable_debug;
  $cat = 0;
  if(is_category()) {
    $cat = get_query_var("cat");
  }
  $defaults = array(
      'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
      'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC',
      'exclude' => '', 'include' => '', 'cat' => $cat
  );
  $args = wp_parse_args( $args, $defaults );
  if($args['cat'] == 0) {
    if($tdotf_enable_debug) { echo "<!-- category is not set: using wp_tag_cloud -->\n"; }
    return wp_tag_cloud($args);
  }

  $key = 'tdotf_cat_tag_cloud_'.$args['cat'];
  $tags = wp_cache_get($key);
  if($tags == false) {
    if($tdotf_enable_debug) { echo "<!-- No tags in cache, generating tag list for cat $cat -->\n"; }
    $tags = array();
    $my_query = new WP_Query("cat=".$args['cat']."&nopaging=true");
    if($my_query->have_posts()) {
      $posts = $my_query->get_posts();
      foreach($posts as $post) {
        $post_tags = wp_get_post_tags($post->ID);
        if(!empty($post_tags)) {
          foreach($post_tags as $post_tag) {
            if(isset($tags[$post_tag->term_id])){
              $tags[$post_tag->term_id]->count++;
            } else {
              $tags[$post_tag->term_id] = $post_tag;
              $tags[$post_tag->term_id]->count = 1;
            }
          }
        }
      }
    }
    wp_cache_add($key,$tags);
  } else if($tdotf_enable_debug) { echo "<!-- Using tags from cache -->\n"; }

  if($tdotf_enable_debug) { echo "<!-- Got ".count($tags)." -->\n"; }

  // filter by include or exclude

  if(!empty($args['exclude']) || !empty($args['include'])) {
     $filtered_tags = array();
     if(!empty($args['exclude'])) {
       if($tdotf_enable_debug) { echo "<!-- Found Exclude List: ". $args['exclude'] . " -->\n"; }
       $exclude_list = explode(',',$args['exclude']);
       foreach($tags as $tag) {
         if(!in_array($tag->term_id,$exclude_list)) {
           $filtered_tags [] = $tag;
         }
       }
     } else if(!empty($args['include'])) {
       if($tdotf_enable_debug) { echo "<!-- Found Include List: ". $args['include'] . " -->\n"; }
       $include_list = explode(',',$args['include']);
       foreach($tags as $tag) {
         if(in_array($tag->term_id,$include_list)) {
           $filtered_tags [] = $tag;
         }
       }
     }
     $tags = $filtered_tags;
     if($tdotf_enable_debug) { echo "<!-- After filtering we have ". count($tags) . " tags -->\n"; }
  }


  $return = tdotf_generate_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args
  if ( is_wp_error( $return ) )
    return false;
  else
    echo apply_filters( 'wp_tag_cloud', $return, $args );
}

function tdotf_generate_tag_cloud( $tags, $args = '' ) {
	global $wp_rewrite,$tdotf_tag_get_var,$tdotf_fix_tag_cloud_auto,$tdotf_enable_debug;
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC', 'cat' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args);

	if ( !$tags )
		return;
	$counts = $tag_links = array();
	foreach ( (array) $tags as $tag ) {
		$counts[$tag->name] = $tag->count;
    if($args['cat'] == 0 || !isset($tdotf_tag_get_var)) {
      $tag_links[$tag->name] = get_tag_link( $tag->term_id );
    } else {
      $cat_link = get_category_link( $args['cat'] );
      if(strpos($cat_link,"?") != FALSE) {
        $tag_links[$tag->name] = $cat_link."&".$tdotf_tag_get_var."=".$tag->slug;
      } else {
        $tag_links[$tag->name] = $cat_link."?".$tdotf_tag_get_var."=".$tag->slug;
      }
    }
		if ( is_wp_error( $tag_links[$tag->name] ) )
			return $tag_links[$tag->name];
		$tag_ids[$tag->name] = $tag->term_id;
	}

	$min_count = min($counts);
	$spread = max($counts) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread <= 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;

	// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
	if ( 'name' == $orderby )
		uksort($counts, 'strnatcasecmp');
	else
		asort($counts);

	if ( 'DESC' == $order )
		$counts = array_reverse( $counts, true );

  if($number != 0) {
    if($tdotf_enable_debug) { echo "<!-- number is set to $number so limiting tag cloud -->\n"; }
    $counts = array_splice($counts,0,$number);
  }

	$a = array();

	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? ' rel="tag"' : '';

	foreach ( $counts as $tag => $count ) {
		$tag_id = $tag_ids[$tag];
		$tag_link = clean_url($tag_links[$tag]);
		$tag = str_replace(' ', '&nbsp;', wp_specialchars( $tag ));
		$a[] = "<a href='$tag_link' class='tag-link-$tag_id' title='" . attribute_escape( sprintf( __('%d topics'), $count ) ) . "'$rel style='font-size: " .
			( $smallest + ( ( $count - $min_count ) * $font_step ) )
			. "$unit;'>$tag</a>";
	}

	switch ( $format ) :
	case 'array' :
		$return =& $a;
		break;
	case 'list' :
		$return = "<ul class='wp-tag-cloud'>\n\t<li>";
		$return .= join("</li>\n\t<li>", $a);
		$return .= "</li>\n</ul>\n";
		break;
	default :
		$return = join("\n", $a);
		break;
	endswitch;

   if( $tdotf_fix_tag_cloud_auto ) {
      return $return;
   }
	return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
}

if( $tdotf_fix_tag_cloud_auto ) {
  function tdotf_tag_cloud_filter($return,$tags,$args) {
     if(is_category()) {
        global $cat;
        if(!isset($args['cat'])) {
           $args['cat'] = $cat;
        }
        return tdotf_cat_tag_cloud($args);
     }
     return $return;
  }
  add_filter('wp_generate_tag_cloud','tdotf_tag_cloud_filter',5,3);
}

if( $tdotf_fix_cat_title_auto ) {
  function tdotf_cat_title_filter($cat_name) {
     if(is_tag()) {
        return $cat_name . " tagged with '".tdotf_get_tag_title()."'";
     }
     return $cat_name;
  }
  add_filter('single_cat_title','tdotf_cat_title_filter');
}

function tdof_the_tags( $before = '', $sep = '', $after = '' ) {
  global $tdotf_tag_get_var,$cat;
  if(is_category() && isset($tdotf_tag_get_var)) {
    $tags = get_the_tags();

    if ( empty( $tags ) )
      return false;

    $cat_link = get_category_link( $cat );
    $tag_list = $before;
    foreach ( $tags as $tag ) {
      if(strpos($cat_link,"?") != FALSE) {
        $link = $cat_link."&".$tdotf_tag_get_var."=".$tag->slug;
      } else {
        $link = $cat_link."?".$tdotf_tag_get_var."=".$tag->slug;
      }
      if ( is_wp_error( $link ) )
        return $link;
      $tag_links[] = '<a href="' . $link . '" rel="tag">' . $tag->name . '</a>';
    }

    $tag_links = join( $sep, $tag_links );
    $tag_links = apply_filters( 'the_tags', $tag_links );
    $tag_list .= $tag_links;

    $tag_list .= $after;

    echo $tag_list;
  } else {
    the_tags($before,$sep,$after);
  }
}

if(isset($tdotf_fancy_tag_regex) && isset($tdotf_tag_get_var)) {

  function tdotf_flush_rewrite_rules() {
     global $wp_rewrite,$wp,$wp_query;
     $wp_rewrite->flush_rules();
  }
  add_action('init', 'tdotf_flush_rewrite_rules');

  function tdotf_add_rewrite_rules( $wp_rewrite ) {
    global $tdotf_fancy_tag_regex,$tdotf_tag_get_var;

    $new_rules = array(
       $tdotf_fancy_tag_regex => "index.php?category_name=".$wp_rewrite->preg_index(1)."&$tdotf_tag_get_var=".$wp_rewrite->preg_index(2)
       # add your own tdo_tag fancy rewrite rules here!
       );

    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
  }
  add_action('generate_rewrite_rules', 'tdotf_add_rewrite_rules');
}

?>
