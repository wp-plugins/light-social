<?php
/*
Plugin Name: Light Social
Plugin URI: http://www.aldentorres.com/light-social-wordpress-plugin/
Description: Insert a set of social share links at the bottom of each post.
Author: Alden Torres
Version: 1.13
Author URI: http://www.aldentorres.com/
*/
/*  Copyright 2009  Alden Torres  (email : aldenml@yahoo.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Runs after WordPress admin has finished loading but before any headers are sent.
 * Useful for intercepting $_GET or $_POST triggers. 
 */
function lightsocial_init()
{
	// Loads the plugin's translated strings. 
	load_plugin_textdomain('light_social', false, dirname(plugin_basename(__FILE__)));
}

// add Light Social custom style
function lightsocial_stylesheet()
{
	$prefix = get_bloginfo('wpurl') . '/wp-content/plugins/light-social/';

	echo '<link rel="stylesheet" href="'.$prefix.'lightsocial.css" type="text/css" media="screen" />';

	// this is a fix hack for transparent png support in IE6
	echo '<!--[if lt IE 7]>';
	echo '<script defer type="text/javascript" src="'.$prefix. 'pngfix.js"></script>';
	echo '<![endif]-->';
}

// this is a helper function to refactor common code
function code_helper($href, $img, $tooltip)
{
	$code = '';

	$code .= '<div class="lightsocial_element">';
	$code .= '<a class="lightsocial_a" href="'.$href.'">';
	$code .= '<img class="lightsocial_img" src="'.$img.'" alt="'.$tooltip.'" title="'.$tooltip.'" />';
	$code .= '</a>';
	$code .= '</div>';

	return $code;
}

