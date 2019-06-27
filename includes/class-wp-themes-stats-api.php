<?php
/**
 * Calling W.ORG API Response.
 *
 * @package WP Themes Active Stats
 * @author Brainstorm Force
 */

/**
 * Helper class for the ActiveCampaign API.
 *
 * @since 1.0.0
 */
class WP_Themes_Stats_Api {
	/**
	 * Constructor calling W.ORG API Response.
	 */
	function __construct() {
		add_shortcode( 'wp_theme_active_install', array( $this, 'bsf_display_active_installs' ) );
		add_shortcode( 'wp_plugin_active_install', array( $this, 'bsf_display_plugin_active_installs' ) );
	}
	/**
	 * Display Theme Details.
	 *
	 * @param int $atts Get attributes theme_slug and theme_author.
	 */
	function bsf_display_active_installs( $atts ) {
		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		// $active_installs = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		//var_dump($wp_theme_slug);
		$args = array(
		    'slug' => $wp_theme_slug,
		    'fields' => array( 'active_installs' => true,'screenshot_url'=> true,'versions'=> true,'rating'=> true,'download_link'=> true )
		);
		 
		// Make request and extract plug-in object
		$response = wp_remote_post(
		    'http://api.wordpress.org/themes/info/1.0/?action=theme_information&request[fields][ratings]=true',
		    array(
		        'body' => array(
		            'action' => 'theme_information',
		            'request' => serialize((object)$args)
		        )
		    )
		);

		 
		if ( !is_wp_error($response) ) {
		    $theme = unserialize(wp_remote_retrieve_body($response));
		    if ( !is_object($theme) && !is_array($theme) ) {
		        // Response body does not contain an object/array
		        echo "An error has occurred";
		    }
		}
		else {
		    // Error object returned
		    echo "An error has occurred";
		}
		// var_dump($theme);
		// echo "<pre>";
		// 	print_r($theme);
		// echo "</pre>";

		$out = '<li><strong>' . esc_html( $theme->name ) . '</strong> by <strong>' . esc_html( $wp_theme_author ) . '</strong> &nbsp; ';
		$out  .= '<br> Current Version : &nbsp;' .esc_attr( $theme->version ). '&nbsp;</br>';
	    $out .= '<br>  Active installs : &nbsp; ' .esc_attr($theme->active_installs). '&nbsp; </br>';
	    $out .=  '<br> Ratings : &nbsp' .esc_attr( $theme->rating ). '%&nbsp;</br>';
	    $out .=  '<br> Total Downloaded : &nbsp' .esc_attr( $theme->downloaded ). '&nbsp;</br>'; 
	    $out .= '<br> Last Updated On : &nbsp' .esc_attr($theme->last_updated).'&nbsp;</br>';
	    $out .= '<br> Download Link : &nbsp (<a href="' .esc_url($theme->download_link).'" target="_blank">'.$wp_theme_slug.'</a>)&nbsp;</br>';
	    $out .= '<br> Screenshot : &nbsp<img alt="" src="' .esc_url($theme->screenshot_url).'"width="172" height="129" &nbsp;</br>';
	  
	    $out .= '</li>';
	    return $out;
		return $active_installs;
	}
	/**
	 * Display Plugin Details.
	 *
	 * @param int $atts Get attributes plugin_slug and plugin_author.
	 */
	function bsf_display_plugin_active_installs($atts){
		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);

		$wp_plugin_slug   = $atts['wp_plugin_slug'];
		$wp_plugin_author = $atts['plugin_author'];
		//var_dump($wp_plugin_author);

			$args = (object) array( 'slug' => $wp_plugin_slug );
 
		    $request = array( 'action' => 'plugin_information', 'timeout' => 15, 'request' => serialize( $args) );
		 
		    $url = 'http://api.wordpress.org/plugins/info/1.0/';
		 
		    $response = wp_remote_post( $url, array( 'body' => $request ) );
	
		    $plugin_info = unserialize( $response['body'] );
		 
		 //echo '<pre>' . print_r( $plugin_info, true ) . '</pre>';
	 			
	   $out = '<li><strong>' . esc_html( $plugin_info->name) . '</strong> by <strong>' . __( $wp_plugin_author ) . '</strong> &nbsp; ';
	   $out  .= '<br> Current Version : &nbsp;' .esc_attr( $plugin_info->version ). '&nbsp;</br>';
	   $out .=  '<br> Total 5 Star ratings : &nbsp' .esc_attr( $plugin_info->ratings[5] ). '&nbsp;</br>';
	   $out .=  '<br> Total Downloaded : &nbsp' .esc_attr( $plugin_info->downloaded ). '&nbsp;</br>'; 
	   $out .= '<br> Last Updated On : &nbsp' .esc_attr($plugin_info->last_updated).'&nbsp;</br>';
	   $out .= '<br> Download Link : &nbsp (<a href="' .esc_url($plugin_info->download_link).'" target="_blank">'.$wp_plugin_slug.'</a>)&nbsp;</br>';
	   $out .= '</li>';
	    return $out;
	}
}


new WP_Themes_Stats_Api();
