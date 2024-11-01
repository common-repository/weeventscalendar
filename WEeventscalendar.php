<?php
/*
Plugin Name: WEevenstcalendar
Plugin URI: http://weabers.com/wordpress-plugins
Description: Create, and manage a new type of content, the events, also setup a shortcodes for event calendar and a list year-month-day of events.
Version: 1.0
Author: Diego Jesus Hincapie Espinal
Author URI: http://www.weabers.com
License:
Create, and manage a new type of content, the events, also setup a shortcodes for event calendar and a list year-month-day of events.
Copyright (C) 2011  Diego Jesus Hincapie Espinal

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
    die('You are not allowed to call this page directly.');
}


    class WEeventscalendar
    {
        public function __construct()
        {
            global $wp_scripts; 
            
            add_action( 'init', array($this, 'create_post_type' ));
            
            if (is_admin())
            {
					
				add_action('add_meta_boxes', array($this, 'add_date_metaboxes'));
				add_action('save_post', array($this, 'savedates'));
            }
				
            add_shortcode('weecdatepicker', array($this, 'weecdatepicker_function'));
            
            wp_enqueue_script('jquery');
            
            wp_register_script('jquerydpt', plugins_url( 'js/jquery.ui.datepicker-'.WPLANG.'.js' , __FILE__ ));
            
            wp_enqueue_script('jquerydpt');
            
            $scripts = array('jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-datepicker');
            
            foreach($scripts as $script)
            {
                wp_enqueue_script($script);
            }
            
            $protocol = is_ssl() ? 'https' : 'http';
            $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
            wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
            
            wp_register_style( 'jqueryuicss', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
            wp_enqueue_style( 'jqueryuicss');
            wp_register_style('weeccss', plugins_url( 'css/weecmain.css' , __FILE__ ));
            wp_enqueue_style('weeccss');
        }
		
		function create_post_type() 
		{
                        load_plugin_textdomain( 'WEeventscalendar', false, dirname( plugin_basename( __FILE__ ) ).'/');
			register_post_type( 'eventswe', array('labels' => array('name' => __( 'Events', 'WEeventscalendar' ), 'singular_name' => __( 'Event', 'WEeventscalendar' ), 'add_new' => __('New Event', 'WEeventscalendar'), 'edit_item' => __('Edit event', 'WEeventscalendar'), 'menu_name' => __( 'Events', 'WEeventscalendar' ), 'add_new_item' => __('New event', 'WEeventscalendar')), 'public' => true));
		}
		
		public function weecdatepicker_function()
		{
            $cadena .= '<div id="weeccontainer">';
            $cadena .= '<div id="weeceventlist">';

            if(isset($_POST['datehidden']))
            {
                $cadena .= $this -> eventlist($_POST['datehidden']);
            }
            else
            {
                $cadena .= __('Please, select a date', 'WEeventscalendar');
            }

            $cadena .= '</div>';
            $cadena .= '<div id="weecdatepicker"></div>';
			
			ob_start();
			include('views/calendar.php');
    	    $cadena .= ob_get_clean();
			
            $cadena .= '</div>';
            return $cadena;
		}
		
		protected function eventList($date)
		{
			$cadena = '';
			
            global $wpdb;

			$query = "SELECT ".$wpdb->prefix."posts.ID, ".$wpdb->prefix."posts.post_title, ".$wpdb->prefix."posts.post_content, ".$wpdb->prefix."posts.post_excerpt, ".$wpdb->prefix."posts.guid FROM ".$wpdb->prefix."posts, ".$wpdb->prefix."postmeta WHERE meta_key='from_datefield' AND meta_value<='".$date."' AND post_id IN (SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key='to_datefield' AND meta_value>='".$date."') AND post_id=ID AND post_status='publish' AND post_type='eventswe'";
			
            $events = $wpdb->get_results($query, OBJECT);
			
			$eventsa = array();
			
			foreach($events as $key => $item)
			{
				$query = "SELECT ".$wpdb->prefix."postmeta.* FROM ".$wpdb->prefix."postmeta WHERE post_id=".$item->ID." AND (meta_key='from_datefield' OR meta_key='to_datefield')";
			
	            $eventdate = $wpdb->get_results($query, OBJECT);	
				
				$eventsa[$key] = array('id' => $item->ID, 'title' => $item->post_title, 'content' => $item->post_content, 'excerpt' => $item->post_excerpt, 'guid' => $item->guid);
				
				foreach($eventdate as $key2=> $item2)
				{
					$eventsa[$key][$item2->meta_key] = $item2->meta_value;
				}
			}
			
			ob_start();
			include('views/list.php');
    	    $cadena .= ob_get_clean();
			
//            $cadena .= '</div>';
            return $cadena;
		}
		
		public function add_date_metaboxes()
		{	
			add_meta_box('wp_event_date_we', __('Range date', 'WEeventscalendar'), array($this, 'datefield'), 'eventswe', 'side');
		}
		
		public function datefield()
		{
			global $post;
			
			$from_datefield = get_post_meta($post->ID, 'from_datefield', true);
			
			$to_datefield = get_post_meta($post->ID, 'to_datefield', true);
			
			if($from_datefield==='')
			{
				$from_datefield = date('Y-m-d');
			}
			
			if($to_datefield==='')
			{
				$to_datefield = date('Y-m-d');
			}
			
			include('views/datefield.php');
		}
		
		public function savedates($post_id)
		{
			global $post;
			if( $post->post_type != 'revision' )
			{
                            update_post_meta($post_id, 'from_datefield', $_POST['from_datefield']);
                            update_post_meta($post_id, 'to_datefield', $_POST['to_datefield']);
			}
		}
    }

    $weeventscalendar = new WEeventscalendar();



        function weec_excerpt($text, $excerpt = false)
        {
            if ($excerpt) return $excerpt;

            $text = strip_shortcodes( $text );

            //$text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = strip_tags($text);
            $excerpt_length = apply_filters('excerpt_length', 55);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
            $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if ( count($words) > $excerpt_length ) {
                    array_pop($words);
                    $text = implode(' ', $words);
                    $text = $text . $excerpt_more;
            } else {
                    $text = implode(' ', $words);
            }

            return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
        }

?>