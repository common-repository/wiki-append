<?php
/*
Plugin Name: Wiki Append
Plugin URI: 
Description: enables the inclusion of mediawiki pages into your own blog page or post.
Version: 1
Author: OLT UBC
Author URI: http://blogs.ubc.ca/oltdev
*/
 
/*
== Installation ==
 
1. Upload WikiInc.zip to the /wp-content/plugins/WikiInc/WikiInc.php directory
2. Unzip into its own folder /wp-content/plugins/
3. Activate the plugin through the 'Plugins' menu in WordPress by clicking "WikiInc"
4. Go to your Options Panel and open the "WikiInc" submenu. /wp-admin/options-general.php?page=WikiInc.php
*/
 
/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| WikiInc - append mediawiki page into your post or page                                        |
| Copyright (C) 2008, OLT, www.olt.ubc.com                   	     |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/



/**
 * Creation of the WikiIncClass
 * This class should host all the functionality that the plugin requires.
 */
if (!class_exists("WikiIncClass")) {

	class WikiIncClass {
		/**
		 * Global Class Variables
		 */

		var $optionsName = "WikiIncOptions";

		
		function saveMeta($post_ID)
		{
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
		
			
			if ( 'page' == $_POST['post_type'] ) {
				if ( !current_user_can( 'edit_page', $post_id ))
				return $post_ID;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ))
				return $post_ID;
			}
			
			 	
			if( !wp_verify_nonce($_POST['wiki-inc-verify-key'], 'wiki-inc-verify-key') )
				return $post_ID;
				
			
			$field = $_POST['wiki_inc_page'];
		
			if ( $field == null )
				return;
			
			// add the url to the custom field
			add_post_meta($post_ID, '_wiki_page', $field, true) or update_post_meta($post_ID, '_wiki_page', $field);
			
			
			
			// set the default 
			if($_POST['wiki_inc_default'] == 1) {
		
				$option_name = 'wiki_ink_base' ; 
		
				if ( get_option( $option_name ) ) {
					update_option( $option_name, $field );
				
				} else {
					$deprecated='';
					add_option( $option_name, $field, $deprecated );
				}
			}
		
			return $post_ID;
		}
	
		
		function addContent( $content = '' ) 
		{
			include_once( "resources/scrape.php" );
			global $post, $id;
			
			
			// includes the wiki content based on the key work
			$page = get_post_meta( $id, "_wiki_page", "true");
			
			// for backwards compatibility sake 
			$base = get_post_meta( $id, "_wiki_base", "true" );
			 
			if( $base == "" ) $base = get_option( "wiki_ink_base" );
 
			if( $page!="" && $base != "" )
			{
				$url = str_replace("Main_Page", $title, $base );
				 
				if( $base == $url)
					$url = str_replace("index.php", "index.php?title=".$title, $base );
					$urlA = @parse_url($url);

				if ( !$urlA ) 
					return $content;
				
				$page = $url;
			}
			// end of backwards compatability 
			
			
			
			// only show up on single or pages 
			if(is_single() || is_page())
			{
				
				if($page != "") { 
					$h = new http();
					// this is the only magic part
					if (!$h->fetch($page."?action=render")) 
						$body = "<div id='wiki-inc-content'><p >Sorry but <a href='".$page."' >".$page."</a> could not be appended to your page</p></div>";
					
					
					if($h->body)
						$body = "<div id='wiki-append-content'>".$h->body."</div>";
						
					// we might have to stip the content if you want to see just the excerpt
					$content .= $body;
				}
				return $content;
			
			} else 
				return $content;
			
		}
		
		function addFormOld()
		{
			echo "Sorry Wiki Inc in not avalable in this version of Wordpress";
		}
		
		function addForm()
		{ 
			global $post;
			
			$page 		= get_post_meta($post->ID, '_wiki_page',true);
			
			
			// for backwards compatibility sake 
			$base = get_post_meta( $id, "_wiki_base", "true" );
			 
			if( $base == "" ) $base = get_option( "wiki_ink_base" );
 
			if( $page!="" && $base != "" )
			{
				$url = str_replace("Main_Page", $title, $base );
				 
				if( $base == $url)
					$url = str_replace("index.php", "index.php?title=".$title, $base );
					$urlA = @parse_url($url);

				if ( !$urlA ) 
					return $content;
				
				$page = $url;
			}
			// end of backwards compatability 
			
			
			?>
			
			<div id="postwiki-inc">
			<p>
				<strong>
                	<label for="wiki_inc_page">URL:</label></strong> 
					<input type="text" name="wiki_inc_page" class="tags-input" id="wiki_inc_page" style="width:80%" value="<?php if($page != '') { echo $page; } ?>" /> <br />
					<input type="hidden" name="wiki-inc-verify-key" id="wiki-inc-verify-key" value="<?php echo  wp_create_nonce('wiki-inc-verify-key') ;?>" />
				<span>Url of the <a href="http://en.wikipedia.org/wiki/Mediawiki" target="_blank">MediaWiki</a> page that you want include. example. http://en.wikipedia.org/wiki/Wordpress</span>
			</p>
			
			</div>
			
			<?		
		}
		

	} 
}





/**
 * Initialize the admin panel function 
 */

if (!function_exists("WikiIncPluginSeries_ap")) {

	function WikiIncPluginSeries_ap() {

		global $WikiIncInstance;

		if (!isset($WikiIncInstance)) 
			return;
		
		if( function_exists( 'add_meta_box' )) 
		{
			add_meta_box( 'wiki_inc_id', __( 'Append Wiki Page', 'wiki_inc_textdomain' ),  array( &$WikiIncInstance, 'addForm' ), 'post', 'advanced' );
			add_meta_box( 'wiki_inc_id', __( 'Append Wiki Page', 'wiki_inc_textdomain' ),  array( &$WikiIncInstance, 'addForm' ), 'page', 'advanced' );
		} else 
		{
			add_action('dbx_post_advanced', array(&$WikiIncInstance, 'addFormOld'));
			add_action('dbx_page_advanced', array(&$WikiIncInstance, 'addFormOld'));
		}
	}	
} 


if (class_exists("WikiIncClass")) {

	$WikiIncInstance = new WikiIncClass();

}


/**
  * Set Actions and Filters
  */

if (isset($WikiIncInstance)) {
    // load the textdomain None set yet
	
	//Actions

	add_action( 'admin_menu', 'WikiIncPluginSeries_ap' );
	add_action('save_post', array(&$WikiIncInstance, 'saveMeta'),101);
	add_action( 'edit_post', array(&$WikiIncInstance, 'saveMeta'), 100 );
	add_action( 'publish_post', array(&$WikiIncInstance, 'saveMeta'), 101 );

	
	//Filters
	add_filter( 'the_content', array(&$WikiIncInstance, 'addContent'), 1 ); 

}
?>