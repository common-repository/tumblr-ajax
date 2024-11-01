<?php
/*  Copyright 2015  Humphrey Aaron  (email : humphreyaaron7@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * @package	Tumblr AJAX
 * @author	Humphrey Aaron, Aris Blevins
 * @version	1.2
 */
 
/*
Plugin Name: Tumblr AJAX Widget
Plugin URI: http://wordpress.org/extend/plugins/tumblr-ajax
Description: Get/display Tumblr posts via AJAX / Javascript / Client-side HTML requests. This plugin is great where the WordPress hosting server does not allow external HTTP requests, or where external HTTP requests on the server are preferred to be minimal.
Author: Humphrey Aaron, Aris Blevins
Version: 1.2
Author URI: yellowpressgroup.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Prevent direct access to the plugin 
  if (!defined('ABSPATH')) {
    exit(__( "Sorry, you are not allowed to access this page directly." ));
  }

define( 'TUMBLR_AJAXWEB_PATH', plugin_dir_url(__FILE__));
define ('TUMBLR_AJAX_OAUTH_API', 'jkd00j21jsWRMoD874K9L0ETg0aOrI3ZxOnjcPZ0Xa5UqKFVPg');


function tumblr_ajax_styles(){
  //get default stylesheet ready
  wp_enqueue_style( 'TumblrAJAX', TUMBLR_AJAXWEB_PATH . '/default.css' ); 
}
add_action('wp_enqueue_scripts', 'tumblr_ajax_styles');


// widget code
function tumblr_ajax($args) {

    extract($args);
    $options = get_option('tumblr_ajax');

    
    //if (!is_single() && !is_home())
    //	return;

    $title = empty($options['title'])  ? '' : $options['title'];
    $link_title = empty($options['title'])  ? 'false' : $options['link_title'];
    $tumblr_domain = empty($options['domainURL']) 		? 'demo.tumblr.com'	: rtrim($options['domainURL'], "/ \t\n\r");
    $posts_count = empty($options['count'])  		? 5	: $options['count'];
    $post_link = empty($options['post_link'] )	? 'false'	: $options['post_link'];
    $errors = empty($options['report_errors'] )	? 'false'	: $options['report_errors'];
    $showfirstimage = empty($options['show_first_image'] )	? 'false'	: $options['show_first_image'];
    $first_photo_size = empty($options['first_photo_size'] )	? '100%'	: $options['first_photo_size'];
    $load_regular = empty($options['load_regular'] )	? ''	: $options['load_regular'];
    $load_photo = empty($options['load_photo'] )	? ''	: $options['load_photo'];
    $load_photo_size = empty($options['load_photo_size'] )	? '100px'	: $options['load_photo_size'];
    $load_quote = empty($options['load_quote'] )	? ''	: $options['load_quote'];
    $load_link = empty( $options['load_link'] )	? ''	: $options['load_link'];
    $load_conversation = empty( $options['load_conversation'] )	? ''	: $options['load_conversation'];
    $load_audio = empty( $options['load_audio'] )	? ''	: $options['load_audio'];
    $load_video = empty( $options['load_video'] )	? ''	: $options['load_video'];
    $load_video_size = empty( $options['load_video_size'] )	? '250'	: $options['load_video_size'];    
    $load_answer = empty( $options['load_answer'] )	? ''	: $options['load_answer'];
    
    $create_excerpt = empty( $options['create_excerpt'] )	? 'false'	: $options['create_excerpt'];
    $excerpt_count = empty( $options['excerpt_count'] )	? '0'	: $options['excerpt_count'];
    $strip_htmlALL = empty( $options['strip_htmlALL'] )	? 'false'	: $options['strip_htmlALL'];
    $allow_HTML = empty( $options['allow_HTML'] )	? ''	: $options['allow_HTML'];
   
      if ($link_title) {
        $html_title = '<a href="http://' . $tumblr_domain . '" target="_blank">' . $title . '</a>';
      }
      else {
        $html_title = $title;
      }
      
      //deal with post types to request from Tumblr
      $post_types = $load_regular . ' ' . $load_photo . ' ' . $load_quote . ' ' . $load_link . ' ' . $load_conversation . ' ' . $load_audio . ' ' . $load_video . ' ' . $load_answer;
      $post_types_count = str_word_count ($post_types, 0);
      $selected_post_type = $post_types;
      
		if ($post_types_count == 1 ) {			
			$AJAX_url = "http://api.tumblr.com/v2/blog/" . $tumblr_domain . "/posts/" . trim($selected_post_type) . "?api_key=" . TUMBLR_AJAX_OAUTH_API . "&amp;limit=" . $posts_count . "&amp;callback=tumblr_ajax_load";
		} 
	  
		elseif ($post_types_count == 8) {			
			$AJAX_url = "http://api.tumblr.com/v2/blog/" . $tumblr_domain . "/posts?api_key=" . TUMBLR_AJAX_OAUTH_API . "&amp;limit=" . $posts_count . "&amp;callback=tumblr_ajax_load";
		} 
		else 
		{			
			$AJAX_url = "http://api.tumblr.com/v2/blog/" . $tumblr_domain . "/posts?api_key=" . TUMBLR_AJAX_OAUTH_API . "&amp;limit=50&amp;callback=tumblr_ajax_load";
		}
	
	
	
      $before_title = '<h2>';
      $after_title = '</h2>';
      $carousel_open = '<div class="tumblr-ajax-container">';
      $carousel_close = '</div>';
    ?>
    <?php echo $before_widget . $carousel_open; ?>
        <?php echo $before_title . $html_title . $after_title; 
       
              ?>
              <input type="hidden" id="tumblr_ajax_sel_posts_count" name="tumblr_ajax_sel_posts_count" value="<?php echo $posts_count ?>" />
              <input type="hidden" id="tumblr_ajax_sel_post_link" name="tumblr_ajax_sel_post_link" value="<?php echo $post_link ?>" />
              <input type="hidden" id="tumblr_ajax_sel_errors" name="tumblr_ajax_sel_errors" value="<?php echo $errors ?>" />
              <input type="hidden" id="tumblr_ajax_sel_showfirstimage" name="tumblr_ajax_sel_showfirstimage" value="<?php echo $showfirstimage ?>" />
              <input type="hidden" id="tumblr_ajax_sel_first_photo_size" name="tumblr_ajax_sel_first_photo_size" value="<?php echo $first_photo_size ?>" />
              <input type="hidden" id="tumblr_ajax_sel_load_photo_size" name="tumblr_ajax_sel_load_photo_size" value="<?php echo $load_photo_size ?>" />
              <input type="hidden" id="tumblr_ajax_sel_create_excerpt" name="tumblr_ajax_sel_create_excerpt" value="<?php echo $create_excerpt ?>" />
              <input type="hidden" id="tumblr_ajax_sel_excerpt_count" name="tumblr_ajax_sel_excerpt_count" value="<?php echo $excerpt_count ?>" />
              <input type="hidden" id="tumblr_ajax_sel_strip_htmlALL" name="tumblr_ajax_sel_strip_htmlALL" value="<?php echo $strip_htmlALL ?>" />
              <input type="hidden" id="tumblr_ajax_sel_allow_HTML" name="tumblr_ajax_sel_allow_HTML" value="<?php echo $allow_HTML ?>" />
              <input type="hidden" id="tumblr_ajax_sel_types" name="tumblr_ajax_sel_types" value="<?php echo $selected_post_type ?>" />
              <input type="hidden" id="tumblr_ajax_sel_posttypes" name="tumblr_ajax_sel_posttypes" value="<?php echo $post_types_count ?>" />
              <input type="hidden" id="tumblr_ajax_sel_videosize" name="tumblr_ajax_sel_videosize" value="<?php echo $load_video_size ?>" />
              
              <div id="tumblr_ajax_data" class="running_ajax"></div>
              <script type="text/javascript" src="<?php echo TUMBLR_AJAXWEB_PATH ?>/tumblr-ajax.js"></script>
              <script type="text/javascript" src="<?php echo $AJAX_url ?>"></script>
             

          <?php  
              echo $carousel_close .$after_widget; ?>
        <?php
      $post = $original_post;
}

// widget controller
function tumblr_ajax_control() {
    
  $options = $newoptions = get_option('tumblr_ajax');
  if ( $_POST['tumblr_ajax-submit'] ) {
	$newoptions['title'] = strip_tags( stripslashes( $_POST['tumblr-ajax-title'] ) );
	$newoptions['link_title'] = strip_tags( stripslashes( $_POST['tumblr-ajax-link_title'] ) );
	$newoptions['domainURL'] = strip_tags( stripslashes( $_POST['tumblr-ajax-domain'] ) );
	$newoptions['count'] = strip_tags( stripslashes( $_POST['tumblr-ajax-count'] ) );
	$newoptions['post_link'] = strip_tags( stripslashes( $_POST['tumblr-ajax-post-link'] ) );
	$newoptions['report_errors'] = strip_tags( stripslashes( $_POST['tumblr-ajax-errors'] ) );
	$newoptions['show_first_image'] = strip_tags( stripslashes( $_POST['tumblr-ajax-showfirstimage'] ) );
	$newoptions['first_photo_size'] = strip_tags( stripslashes( $_POST['tumblr-ajax-firstphotosize'] ) );
	
	$newoptions['load_regular'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadregular'] ) );
	$newoptions['load_photo'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadphoto'] ) );
	$newoptions['load_photo_size'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadphotosize'] ) );
	$newoptions['load_quote'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadquote'] ) );
	$newoptions['load_link'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadlink'] ) );
	$newoptions['load_conversation'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadconversation'] ) );
	$newoptions['load_audio'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadaudio'] ) );
	$newoptions['load_video'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadvideo'] ) );
	$newoptions['load_video_size'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadvideosize'] ) );
	$newoptions['load_answer'] = strip_tags( stripslashes( $_POST['tumblr-ajax-loadanswer'] ) );
	$newoptions['create_excerpt'] = strip_tags( stripslashes( $_POST['tumblr-ajax-create-excerpt'] ) );
	$newoptions['excerpt_count'] = strip_tags( stripslashes( $_POST['tumblr-ajax-excerpt-count'] ) );
	$newoptions['strip_htmlALL'] = strip_tags( stripslashes( $_POST['tumblr-ajax-strip-HTML'] ) );
	$newoptions['allow_HTML'] = $_POST['tumblr-ajax-allow-html'];
  }

  if ( $options != $newoptions ) {
    $options = $newoptions;
    update_option( 'tumblr_ajax', $options );
  }

  $title = esc_attr( $options['title'] );
  $link_title = esc_attr( $options['link_title'] );
  $domainURL = esc_attr( $options['domainURL'] );
  $count = esc_attr( $options['count'] );
  $post_link = esc_attr( $options['post_link'] );
  $errors = esc_attr( $options['report_errors'] );
  $showfirstimage = esc_attr( $options['show_first_image'] );
  $first_photo_size = esc_attr( $options['first_photo_size'] );
  
  $load_regular = esc_attr( $options['load_regular'] );
  $load_photo = esc_attr( $options['load_photo'] );
  $load_photo_size = esc_attr( $options['load_photo_size'] );
  $load_quote = esc_attr( $options['load_quote'] );
  $load_link = esc_attr( $options['load_link'] );
  $load_conversation = esc_attr( $options['load_conversation'] );
  $load_audio = esc_attr( $options['load_audio'] );
  $load_video = esc_attr( $options['load_video'] );
  $load_video_size = esc_attr( $options['load_video_size'] );
  $load_answer = esc_attr( $options['load_answer'] );
  $create_excerpt = esc_attr( $options['create_excerpt'] );
  $excerpt_count = esc_attr( $options['excerpt_count'] );
  $strip_htmlALL = esc_attr( $options['strip_htmlALL'] );
  $allow_HTML  = esc_attr( $options['allow_HTML'] );
?>
		  <p>
		    <label for="tumblr-ajax-title">Title: <input class="widefat" id="tumblr-ajax-title" name="tumblr-ajax-title" type="text" value="<?php echo $title; ?>" />
		    </label>
		  </p>
		  <p>
		    <input class="checkbox" type="checkbox" id="tumblr-ajax-link_title" value="true" name="tumblr-ajax-link_title" <?php if ($link_title) echo 'checked'; ?> />
		    <label for="tumblr-ajax-link_title">Make main Title a link to Tumblr Blog</label>
		  </p>
		  <p>
		    <label for="tumblr-ajax-domain">
		      <?php _e( 'Tumblr URL:', 'tumblr_ajax' ) ?> <em>e.g. demo.tumblr.com</em>  <input class="widefat" id="tumblr-ajax-domain" name="tumblr-ajax-domain" type="text" value="<?php echo $domainURL; ?>" />
		    </label>
		  </p>
		  <p>
		    <label for="tumblr-ajax-count">
		      <?php _e( 'No. of Posts (numeric):', 'tumblr_ajax' ) ?> <input class="widefat" id="tumblr-ajax-count" name="tumblr-ajax-count" style="width:50px;" type="text" value="<?php echo $count; ?>" />
		      <em>Note that Tumblr limits this to 50</em>
		    </label>
		  </p>
		  <p>
		    <input class="checkbox" type="checkbox" id="tumblr-ajax-errors" value="true" name="tumblr-ajax-errors" <?php if ($errors) echo 'checked'; ?> />
		    <label for="tumblr-ajax-errors">Report back errors</label>
		  </p>
		  <p>
		    <input class="checkbox" type="checkbox" id="tumblr-ajax-post-link" value="true" name="tumblr-ajax-post-link" <?php if ($post_link) echo 'checked'; ?> />
		    <label for="tumblr-ajax-post-link">Make each post Title a link to corresponding Tumblr Post</label>
		  </p>
		  <p>
		    <input class="checkbox" type="checkbox" id="tumblr-ajax-create-excerpt" value="true" name="tumblr-ajax-create-excerpt" <?php if ($create_excerpt) echo 'checked'; ?> />
		    <label for="tumblr-ajax-create-excerpt">Show excerpt instead of entire post</label>
		  </p>
		  <p>
		    <em>Excerpt character length </em><input class="widefat" id="tumblr-ajax-excerpt-count" name="tumblr-ajax-excerpt-count" style="width:50px;" type="text" value="<?php echo $excerpt_count; ?>" />
		  </p>
		  <p>
		    <input class="checkbox" type="checkbox" id="tumblr-ajax-strip-HTML" value="true" name="tumblr-ajax-strip-HTML" <?php if ($strip_htmlALL) echo 'checked'; ?> />
		    <label for="tumblr-ajax-strip-HTML">Strip all HTML Tags from Post</label>
		  </p>
		  <p>
		    <em>Allow only these HTML Tags e.g.&lt;b&gt;&lt;p&gt;<br/>(Note: &lt;div&gt;&lt;blockquote&gt;&lt;strong&gt; are always allowed)</em>
		  </p>
		  <p>
		    <input class="widefat" id="tumblr-ajax-allow-html" name="tumblr-ajax-allow-html" style="width:250px;" type="text" value="<?php echo $allow_HTML; ?>" />
		  </p>
		  <p>
		    <input class="checkbox" type="checkbox" value="true" id="tumblr-ajax-showfirstimage" name="tumblr-ajax-showfirstimage" <?php if ($showfirstimage) echo 'checked'; ?> />
		    <label for="tumblr-ajax-showfirstimage">Show the first image (if any) in post as a feature image</label>
		    </p>
		  <p>
		     <em>Select preferred image size:</em>&nbsp;&nbsp;<select id="tumblr-ajax-firstphotosize" name="tumblr-ajax-firstphotosize" value="<?php echo $first_photo_size; ?>">
		      <option value="10%"
			<?php if ($first_photo_size=='10%') echo 'selected="selected"'; ?>>10%
		      </option>
		      <option value="20%"
			<?php if ($first_photo_size=='20%') echo 'selected="selected"'; ?>>20%
		      </option>
		      <option value="30%"
			<?php if ($first_photo_size=='30%') echo 'selected="selected"'; ?>>30%
		      </option>
		      <option value="40%"
			<?php if ($first_photo_size=='40%') echo 'selected="selected"'; ?>>40%
		      </option>
		      <option value="50%"
			<?php if ($first_photo_size=='50%') echo 'selected="selected"'; ?>>50%
		      </option>
		      <option value="60%"
			<?php if ($first_photo_size=='60%') echo 'selected="selected"'; ?>>60%
		      </option>
		      <option value="70%"
			<?php if ($first_photo_size=='70%') echo 'selected="selected"'; ?>>70%
		      </option>
		      <option value="80%"
			<?php if ($first_photo_size=='80%') echo 'selected="selected"'; ?>>80%
		      </option>
		      <option value="90%"
			<?php if ($first_photo_size=='90%') echo 'selected="selected"'; ?>>90%
		      </option>
		      <option value="100%"
			<?php if ($first_photo_size=='100%') echo 'selected="selected"'; ?>>100%
		      </option>
		    </select>  
		  </p>
		  
		  <p>
		    <strong>Load the following type of posts:</strong>
		  </p>
		  
		  <p>
		    <input class="checkbox" value="text" type="checkbox" <?php if ($load_regular) echo 'checked'; ?> id="tumblr-ajax-loadregular" name="tumblr-ajax-loadregular" />
		      <label for="tumblr-ajax-loadregular">Regular posts</label>
		  </p>
		  
		  <p>
		    <input class="checkbox"  value="photo" type="checkbox" <?php if ($load_photo) echo 'checked'; ?> id="tumblr-ajax-loadphoto" name="tumblr-ajax-loadphoto" />
		      <label for="tumblr-ajax-loadphoto">Photo posts</label>
		    <label for="tumblr-ajax-loadphotosize"><strong><em>Photo size:</em></strong></label>
		    <select id="tumblr-ajax-loadphotosize" name="tumblr-ajax-loadphotosize" value="<?php echo $load_photo_size; ?>">
		      <option value="75"
			<?php if ($load_photo_size==75) echo 'selected="selected"'; ?>>75px
		      </option><option value="100"
			<?php if ($load_photo_size==100) echo 'selected="selected"'; ?>>100px
		      </option><option value="250"
			<?php if ($load_photo_size==250) echo 'selected="selected"'; ?>>250px
		      </option><option value="400"
			<?php if ($load_photo_size==400) echo 'selected="selected"'; ?>>400px
		      </option><option value="500"
			<?php if ($load_photo_size==500) echo 'selected="selected"'; ?>>500px
		      </option>
		    </select>
		  </p>
		  
		  <p>
		    <input class="checkbox"  value="quote"  type="checkbox" <?php if ($load_quote) echo 'checked'; ?> id="tumblr-ajax-loadquote" name="tumblr-ajax-loadquote" />
		      <label for="tumblr-ajax-loadquote">Quotation posts </label>
		  </p>
		  <p>
		    <input class="checkbox"  value="link"  type="checkbox" <?php if ($load_link) echo 'checked'; ?> id="tumblr-ajax-loadlink" name="tumblr-ajax-loadlink" />
		      <label for="tumblr-ajax-loadlink">Link posts </label>
		  </p>
		  <p>
		    <input class="checkbox"  value="chat"  type="checkbox" <?php if ($load_conversation) echo 'checked'; ?> id="tumblr-ajax-loadconversation" name="tumblr-ajax-loadconversation" />
		      <label for="tumblr-ajax-loadconversation">Conversation posts</label>
		  </p>
		  
		  <p>
		    <input class="checkbox"  value="audio"  type="checkbox" <?php if ($load_audio) echo 'checked'; ?> id="tumblr-ajax-loadaudio" name="tumblr-ajax-loadaudio" />
		      <label for="tumblr-ajax-loadaudio">Audio posts</label>
		  </p>
		  
		  <p>
		    <input class="checkbox"  value="video"  type="checkbox" <?php if ($load_video) echo 'checked'; ?> id="tumblr-ajax-loadvideo" name="tumblr-ajax-loadvideo" />
		      <label for="tumblr-ajax-loadvideo">Video posts
		    </label>
		      <label for="tumblr-ajax-loadphotosize">
			<strong>
			  <em>Video size:</em>
			</strong>
		      </label>
		      <select id="tumblr-ajax-loadvideosize" name="tumblr-ajax-loadvideosize" value="<?php echo $load_video_size; ?>">
			<option value="75"
			  <?php if ($load_video_size==400) echo 'selected="selected"'; ?>>400px
			</option><option value="100"
			  <?php if ($load_video_size==500) echo 'selected="selected"'; ?>>500px
			</option><option value="250"
			  <?php if ($load_video_size==250) echo 'selected="selected"'; ?>>250px
			</option>
		      </select>
		  </p>
		  <p>
		    <input class="checkbox"  value="answer"  type="checkbox" <?php if ($load_answer) echo 'checked'; ?> id="tumblr-ajax-loadanswer" name="tumblr-ajax-loadanswer" />
		    <label for="tumblr-ajax-loadanswer">
		      Answer posts
		    </label>
		  </p>
		  <hr style="width:100%"/>
		  <input type="hidden" id="tumblr_ajax-submit" name="tumblr_ajax-submit" value="1" />
<?php

}
// widget initializer
function tumblr_ajax_init()
{
	wp_register_sidebar_widget( 'tumblr_ajax', 'Tumblr AJAX', 'tumblr_ajax', array('Description' => 'Display Tumblr posts on your site' ));
	wp_register_widget_control( 'tumblr_ajax', 'Tumblr AJAX', 'tumblr_ajax_control', array( 'width' =>  300, 'height' => 470));
}

add_action('widgets_init', 'tumblr_ajax_init' );



//function plugin_meta_links($links, $file) {  
//    $plugin = plugin_basename(__FILE__);
//    if ($file == $plugin)
//    {
//      $don_link =  '<a href="yellowpressgroup.com" target="_blank">' . __('Donate') . '</a>';
//      array_push($links, $don_link);
//    }
//    return $links;  
//  }  
//add_filter( 'plugin_row_meta', 'plugin_meta_links', 10, 2 );  


?>
