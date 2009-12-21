<?php
/*
Plugin Name: Light Social
Plugin URI: http://www.aldentorres.com/lightsocial-wordpress-plugin/
Description: Insert a set of social share links at the bottom of each post.
Author: Alden Torres
Version: 1.1
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

//add lightsocial custom style
function lightsocial_stylesheet()
{
	echo '<link rel="stylesheet" href="'. get_bloginfo('wpurl') . '/wp-content/plugins/light-social/lightsocial.css" type="text/css" media="screen" />';
}

function code_digg($title, $link, $img_prefix)
{
	$code = '';

	$code .= '<div class="lightsocial_element">';
	$code .= '<a href="http://digg.com/submit?url='.$link.'&amp;title='.$title.'">';
	$code .= '<img src="'.$img_prefix.'digg.ico" alt="Digg This" title="Digg This" />';
	$code .= '</a>';
	$code .= '</div>';

	return $code;
}

function code_reddit($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
	$code .= '<a href="http://www.reddit.com/submit?url='.$link.'&amp;title='.$title.'">';
        $code .= '<img src="'.$img_prefix.'reddit.ico" alt="Reddit This" title="Reddit This" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_yahoo_buzz($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
	$code .= '<a href="http://buzz.yahoo.com/buzz?targetUrl='.$link.'&amp;headline='.$title.'">';
        $code .= '<img src="'.$img_prefix.'yahoo_buzz.ico" alt="Buzz This" title="Buzz This" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_dzone($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
        $code .= '<a href="http://www.dzone.com/links/add.html?title='.$title.'&amp;url='.$link.'">';
        $code .= '<img src="'.$img_prefix.'dzone.ico" alt="Vote on DZone" title="Vote on DZone" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_facebook($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
        $code .= '<a href="http://www.facebook.com/sharer.php?t='.$title.'&amp;u='.$link.'">';
        $code .= '<img src="'.$img_prefix.'facebook.ico" alt="Share on a Facebook" title="Share on a Facebook" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_delicious($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
        $code .= '<a href="http://delicious.com/save?title='.$title.'&amp;url='.$link.'">';
        $code .= '<img src="'.$img_prefix.'delicious.ico" alt="Bookmark this on Delicious" title="Bookmark this on Delicious" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_dotnetkicks($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
	$code .= '<a href="http://www.dotnetkicks.com/kick/?title='.$title.'&amp;url='.$link.'">';
	$code .= '<img src="'.$img_prefix.'dotnetkicks.ico" alt="Kick It on DotNetKicks.com" title="Kick It on DotNetKicks.com" />';
	$code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_linkedin($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
        $code .= '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'&amp;summary=&amp;source=">';
        $code .= '<img src="'.$img_prefix.'linkedin.ico" alt="Share on LinkedIn" title="Share on LinkedIn" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_technorati($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
        $code .= '<a href="http://www.technorati.com/faves?add='.$link.'">';
        $code .= '<img src="'.$img_prefix.'technorati.ico" alt="Bookmark this on Technorati" title="Bookmark this on Technorati" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

function code_twitter($title, $link, $img_prefix)
{
        $code = '';

	$code .= '<div class="lightsocial_element">';
        $code .= '<a href="http://twitter.com/home?status='.urlencode('Reading '.urldecode($link)).'">';
        $code .= '<img src="'.$img_prefix.'twitter.ico" alt="Post on Twitter" title="Post on Twitter" />';
        $code .= '</a>';
	$code .= '</div>';

        return $code;
}

//insert lightsocial custom html
function lightsocial_insert($content)
{
	global $wp_query;
	$post = $wp_query->post; //get post content
	$id = $post->ID; //get post id
	$postlink = get_permalink($id); //get post link
	$title = trim(urlencode($post->post_title)); // get post title
	$link = split('#', $postlink); //split the link with '#', for comment
	$link = urlencode($link[0]);
	$img_prefix = get_bloginfo('wpurl') . '/wp-content/plugins/light-social/';

	$code = '';

	$display = true;

	if ($display)
	{
		$code .= '<div class="lightsocial_container">';

		//digg
		$code .= code_digg($title, $link, $img_prefix);

		//reddit
		$code .= code_reddit($title, $link, $img_prefix);

		//yahoo buzz
		$code .= code_yahoo_buzz($title, $link, $img_prefix);

		//dzone
		$code .= code_dzone($title, $link, $img_prefix);

		//facebook
                $code .= code_facebook($title, $link, $img_prefix);

		//facebook
                $code .= code_delicious($title, $link, $img_prefix);

		//dotnetkicks
		$code .= code_dotnetkicks($title, $link, $img_prefix);

		//linkedin
                $code .= code_linkedin($title, $link, $img_prefix);

		//technorati
                $code .= code_technorati($title, $link, $img_prefix);

		//twitter
                $code .= code_twitter($title, $link, $img_prefix);

		$code .= '</div>';
	}

	return $content . $code;
}

add_action('wp_head', 'lightsocial_stylesheet');
add_filter('the_content', 'lightsocial_insert');
