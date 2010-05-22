<?php
/*
Plugin Name: Light Social
Plugin URI: http://aldenml.com/blog/2009/12/20/light-social-wordpress-plugin/
Description: Insert a set of social share links at the bottom of each post.
Author: Alden Torres
Version: 2.2
Author URI: http://aldenml.com/blog
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

// This class is only a way to create a namespace in a safe manner. Do not think for an instant that
// this is OOP programming.
class LightSocial
{
  //*************************
  // Private variables
  //*************************

  var $version          = '2.2';      // Plugin version
  var $settings         = array();    // Contains the user's settings
  var $defaultsettings  = array();    // Contains the default settings

  //*******************************************
  // Constructor (kind of module init routine)
  //*******************************************

  // PHP4 constructor
  function LightSocial()
  {
    $this->__construct();
  }

  // Initalize the plugin by registering the hooks
  function __construct()
  {
    // Loads the plugin's translated strings.
    load_plugin_textdomain('lightsocial', false, dirname(plugin_basename(__FILE__)));

    // Create array of default settings
    $this->defaultsettings = array(
                   'enabled'         => 1, // 0, 1
                   'position'        => 'after_content', //after_content, before_content
                   'newwindow'       => 'no' // blank, javascript
				   );

    // Create the settings array by merging the user's settings and the defaults
    $usersettings = (array)get_option('lightsocial_settings');
    $this->settings = wp_parse_args($usersettings, $this->defaultsettings);

    // Admin hooks
    add_action('admin_init', array(&$this, 'register_settings'));
    add_action('admin_menu', array(&$this, 'register_settings_page'));

    add_filter('plugin_action_links', array(&$this, 'settings_link'), 10, 2);


    // head
    add_action('wp_head', array(&$this, 'code_insert_stylesheet'));

    // content
    add_filter('the_content', array(&$this, 'code_insert'));

    // content feed
    add_filter('the_content_feed', array(&$this, 'code_insert_feed'));
  }

  //**********************************************
  // Administration options and page
  //**********************************************

  /**
   * Creates the settings group for Lgith Social
   */
  function register_settings()
  {
    register_setting('lightsocial_settings', 'lightsocial_settings', array(&$this, 'validate_settings'));
  }

  /**
   * Defines the sub-menu admin page using the add_options_page function
   * (kind of shortcut to add_submenu_page). Add the plugin options.
   */
  function register_settings_page()
  {
    // create the menu option under Settings
    add_options_page(__('Light Social', 'lightsocial'),
		     __('Light Social', 'lightsocial'),
		     'administrator', __FILE__, array(&$this, 'settings_page'));
  }

  // Add a "Settings" link to the plugins page
  function settings_link($links, $file)
  {
    static $this_plugin;

    if (empty($this_plugin))
      $this_plugin = plugin_basename(__FILE__);

    if ($file == $this_plugin)
      $links[] = '<a href="' . admin_url('options-general.php?page=light-social/lightsocial.php' ) . '">' . __('Settings', 'lightsocial') . '</a>';

    return $links;
  }

  // Validate the settings sent from the settings page
  function validate_settings($settings)
  {
    $settings['enabled'  ] = (!empty($settings['enabled'  ])) ? 1 : 0;
    $settings['position' ] = (!empty($settings['position' ])) ? strtolower($settings['position' ]) : $this->defaultsettings['position' ];
    $settings['newwindow'] = (!empty($settings['newwindow'])) ? strtolower($settings['newwindow']) : $this->defaultsettings['newwindow'];

    return $settings;
  }

  // Build the settings page
  function settings_page()
  {
?>
<div class="wrap">

    <h2><?php _e('Light Social Settings', 'lightsocial'); ?></h2>

    <form method="post" action="options.php">

        <?php settings_fields('lightsocial_settings'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enabled', 'lightsocial'); ?></th>
                <td><input type="checkbox" name="lightsocial_settings[enabled]" <?php checked($this->settings['enabled'], 1); ?> /></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Position', 'lightsocial'); ?></th>
                <td>
                    <select name="lightsocial_settings[position]">
                        <option value="after_content" <?php selected($this->settings['position'], 'after_content'); ?>>
                            After Content
                        </option>
                        <option value="before_content" <?php selected($this->settings['position'], 'before_content'); ?>>
                            Before Content
                        </option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Show in new window?', 'lightsocial'); ?></th>
                <td>
                    <select name="lightsocial_settings[newwindow]">
                        <option value="no" <?php selected($this->settings['newwindow'], 'no'); ?>>
                            No
                        </option>
                        <option value="blank" <?php selected($this->settings['newwindow'], 'blank'); ?>>
                            Using target=&quot;_blank&quot;
                        </option>
                        <option value="javascript" <?php selected($this->settings['newwindow'], 'javascript'); ?>>
                            Using javascript
                        </option>
                    </select>
                </td>
            </tr>

        </table>

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'lightsocial') ?>" />
        </p>

    </form>

</div>
<?php
  }

  //********************************************************
  // Code insert
  //********************************************************

  // this is a helper function to refactor common code
  function code_helper($href, $img, $tooltip)
  {
    $newwindow_code = '';

    switch ($this->settings['newwindow'])
    {
      case 'no':
        $newwindow_code = '';
        break;
      case 'blank':
        $newwindow_code = 'target="_blank"';
        break;
      case 'javascript':
        $newwindow_code = 'onclick="window.open(this.href);return false;"';
        break;
      default:
        $newwindow_code = '';
    }

    $code = '';

    $code .= '<div class="lightsocial_element">';
    $code .= '<a class="lightsocial_a" href="' . $href . '" ' . $newwindow_code . '>';
    $code .= '<img class="lightsocial_img" src="' . $img . '" alt="' . $tooltip . '" title="' . $tooltip . '" />';
    $code .= '</a>';
    $code .= '</div>';

    return $code;
  }

  function code_digg($title, $link, $img_prefix)
  {
    $href    = 'http://digg.com/submit?url='.$link.'&amp;title='.$title;
    $img     = $img_prefix.'digg.png';
    $tooltip = __('Digg This', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_reddit($title, $link, $img_prefix)
  {
    $href    = 'http://www.reddit.com/submit?url='.$link.'&amp;title='.$title;
    $img     = $img_prefix.'reddit.png';
    $tooltip = __('Reddit This', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_stumbleupon($title, $link, $img_prefix)
  {
    $href    = 'http://www.stumbleupon.com/submit?url='.$link.'&amp;title='.$title;
    $img     = $img_prefix.'stumbleupon.png';
    $tooltip = __('Stumble Now!', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_yahoo_buzz($title, $link, $img_prefix)
  {
    $href    = 'http://buzz.yahoo.com/buzz?targetUrl='.$link.'&amp;headline='.$title;
    $img     = $img_prefix.'yahoo_buzz.png';
    $tooltip = __('Buzz This', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_dzone($title, $link, $img_prefix)
  {
    $href    = 'http://www.dzone.com/links/add.html?title='.$title.'&amp;url='.$link;
    $img     = $img_prefix.'dzone.png';
    $tooltip = __('Vote on DZone', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_facebook($title, $link, $img_prefix)
  {
    $href    = 'http://www.facebook.com/sharer.php?t='.$title.'&amp;u='.$link;
    $img     = $img_prefix.'facebook.png';
    $tooltip = __('Share on Facebook', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_viadeo($title, $link, $img_prefix)
  {
    $href    = 'http://www.viadeo.com/shareit/share/?url='.$link.'&amp;title='.$title.'&amp;encoding=UTF-8';
    $img     = $img_prefix.'viadeo.png';
    $tooltip = __('Share it on Viadeo', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_delicious($title, $link, $img_prefix)
  {
    $href    = 'http://delicious.com/save?title='.$title.'&amp;url='.$link;
    $img     = $img_prefix.'delicious.png';
    $tooltip = __('Bookmark this on Delicious', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_dotnetkicks($title, $link, $img_prefix)
  {
    $href    = 'http://www.dotnetkicks.com/kick/?title='.$title.'&amp;url='.$link;
    $img     = $img_prefix.'dotnetkicks.png';
    $tooltip = __('Kick It on DotNetKicks.com', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_dotnetshoutout($title, $link, $img_prefix)
  {
    $href    = 'http://dotnetshoutout.com/Submit?title='.$title.'&amp;url='.$link;
    $img     = $img_prefix.'dotnetshoutout.png';
    $tooltip = __('Shout it', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_linkedin($title, $link, $img_prefix)
  {
    $href    = 'http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'&amp;summary=&amp;source=';
    $img     = $img_prefix.'linkedin.png';
    $tooltip = __('Share on LinkedIn', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_technorati($title, $link, $img_prefix)
  {
    $href    = 'http://www.technorati.com/faves?add='.$link;
    $img     = $img_prefix.'technorati.png';
    $tooltip = __('Bookmark this on Technorati', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_twitter($title, $link, $img_prefix)
  {
    $href    = 'http://twitter.com/home?status='.urlencode('Reading '.urldecode($link));
    $img     = $img_prefix.'twitter.png';
    $tooltip = __('Post on Twitter', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_google_buzz($title, $link, $img_prefix)
  {
    $href    = 'http://www.google.com/buzz/post?url='.$link;
    $img     = $img_prefix.'google_buzz.png';
    $tooltip = __('Google Buzz (aka. Google Reader)', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_faves($title, $link, $img_prefix)
  {
    $href    = 'http://faves.com/Authoring.aspx?u='.$link.'&amp;t='.$title;
    $img     = $img_prefix.'faves.png';
    $tooltip = __('Fave It!', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  function code_misterwong($title, $link, $img_prefix)
  {
    $href    = 'http://www.mister-wong.com/index.php?action=addurl&amp;bm_url='.$link.'&amp;bm_description='.$title;
    $img     = $img_prefix.'misterwong.png';
    $tooltip = __('Bookmark this on Mister Wong', 'light_social');

    return $this->code_helper($href, $img, $tooltip);
  }

  // insert Light Social custom html
  function code_insert($content)
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
    $display = $this->settings['enabled'];

    if ($display)
    {
      $code .= '<div class="lightsocial_container">';

      // digg
      $code .= $this->code_digg($title, $link, $img_prefix);

      // reddit
      $code .= $this->code_reddit($title, $link, $img_prefix);

      // stumbleupon
      $code .= $this->code_stumbleupon($title, $link, $img_prefix);

      // yahoo buzz
      $code .= $this->code_yahoo_buzz($title, $link, $img_prefix);

      // dzone
      $code .= $this->code_dzone($title, $link, $img_prefix);

      // facebook
      $code .= $this->code_facebook($title, $link, $img_prefix);

      // viadeo
      //$code .= $this->code_viadeo($title, $link, $img_prefix);

      // delicious
      $code .= $this->code_delicious($title, $link, $img_prefix);

      // dotnetkicks
      $code .= $this->code_dotnetkicks($title, $link, $img_prefix);

      // dotnetshoutout
      $code .= $this->code_dotnetshoutout($title, $link, $img_prefix);

      // linkedin
      $code .= $this->code_linkedin($title, $link, $img_prefix);

      // technorati
      $code .= $this->code_technorati($title, $link, $img_prefix);

      // twitter
      $code .= $this->code_twitter($title, $link, $img_prefix);

      // google buzz
      $code .= $this->code_google_buzz($title, $link, $img_prefix);

      // faves
      //$code .= $this->code_faves($title, $link, $img_prefix);

      // misterwong
      //$code .= $this->code_misterwong($title, $link, $img_prefix);

      $code .= '</div>';


      // ready to place the code in the content
      switch ($this->settings['position'])
      {
        case 'after_content':
          return $content . $code;
        case 'before_content':
          return $code . $content;
        default:
          return $content; // only for safe
      }
    }
    else
    {
      return $content;
    }
  }

  // add Light Social custom style
  function code_insert_stylesheet()
  {
    $prefix = get_bloginfo('wpurl') . '/wp-content/plugins/light-social/';

    echo '<link rel="stylesheet" href="'.$prefix.'lightsocial.css" type="text/css" media="screen" />';

    // this is a fix hack for transparent png support in IE6
    echo '<!--[if lt IE 7]>';
    echo '<script defer type="text/javascript" src="'.$prefix. 'pngfix.js"></script>';
    echo '<![endif]-->';
  }

  // change the Light Social custom html for proper render of feed in readers
  function code_insert_feed($content)
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
}

//***********************************************
// Init(actual) plugin
//***********************************************

// Start this plugin once all other plugins are fully loaded
add_action('init', 'LightSocial');

function LightSocial() {
  global $lightSocial;

  $lightSocial = new LightSocial();
}
