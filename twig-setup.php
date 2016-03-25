<?php

// Load Twig
require_once dirname(__FILE__) . '/Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
// Initiate Twig
$loop_shortcode_twig_loader = new Twig_Loader_Array(array());
$loop_shortcode_twig = new Twig_Environment($loop_shortcode_twig_loader);

/* Twig WordPress helper extensions  */

// For word concatination
// Use {{ excerpt|words(50,'...') }}
function loop_shortcode_words($string, $words = 50, $append = '...', $trim_punctuation = true) {
	// Simple, quick way
	$return = implode(' ',
				array_slice(
					explode(' ', strip_tags($string)),
					0, $words)
			  );
	// Remove trailing small punctuation
	if($trim_punctuation) {
		if(in_array(substr($return, -1),str_split('.,/@\\&-=+_|#$%^*;:'))) {
			$return = substr($return, 0, -1);
		}
	}
	$return .= $append;
	return $return;
}
$filter = new Twig_SimpleFilter('words', 'loop_shortcode_words');
$loop_shortcode_twig->addFilter($filter);

// For title concatenation. Returns everything before the first colon,
// surrounded by the first and second arguments
// Use {{ excerpt|title('<span class="maintitle">','</span>') }}
function loop_shortcode_title($string, $prepend = '', $append = '') {
	if(strpos($string, ':') === FALSE) return $string;
	// Simple, quick way
	return	$prepend
			. trim(strstr($string, ':', true))
			. $append;
}
$filter = new Twig_SimpleFilter('title', 'loop_shortcode_title');
$loop_shortcode_twig->addFilter($filter);

// For subtitle concatenation. Returns everything after the first colon,
// surrounded by the first and second arguments
// Use {{ excerpt|subtitle('<span class="subtitle">','</span>') }}
function loop_shortcode_subtitle($string, $prepend = '', $append = '') {
	if(strpos($string, ':') === FALSE) return '';
	// Simple, quick way
	return	$prepend
			. trim(substr($string, (-1 * (strlen($string) - strpos($string, ':') - 1))))
			. $append;
}
$filter = new Twig_SimpleFilter('subtitle', 'loop_shortcode_subtitle');
$loop_shortcode_twig->addFilter($filter);

// For outputting text safe for use in title tags
// Use: <a href="{{ link }}" title="View full post of {{ title|titlesafe }}">{{ title }}</a>
function loop_shortcode_titlesafe($string) {
	return str_replace(array("\r\n","\r","\n"), ' ', htmlspecialchars(strip_tags($string)));
}
$filter = new Twig_SimpleFilter('titlesafe', 'loop_shortcode_titlesafe');
$loop_shortcode_twig->addFilter($filter);

// For getting author meta from an ID
// Use {{ otherauthor.id|wpauthormeta('display_name') }}
function loop_shortcode_wpauthormeta($id, $options = array()) {
	$id = (int) $id;
	$field = (is_array($options)) ? $options[0] : $options;
	return get_the_author_meta($field, $id);
}
$filter = new Twig_SimpleFilter('wpauthormeta', 'loop_shortcode_wpauthormeta');
$loop_shortcode_twig->addFilter($filter);

// For getting the author posts page
// use {{ otherauthor.id|wpgetauthorpage }}
function loop_shortcode_wpgetauthorpage($id) {
	$id = (int) $id;
	return get_author_posts_url($id);
}
$filter = new Twig_SimpleFilter('wpgetauthorpage', 'loop_shortcode_wpgetauthorpage');
$loop_shortcode_twig->addFilter($filter);
