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
		add_shortcode( 'wp_plugin_active_install', array( $this,'bsf_display_plugin_active_installs' ) );
		add_shortcode( 'wp_plugin_by_author', array($this, 'bsf_display_plugin_by_author' ) );
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
		//Code To fetch todays count
			$url= file_get_contents('http://api.wordpress.org/stats/themes/1.0/downloads.php?slug={'.$wp_theme_slug.'}&limit=1');
			$arr1 = json_decode($url);
			//return 'Todays Downloads:'.esc_html($arr1->{date('Y-m-d')}).'&nbsp;';
		//--------------------------

		//var_dump($arr1);
		$args = array(
		    'slug' => $wp_theme_slug,
		    'fields' => array( 'active_installs' => true,'screenshot_url'=> true,'versions'=> true,'ratings'=> true,'download_link'=> true )
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
 		if ($wp_theme_slug === '')
 		{
 			//error when slug is empty
 			return "Error! Theme Slug is missing";
 		}
 		elseif ($wp_theme_author === '') {
 			//error when author is empty
 			return "Error! Theme Author is missing";
 		}
 		else{
				if ( !is_wp_error($response) ) {
				    $theme = unserialize(wp_remote_retrieve_body($response));
				    if ( !is_object($theme) && !is_array($theme) ) {
				        // Response body does not contain an object/array
				        return "An error has occurred";
				    }
				}
				else {
				    // Error object returned
				   return "An error has occurred";
				}
		return '<div class="bsfresp-table">
	   			<div class="bsfresp-table-caption">THEME INFORMATION</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
							Name
						</div>
						<div class="bsftable-body-cell">&nbsp;
								'. esc_attr( $theme->name) .'&nbsp;
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Company
						</div>
						<div class="bsftable-body-cell">
								&nbsp;'. strip_tags( $theme->author ) .' 
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Version
						</div>
						<div class="bsftable-body-cell">&nbsp;
								' .esc_attr( $theme->version ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Active installs
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr($theme->active_installs).'
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						5 Star Ratings
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $theme->ratings[5] ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Total Ratings
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $theme->num_ratings ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Today`s Download
						</div>
						<div class="bsftable-body-cell">&nbsp;
								'.esc_attr($arr1->{date('Y-m-d')}).'
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Total Downloads
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $theme->downloaded ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Updated on
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr($theme->last_updated). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Download
						</div>
						<div class="bsftable-body-cell">
								&nbsp;<a href="' .esc_url($theme->download_link).'" target="_blank">'.esc_attr($theme->name).'</a>
						</div>
					</div>
				</div>';
			}
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
		//Code To fetch todays count
			$url= file_get_contents('http://api.wordpress.org/stats/plugin/1.0/downloads.php?slug={'.$wp_plugin_slug.'}&limit=1');
			$arr = json_decode($url);
			//var_dump($arr); 
			//return 'Todays Downloads:'.esc_html($arr->{date('Y-m-d')}).'&nbsp;';
		//--------------------------

			$args = (object) array( 'slug' => $wp_plugin_slug ,'fields' => array( 'active_installs' => true,));
		    $request = array( 'action' => 'plugin_information', 'timeout' => 15, 'request' => serialize( $args) );
		    $url = 'http://api.wordpress.org/plugins/info/1.0/';
		    $response = wp_remote_post( $url, array( 'body' => $request ) );
		    $plugin_info = unserialize( $response['body'] );

			    if ($wp_plugin_slug === ''){
		 			//error when slug is empty
		 			return "Error! Plugin Slug is missing";
		 			}
		 		elseif ($wp_plugin_author === ''){
		 			//error when author is empty
		 			return "Error! Plugin Author is missing";
		 			}
		 		else{
			 		if ( !is_wp_error($response) ) {
					    $plugin_info = unserialize(wp_remote_retrieve_body($response));
					    if ( !is_object($plugin_info) && !is_array($plugin_info) ) {
					        // Response body does not contain an object/array
					        echo "An error has occurred";
					    }
		    return '<div class="bsfresp-table">
	   				<div class=" bsfresp-table-caption">PLUGIN INFORMATION</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
							Name
						</div>
						<div class="bsftable-body-cell">&nbsp;
								'. esc_attr( $plugin_info->name) .'
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Company
						</div>
						<div class="bsftable-body-cell">
								&nbsp; '. strip_tags($wp_plugin_author) .' 
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Version
						</div>
						<div class="bsftable-body-cell">&nbsp;
								' .esc_attr( $plugin_info->version ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Active installs
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr($plugin_info->active_installs).'
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						5 Star Ratings
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $plugin_info->ratings[5] ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Total Ratings
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $plugin_info->num_ratings ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Today`s Download
						</div>
						<div class="bsftable-body-cell">&nbsp;
								'.esc_attr($arr->{date('Y-m-d')}).'
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Total Downloads
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $plugin_info->downloaded ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Updated on
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr($plugin_info->last_updated). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Download
						</div>
						<div class="bsftable-body-cell">
								&nbsp;<a href="' .esc_url($plugin_info->download_link).'" target="_blank">'.esc_attr($wp_plugin_slug).'</a>
						</div>
					</div>
				</div>';

					}
					else {
					    // Error object returned
					    echo "An error has occurred";
					}
				}
					
	}
	/**
	 * Display Plugin Details by particular author.
	 *
	 * @param int $atts Get attribute plugin_author.
	 */
	function bsf_display_plugin_by_author($atts){
		$atts = shortcode_atts(
			array(
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_author = $atts['plugin_author'];
		$args =(object) $args = array('author' => $wp_plugin_author,'fields' => array( 'active_installs' => true,));
		$url= 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post($url,array('body' => array('action' => 'query_plugins',
			'request' => serialize((object)$args))));
		// var_dump($response);
		if($wp_plugin_author === ''){
			// Response body does not contain an object/array
		        echo "Error! missing Plugin Author";
		}
		else{
		if ( !is_wp_error($response) ) {
		    $returned_object = unserialize(wp_remote_retrieve_body($response));
		    $plugins = $returned_object->plugins;
		     //var_dump($plugins);
		    if ( !is_array($plugins) ) {
		        // Response body does not contain an object/array
		        echo "An error has occurred";
		    }
		    else {
		        // Display a list of the plug-ins and other information
  
		        if ( $plugins ) {
		        	$temp = '';
		        	foreach ( $plugins as $plugin )  {
		        		$temp.='<li><strong>'.esc_html($plugin->name).'</strong> <br> Current Version : &nbsp;'.esc_attr($plugin->version).'&nbsp;<br>Total 5 Star ratings : &nbsp;'.esc_attr( $plugin->rating ).'&nbsp;<br>Total Downloaded : &nbsp; '.esc_html($plugin->downloaded).'&nbsp; times<br> Last Updated On : &nbsp;'.esc_attr($plugin->last_updated).'&nbsp;<br> Download link : &nbsp;<a href="'.esc_html($plugin->download_link).'" target="_blank">'.$plugin->name.'</a>&nbsp;<br></li>';
		        	}
		        	  return $temp;
		        	}
		   		 }
		}
			else {
			    // Error object returned
			    return "An error has occurred";
			}
		}	
	}
	
 }
new WP_Themes_Stats_Api();