function code_digg($title, $link, $img_prefix)
{
	$href    = 'http://digg.com/submit?url='.$link.'&amp;title='.$title;
	$img     = $img_prefix.'digg.png';
	$tooltip = __('Digg This', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_reddit($title, $link, $img_prefix)
{
	$href    = 'http://www.reddit.com/submit?url='.$link.'&amp;title='.$title;
	$img     = $img_prefix.'reddit.png';
	$tooltip = __('Reddit This', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_stumbleupon($title, $link, $img_prefix)
{
	$href    = 'http://www.stumbleupon.com/submit?url='.$link.'&amp;title='.$title;
	$img     = $img_prefix.'stumbleupon.png';
	$tooltip = __('Stumble Now!', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_yahoo_buzz($title, $link, $img_prefix)
{
	$href    = 'http://buzz.yahoo.com/buzz?targetUrl='.$link.'&amp;headline='.$title;
	$img     = $img_prefix.'yahoo_buzz.png';
	$tooltip = __('Buzz This', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_dzone($title, $link, $img_prefix)
{
	$href    = 'http://www.dzone.com/links/add.html?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'dzone.png';
	$tooltip = __('Vote on DZone', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_facebook($title, $link, $img_prefix)
{
	$href    = 'http://www.facebook.com/sharer.php?t='.$title.'&amp;u='.$link;
	$img     = $img_prefix.'facebook.png';
	$tooltip = __('Share on Facebook', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_viadeo($title, $link, $img_prefix)
{
        $href    = 'http://www.viadeo.com/shareit/share/?url='.$link.'&amp;title='.$title.'&amp;encoding=UTF-8';
        $img     = $img_prefix.'viadeo.png';
        $tooltip = __('Share it on Viadeo', 'light_social');

        return code_helper($href, $img, $tooltip);
}

function code_delicious($title, $link, $img_prefix)
{
	$href    = 'http://delicious.com/save?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'delicious.png';
	$tooltip = __('Bookmark this on Delicious', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_dotnetkicks($title, $link, $img_prefix)
{
	$href    = 'http://www.dotnetkicks.com/kick/?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'dotnetkicks.png';
	$tooltip = __('Kick It on DotNetKicks.com', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_dotnetshoutout($title, $link, $img_prefix)
{
	$href    = 'http://dotnetshoutout.com/Submit?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'dotnetshoutout.png';
	$tooltip = __('Shout it', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_linkedin($title, $link, $img_prefix)
{
	$href    = 'http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'&amp;summary=&amp;source=';
	$img     = $img_prefix.'linkedin.png';
	$tooltip = __('Share on LinkedIn', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_technorati($title, $link, $img_prefix)
{
	$href    = 'http://www.technorati.com/faves?add='.$link;
	$img     = $img_prefix.'technorati.png';
	$tooltip = __('Bookmark this on Technorati', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_twitter($title, $link, $img_prefix)
{
	$href    = 'http://twitter.com/home?status='.urlencode('Reading '.urldecode($link));
	$img     = $img_prefix.'twitter.png';
	$tooltip = __('Post on Twitter', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_google_buzz($title, $link, $img_prefix)
{
	$href    = 'http://www.google.com/buzz/post?url='.$link;
	$img     = $img_prefix.'google_buzz.png';
	$tooltip = __('Google Buzz (aka. Google Reader)', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_faves($title, $link, $img_prefix)
{
	$href    = 'http://faves.com/Authoring.aspx?u='.$link.'&amp;t='.$title;
	$img     = $img_prefix.'faves.png';
	$tooltip = __('Fave It!', 'light_social');

	return code_helper($href, $img, $tooltip);
}

function code_misterwong($title, $link, $img_prefix)
{
	$href    = 'http://www.mister-wong.com/index.php?action=addurl&amp;bm_url='.$link.'&amp;bm_description='.$title;
	$img     = $img_prefix.'misterwong.png';
	$tooltip = __('Bookmark this on Mister Wong', 'light_social');

	return code_helper($href, $img, $tooltip);
}

// insert Light Social custom html
function lightsocial_insert($content)
{
	global $wp_query;

	$post = $wp_query->post; // get post content
	$id = $post->ID; // get post id
	$postlink = get_permalink($id); // get post link
	$title = trim(urlencode($post->post_title)); // get post title
	$link = split('#', $postlink); // split the link with '#', for comment
	$link = urlencode($link[0]); // get the actual link from array
	$img_prefix = get_bloginfo('wpurl') . '/wp-content/plugins/light-social/';

	$code = '';

	//$display = is_home() || is_single(); // use this line for display on home and single posts only
	//$display = is_single(); // use this line for display on single posts only
	$display = true; // if you want to put some special condition

	if ($display)
	{
		$code .= '<div class="lightsocial_container">';

		// digg
		$code .= code_digg($title, $link, $img_prefix);

		// reddit
		$code .= code_reddit($title, $link, $img_prefix);
		
		// stumbleupon
		$code .= code_stumbleupon($title, $link, $img_prefix);

		// yahoo buzz
		$code .= code_yahoo_buzz($title, $link, $img_prefix);

		// dzone
		$code .= code_dzone($title, $link, $img_prefix);

		// facebook
		$code .= code_facebook($title, $link, $img_prefix);

		// viadeo
		//$code .= code_viadeo($title, $link, $img_prefix);

		// delicious
		$code .= code_delicious($title, $link, $img_prefix);

		// dotnetkicks
		$code .= code_dotnetkicks($title, $link, $img_prefix);
		
		// dotnetshoutout
		$code .= code_dotnetshoutout($title, $link, $img_prefix);

		// linkedin
		$code .= code_linkedin($title, $link, $img_prefix);

		// technorati
		$code .= code_technorati($title, $link, $img_prefix);

		// twitter
		$code .= code_twitter($title, $link, $img_prefix);
		
		// google buzz
		$code .= code_google_buzz($title, $link, $img_prefix);
		
		// faves
		//$code .= code_faves($title, $link, $img_prefix);
		
		// misterwong
		//$code .= code_misterwong($title, $link, $img_prefix);

		$code .= '</div>';
	}

	//return $code . $content; // use this line if you want the links before content
	return $content . $code; // use this line if you want the links after content
}

// change the Light Social custom html for proper render of feed in readers
function lightsocial_insert_feed($content)
{
	// this pattern replace the element <div> with the inner <a>
	$pattern = '/<div class="lightsocial_element"><a class="lightsocial_a" href=(.*?)<\/a><\/div>/i';
	$replacement = '<a class="lightsocial_a" href=${1}</a>&nbsp;&nbsp;';

	$new_content = preg_replace($pattern, $replacement, $content);

	if (function_exists('preg_last_error')) // PHP >= 5.2.0
	{
		if (preg_last_error() != PREG_NO_ERROR) // error in preg, probably a backtrack limit error
		{
			// restore the content
			$new_content = $content;
		}
	}
	
	// VERY IMPORTANT: If your PHP < 5.2.0 you will not see any preg error.
	// For long, very long post, you can get a backtrack limit error.

	return $new_content;
}

// init
add_action('init', 'lightsocial_init');

// head
add_action('wp_head', 'lightsocial_stylesheet');

// content
add_filter('the_content', 'lightsocial_insert');

// content feed
add_filter('the_content_feed', 'lightsocial_insert_feed');
