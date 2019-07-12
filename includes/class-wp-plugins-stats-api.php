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
		add_shortcode( 'wp_plugin_active_install', array( $this, 'bsf_display_plugin_active_installs' ) );
		add_shortcode( 'wp_plugin_version', array( $this, 'bsf_display_plugin__version' ) );
		add_shortcode( 'wp_plugin_ratings', array( $this, 'bsf_display_plugin__ratings' ) );
		add_shortcode( 'wp_plugin_5starrate', array( $this,'bsf_display_plugin__fiveStar_ratings' ) );
		add_shortcode( 'wp_plugin_averagerate', array( $this, 'bsf_display_plugin__average_ratings' ) );
		add_shortcode( 'wp_plugin_totaldownloads', array( $this,'bsf_display_plugin__totaldownloads' ) );
		add_shortcode( 'wp_plugin_lastupdated', array( $this, 'bsf_display_plugin__lastupdated' ) );
		add_shortcode( 'wp_plugin_downloadlink', array( $this, 'bsf_display_plugin__downloadlink' ) );
		// echo "jndfjnd";
		// wp_die();
	}
	/**
	 * Get the plugin Details.
	 *
	 * @param int $action Get attributes plugin Details.
	 * @param int $api_params Get attributes plugin Details.
	 */
	function bsf_plugin_fetch_data(){
		$args = (object) array( 'slug' => 'astra-hooks' ,'fields' => array( 'active_installs' => true,));
		    $request = array( 'action' => 'plugin_information', 'timeout' => 15, 'request' => serialize( $args) );
		    $url = 'http://api.wordpress.org/plugins/info/1.0/';
		    $response = wp_remote_post( $url, array( 'body' => $request ) );
		    $plugin_info = unserialize( $response['body'] );
		    return $plugin_info;  
	}
	function bsf_plugin_get_text( $action, $api_params = array() ){
		//$wp_plugin_info = get_option('wp_plugin_info');
		// var_dump($wp_plugin_info['wp_plugin_slug']);
		// wp_die();
		$plugin_slug = isset( $api_params['plugin'] ) ? $api_params['plugin'] : '';
		$frequency  = get_option('wp_info');

		$second = 0;
		$day = 0;
		if(!empty($frequency['Frequency'])) {
			$day    = (($frequency['Frequency'] *24)*60)*60;
			$second = ( $second + $day );
		}
		//$wp_plugin_info['wp_plugin_slug']
			$args = (object) array( 'slug' =>'astra-hooks'  ,'fields' => array( 'active_installs' => true,));
		    $response = wp_remote_post(
			    'http://api.wordpress.org/plugins/info/1.0/',
			    array(
			        'body' => array(
			            'action'  => 'plugin_information',
			            'request' => serialize((object)$args)
			        )
			    )
			);
		$plugin = unserialize(wp_remote_retrieve_body($response));
		$plugin = get_site_transient( 'bsf_tr_plugin_info' );
		// var_dump($plugin);
		// wp_die();
			if ( false === $plugin ) 
			{
					$data = (!empty($plugin) ? $plugin : '' );
					set_site_transient( 'bsf_tr_plugin_info', $this->bsf_plugin_fetch_data() ,$second );
			}

			if ( empty( $plugin ) ) {
			 	return '';
			 }
		return $plugin;

	}
	function bsf_display_plugin_active_installs( $atts ) {
		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				// var_dump($plugin);
				// wp_die();
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
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				//var_dump($version->version);
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
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				//var_dump($version->version);
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->num_ratings;
		
		}
		function bsf_display_plugin__fiveStar_ratings( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				//var_dump($plugin->ratings->{5});
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->ratings->{5};
		}
		function bsf_display_plugin__average_ratings( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				//var_dump($version->version);
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->rating;
		
		}
		function bsf_display_plugin__totaldownloads( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				//var_dump($version->version);
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->downloaded;
		
		}
		function bsf_display_plugin__lastupdated( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				//var_dump($version->version);
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return $plugin->last_updated;
		
		}
		function bsf_display_plugin__downloadlink( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_plugin_slug'   => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			), $atts
		);
		$wp_plugin_slug   = $atts['wp_plugin_slug'];
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
				// var_dump($plugin);
				// return if we get false response.
				if ( false == $plugin ) {
					return 'Please Verify plugin Details!';
				}
			}
		return '<a href="'.esc_url($plugin->download_link).'" target="_blank">'.esc_url($plugin->download_link).'</a>';

		
		}
}
new WP_plugin_Stats_Api();