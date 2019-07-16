<?php
/**
 * Calling W.ORG API Response.
 *
 * @package WP plugins Active Stats
 * @author Brainstorm Force
 */

/**
 * Helper class for the ActiveCampaign API.
 *
 * @since 1.0.0
 */
class WP_plugin_Stats_Api {
	/**
	 * Constructor calling W.ORG API Response.
	 */
	function __construct() {
		add_shortcode( 'adv_stats_name', array( $this, 'bsf_display_plugin_name' ) );
		add_shortcode( 'adv_stats_active_install', array( $this, 'bsf_display_plugin_active_installs' ) );
		add_shortcode( 'adv_stats_version', array( $this, 'bsf_display_plugin__version' ) );
		add_shortcode( 'adv_stats_ratings', array( $this, 'bsf_display_plugin__ratings' ) );
		add_shortcode( 'adv_stats_ratings_5star', array( $this,'bsf_display_plugin__fiveStar_ratings' ) );
		add_shortcode( 'adv_stats_ratings_average', array( $this, 'bsf_display_plugin__average_ratings' ) );
		add_shortcode( 'adv_stats_downloads', array( $this,'bsf_display_plugin__totaldownloads' ) );
		add_shortcode( 'adv_stats_last_updated', array( $this, 'bsf_display_plugin__lastupdated' ) );
		add_shortcode( 'adv_stats_download_link', array( $this, 'bsf_display_plugin__downloadlink' ) );
		add_shortcode( 'adv_stats_downloads_counts', array( $this, 'bsf_display_download_count'));
		add_shortcode( 'adv_stats_total_active', array($this, 'bsf_display_Active_installs' ) );
	}
	/**
	 * Get the plugin Details.
	 *
	 * @param int $action Get attributes plugin Details.
	 * @param int $api_params Get attributes plugin Details.
	 */
	function bsf_plugin_get_text( $action, $api_params = array() ){
		$plugin_slug = isset( $api_params['plugin'] ) ? $api_params['plugin'] : '';
		$frequency  = get_option('wp_info');
		$second = 0;
		$day = 0;
		if(!empty($frequency['Frequency'])) {
			$day    = (($frequency['Frequency'] *24)*60)*60;
			$second = ( $second + $day );
		}
			$args = (object) array( 'slug' =>$plugin_slug,'fields' => array( 'active_installs' => true,));
		    $response = wp_remote_post(
			    'http://api.wordpress.org/plugins/info/1.0/',
			    array(
			        'body' => array(
			            'action'  => 'plugin_information',
			            'request' => serialize((object)$args)
			        )
			    )
			);
		$wp_plugin = unserialize(wp_remote_retrieve_body($response));
		$slug ='bsf_tr_plugin_info_' . $plugin_slug;
		$plugin = get_site_transient($slug);
			if ( false === $plugin || empty($plugin) )
			{
				$second = (!empty($second) ? $second : 86400 );
				set_site_transient( $slug , $wp_plugin ,$second );
			}
			if ( empty( $plugin ) ) {
			 	return '';
			 }
			
		return $plugin;
	}
		function bsf_display_plugin_name( $atts ) {
		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$update_option = array( 
			'wp_plugin_slug' => (!empty($atts['wp_plugin_slug']) ? $atts['wp_plugin_slug'] : '' ),
		);
		update_option('wp_plugin_info', $update_option);
		if ( '' == $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'name' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->name;

		}
	function bsf_display_plugin_active_installs( $atts ) {
		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$update_option = array( 
			'wp_plugin_slug' => (!empty($atts['wp_plugin_slug']) ? $atts['wp_plugin_slug'] : '' ),
		);
		update_option('wp_plugin_info', $update_option);
		if ( '' == $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'active_installs' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->active_installs;

		}
	function bsf_display_plugin__version( $atts ) {
		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'active_installs' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->version;

		}
	function bsf_display_plugin__ratings( $atts ) {

		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'num_ratings' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->num_ratings;
		
		}
	function bsf_display_plugin__fiveStar_ratings( $atts) {

		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'ratings' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
			
			// wp_die();
		return $plugin->ratings[5];
			//return $outof;
		}
	function bsf_display_plugin__average_ratings( $atts ) {
		//var_dump($atts['outof']);//Error
		
		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
				'outof' => isset( $atts['outof'] ) ? $atts['outof'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$outof = $atts['outof'];
			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'rating' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
			// var_dump($outof);
			// wp_die();
			if(is_numeric($outof)|| empty($outof))
			{
				$outof = (!empty($outof) ? $outof : 100 );
				$outof = ( ($plugin->rating) / 100 ) * $outof ;
				// var_dump($atts['outof']);
				return $outof;
			}
			else
			{
				return "Out Of Value Must Be Nummeric!";
			}
		}
	function bsf_display_plugin__totaldownloads( $atts ) {

		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'rating' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->downloaded;
		
		}
	function bsf_display_plugin__lastupdated( $atts ) {
		$dateformat  = get_option('wp_info');
		// var_dump($dateformat);
		// wp_die();
		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'last_updated' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
			// $originalDate = "2010-03-21";

			$dateformat['Choice'] = (!empty($dateformat['Choice']) ? $dateformat['Choice'] : 'Y-m-d' );
			//(!empty($second) ? $second : 86400 );
				 //var_dump($dateformat['Choice']);
		// wp_die();
			$newDate = date($dateformat['Choice'], strtotime($plugin->last_updated));
		return $newDate;
		
		}
	function bsf_display_plugin__downloadlink( $atts ,$label ) {
		//$label= $label;
		$atts = shortcode_atts(
			array(
				'plugin'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
				'label' => isset( $atts['label'] ) ? $atts['label'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$wp_plugin_label  = $atts['label'];

			if ( '' == $wp_plugin_slug ) {
				return 'Please Verify plugin Details!';
			}
			if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
				$api_params = array(
					'plugin'    => $wp_plugin_slug,
					'author'   => $wp_plugin_author,
					'per_page' => 1,
					'fields'   => array(
						'homepage'        => false,
						'description'     => false,
						'screenshot_url'  => false,
						'rating' => true,
					),
				);

				$plugin = $this->bsf_plugin_get_text( 'plugin_information',$api_params);
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
			$label = (!empty($wp_plugin_label) ? esc_attr($wp_plugin_label) : esc_url($plugin->download_link) );
		
		return '<a href="'.esc_url($plugin->download_link).'" target="_blank">'.$label.'</a>';		
		}
	function bsf_display_plugins_active_count($action,$api_params=array()){
		$frequency  = get_option('wp_info');
		$second = 0;
		$day = 0;
		if(!empty($frequency['Frequency'])) {
			$day    = (($frequency['Frequency'] *24)*60)*60;
			$second = ( $second + $day );
		}
		$args =(object) $args = array('author' =>$api_params,'fields' => array( 'active_installs' => true,));
		$url= 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post($url,array('body' => array('action' => 'query_plugins',
			'request' => serialize((object)$args))));
		
		if($api_params === ''){
			// Response body does not contain an object/array
		        return "Error! missing Plugin Author";
		}
		else{
		if ( !is_wp_error($response) ) {
		    $returned_object = unserialize(wp_remote_retrieve_body($response));
		     $plugins = $returned_object->plugins;
		     	$temp = 0;
		        	// $t = 0;
		        	foreach ($plugins as $key) 
		        	{
		        		$temp = $temp + $key->active_installs;
		        	}
		        	
		    $author ='bsf_tr_plugin_Active_Count_' .$api_params;
			$plugins = get_site_transient($author);


		    if ( false === $plugins || empty($plugins) ) {
	
		    $second = (!empty($second) ? $second : 86400 );
			set_site_transient( $author , $temp ,$second );

	   		}
	   		if ( empty( $plugins ) ) {
			 	return '';
			 }
			 return $plugins;
		 }	
		}
		
  	}
  	function bsf_display_Active_installs( $atts ){
  		$atts = shortcode_atts(
			array(
				'plugin_author'    => isset( $atts['author'] ) ? $atts['author'] : '',
			), $atts
		);
		$wp_plugin_author = $atts['plugin_author'];
		$api_params = array(
				
				'plugin_author'   => $wp_plugin_author,
				'per_page' => 1,
			);
		$plugins = $this->bsf_display_plugins_active_count( 'query_plugins',$api_params['plugin_author']);
		if ( false == $plugins ) 
		{
					return 'Please Verify plugin Author!';
		}
		// setlocale(LC_MONETARY, 'en_US.UTF-8');
		// $plugins = money_format('%.2n', $plugins);
		// var_dump($plugins);
		// wp_die();

		return number_format($plugins);
		// $number_format = $this->number_format_unchanged_precision($plugins);
		// return $number_format;
  	}
  	function bsf_display_total_plugin_download_count($action,$api_params=array()){
		$frequency  = get_option('wp_info');
		$second = 0;
		$day = 0;
		if(!empty($frequency['Frequency'])) {
			$day    = (($frequency['Frequency'] *24)*60)*60;
			$second = ( $second + $day );
		}
		$args =(object) $args = array('author' =>$api_params,'fields' => array( 'active_installs' => true,));
		$url= 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post($url,array('body' => array('action' => 'query_plugins',
			'request' => serialize((object)$args))));
		if($api_params === ''){
			// Response body does not contain an object/array
		        return "Error! missing Plugin Author";
		}
		else{
		if ( !is_wp_error($response) ) {
		    $returned_object = unserialize(wp_remote_retrieve_body($response));
		     $plugins = $returned_object->plugins;
		     	$temp = 0;
		        	
		        	foreach ($plugins as $key) 
		        	{
		        		$temp = $temp + $key->downloaded;
		        	}
		        	
		    $author ='bsf_tr_plugin_downloads_Count_' .$api_params;
			$plugins = get_site_transient($author);
		
		    if ( false === $plugins || empty($plugins) ) {

		    $second = (!empty($second) ? $second : 86400 );
			set_site_transient( $author , $temp ,$second );

	   		}
	   		if ( empty( $plugins ) ) {
			 	return '';
			 }
			 return $plugins;
		 }	
		}
		
  	}
  	function bsf_display_download_count( $atts ){
  		$atts = shortcode_atts(
			array(
				'plugin_author'    => isset( $atts['author'] ) ? $atts['author'] : '',
			), $atts
		);
		$wp_plugin_author = $atts['plugin_author'];
		$api_params = array(
				
				'plugin_author'   => $wp_plugin_author,
				'per_page' => 1,
			);
		$plugins = $this->bsf_display_total_plugin_download_count( 'query_plugins',$api_params['plugin_author']);
		if ( false == $plugins )
		{
					return 'Please Verify plugin Author!';
		}
		return number_format($plugins);
  	}
}
new WP_plugin_Stats_Api();