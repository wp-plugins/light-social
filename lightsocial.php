<?php
/*
Plugin Name: Light Social
Plugin URI: http://www.aldentorres.com/light-social-wordpress-plugin/
Description: Insert a set of social share links at the bottom of each post.
Author: Alden Torres
Version: 1.5
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
	$code .= '<a href="'.$href.'">';
	$code .= '<img src="'.$img.'" alt="'.$tooltip.'" title="'.$tooltip.'" />';
	$code .= '</a>';
	$code .= '</div>';

	return $code;
}

function code_digg($title, $link, $img_prefix)
{
	$href    = 'http://digg.com/submit?url='.$link.'&amp;title='.$title;
	$img     = $img_prefix.'digg.png';
	$tooltip = 'Digg This';

	return code_helper($href, $img, $tooltip);
}

function code_reddit($title, $link, $img_prefix)
{
	$href    = 'http://www.reddit.com/submit?url='.$link.'&amp;title='.$title;
	$img     = $img_prefix.'reddit.png';
	$tooltip = 'Reddit This';

	return code_helper($href, $img, $tooltip);
}

function code_stumbleupon($title, $link, $img_prefix)
{
	$href    = 'http://www.stumbleupon.com/submit?url='.$link.'&amp;title='.$title;
	$img     = $img_prefix.'stumbleupon.png';
	$tooltip = 'Stumble Now!';

	return code_helper($href, $img, $tooltip);
}

function code_yahoo_buzz($title, $link, $img_prefix)
{
	$href    = 'http://buzz.yahoo.com/buzz?targetUrl='.$link.'&amp;headline='.$title;
	$img     = $img_prefix.'yahoo_buzz.png';
	$tooltip = 'Buzz This';

	return code_helper($href, $img, $tooltip);
}

function code_dzone($title, $link, $img_prefix)
{
	$href    = 'http://www.dzone.com/links/add.html?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'dzone.png';
	$tooltip = 'Vote on DZone';

	return code_helper($href, $img, $tooltip);
}

function code_facebook($title, $link, $img_prefix)
{
	$href    = 'http://www.facebook.com/sharer.php?t='.$title.'&amp;u='.$link;
	$img     = $img_prefix.'facebook.png';
	$tooltip = 'Share on Facebook';

	return code_helper($href, $img, $tooltip);
}

function code_delicious($title, $link, $img_prefix)
{
	$href    = 'http://delicious.com/save?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'delicious.png';
	$tooltip = 'Bookmark this on Delicious';

	return code_helper($href, $img, $tooltip);
}

function code_dotnetkicks($title, $link, $img_prefix)
{
	$href    = 'http://www.dotnetkicks.com/kick/?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'dotnetkicks.png';
	$tooltip = 'Kick It on DotNetKicks.com';

	return code_helper($href, $img, $tooltip);
}

function code_dotnetshoutout($title, $link, $img_prefix)
{
	$href    = 'http://dotnetshoutout.com/Submit?title='.$title.'&amp;url='.$link;
	$img     = $img_prefix.'dotnetshoutout.png';
	$tooltip = 'Shout it';

	return code_helper($href, $img, $tooltip);
}

function code_linkedin($title, $link, $img_prefix)
{
	$href    = 'http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'&amp;summary=&amp;source=';
	$img     = $img_prefix.'linkedin.png';
	$tooltip = 'Share on LinkedIn';

	return code_helper($href, $img, $tooltip);
}

function code_technorati($title, $link, $img_prefix)
{
	$href    = 'http://www.technorati.com/faves?add='.$link;
	$img     = $img_prefix.'technorati.png';
	$tooltip = 'Bookmark this on Technorati';

	return code_helper($href, $img, $tooltip);
}

function code_twitter($title, $link, $img_prefix)
{
	$href    = 'http://twitter.com/home?status='.urlencode('Reading '.urldecode($link));
	$img     = $img_prefix.'twitter.png';
	$tooltip = 'Post on Twitter';

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

		$code .= '</div>';
	}

	//return $code . $content; // use this line if you want the links before content
	return $content . $code; // use this line if you want the links after content
}

// change the Light Social custom html for proper render of feed in readers
function lightsocial_insert_feed($content)
{
	// this pattern replace the element <div> with the inner <a>
	$pattern = '/<div class="lightsocial_element"><a href=(.*?)<\/a><\/div>/i';
	$replacement = '<a href=${1}</a>&nbsp;&nbsp;';

	$new_content = preg_replace($pattern, $replacement, $content);

	if (preg_last_error() != PREG_NO_ERROR) // error in preg, probably a backtrack limit error
	{
		// restore the content
		$new_content = $content;
	}

	return $new_content;
}

// head
add_action('wp_head', 'lightsocial_stylesheet');

// content
add_filter('the_content', 'lightsocial_insert');

// content feed
add_filter('the_content_feed', 'lightsocial_insert_feed');
