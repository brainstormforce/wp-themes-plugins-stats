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
//require_once(WP_THEMES_STATS_BASE_DIR.'includes/class-wp-plugins-stats-api.php');

class WP_Themes_Stats_Api {
	/**
	 * Constructor calling W.ORG API Response.
	 */
	function __construct() {
		add_shortcode( 'wp_theme_active_install', array( $this, 'bsf_display_active_installs' ) );
		add_shortcode( 'wp_theme_version', array( $this, 'bsf_display_theme_version' ) );
		add_shortcode( 'wp_theme_ratings', array( $this, 'bsf_display_theme_ratings' ) );
		add_shortcode( 'wp_theme_5starRate', array( $this, 'bsf_display_theme_FiveStarRatings' ) );
	add_shortcode( 'wp_theme_averageRate', array( $this, 'bsf_display_theme_AverageRatings' ) );
	add_shortcode( 'wp_theme_total_downloads', array( $this, 'bsf_display_theme_totaldownloads' ) );
	add_shortcode( 'wp_theme_last_updated', array( $this, 'bsf_display_theme_lastupdated' ) );
	add_shortcode( 'wp_theme_downloadlink', array( $this, 'bsf_display_theme_downloadlink' ) );
	}

	/**
	 * Get the Theme Details.
	 *
	 * @param int $action Get attributes Theme Details.
	 * @param int $api_params Get attributes Theme Details.
	 */
	function get_theme_activate_installs( $action, $api_params = array() ) {
		$theme_slug       = isset( $api_params['theme'] ) ? $api_params['theme'] : '';
		//
		$activet_installs = get_transient( "bsf_active_status_$theme_slug" );
		if ( false === $activet_installs ) {

			$url = 'https://api.wordpress.org/themes/info/1.0/';
			if ( wp_http_supports( array( 'ssl' ) ) ) {
				$url = set_url_scheme( $url, 'https' );
			}

			$args      = (object) $api_params;
			$http_args = array(
				'body' => array(
					'action'  => $action,
					'timeout' => 15,
					'request' => serialize( $args ),
				),
			);

			$request = wp_remote_post( $url, $http_args );
		// 	var_dump($request);
		// wp_die();
			
			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );
				// var_dump($response);
				// wp_die();
				$themes_list = ( is_object( $response ) && isset( $response->themes ) ) ? $response->themes : array();

				// If theme list is not returned, retuen false.
				if ( ! isset( $themes_list[0] ) ) {
					return false;
				}

				$activet_installs = $themes_list[0]->active_installs;
				set_transient( "bsf_active_status_$theme_slug", $activet_installs, 604800 );
			}
		}
		
		return $activet_installs;
	}
	function bsf_tr_fetch_data() {	
	$argst = array(
		    'slug' => 'astra',
		    'fields' => array( 'active_installs' => true,'screenshot_url'=> true,'versions'=> true,'ratings'=> true,'download_link'=> true )
		);
		 
		// Make request and extract plug-in object
		$responset = wp_remote_post(
		    'http://api.wordpress.org/themes/info/1.0/?action=theme_information&request[fields][ratings]=true',
		    array(
		        'body' => array(
		            'action'  => 'theme_information',
		            'request' => serialize((object)$argst)
		        )
		    )
		);
		$theme = unserialize(wp_remote_retrieve_body($responset));
		////($theme);
	
	return $theme;
	}
    function bsf_tr_get_text( $action, $api_params = array() ) {
    	$theme_slug = isset( $api_params['theme'] ) ? $api_params['theme'] : '';
		$frequency  = get_option('wp_info');
		////($frequency);
		//$frequency = get_option('frequency');
		$second = 0;
		$day = 0;
		if(!empty($frequency['Frequency'])) {
			$day    = (($frequency['Frequency'] *24)*60)*60;
			$second = ( $second + $day );
		}
		
		
		////($second);
		$argst = array(
			    'slug' => $theme_slug,
			    'fields' => array( 'active_installs' => true,'screenshot_url'=> true,'versions'=> true,'ratings'=> true,'download_link'=> true )
			);
			// Make request and extract plug-in object
			$responset = wp_remote_post(
			    'http://api.wordpress.org/themes/info/1.0/?action=theme_information&request[fields][ratings]=true',
			    array(
			        'body' => array(
			            'action'  => 'theme_information',
			            'request' => serialize((object)$argst)
			        )
			    )
			);
		$theme = unserialize(wp_remote_retrieve_body($responset));
		$theme = get_site_transient( 'bsf_tr_theme_info' );
		//$data = get_site_transient( 'bsf_tr_theme_info' );
		////($theme);
			if ( false === $theme ) {
			// 	//$data = tr_fetch_data();
				$data = (!empty($theme) ? $theme : '' );
				set_site_transient( 'bsf_tr_theme_info', $this->bsf_tr_fetch_data() ,$second );
			}

			if ( empty( $theme ) ) {
			 	return '';
			 }
		return $theme;
	}
	
	/**
	 * Display Active Install Count.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	function bsf_display_active_installs( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$active_installs = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];

		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);

			$active_installs = $this->get_theme_activate_installs( 'query_themes', $api_params );

			// return if we get false response.
			if ( false == $active_installs ) {
				return 'Please Verify Theme Details!';
			}
		}

		return $active_installs;
	}
	function bsf_display_theme_version( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}

		return $theme->version;
	}
	function bsf_display_theme_ratings( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'num_ratings'     => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		////($theme);

		return ($theme->num_ratings);
	}
	function bsf_display_theme_FiveStarRatings( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version         = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'ratings'         => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		////($theme);

		return ($theme->ratings[5]);
	}
	function bsf_display_theme_AverageRatings( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'rating'          => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		////($theme);

		return "".($theme->rating)."%";
	}
	function bsf_display_theme_totaldownloads( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'downloaded'      => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		////($theme);

		return "".($theme->downloaded)."";
	}
	function bsf_display_theme_lastupdated( $atts ) {

		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'last_updated'    => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		////($theme);

		return "".($theme->last_updated)."";
	}
	function bsf_display_theme_downloadlink( $atts ){
		$atts = shortcode_atts(
			array(
				'wp_theme_slug'   => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author'    => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			), $atts
		);

		$version = false;
		$wp_theme_slug   = $atts['wp_theme_slug'];
		$wp_theme_author = $atts['theme_author'];
		////($wp_theme_slug);
		// bail early if theme name is not provided.
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}

		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'download_link'        => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information',$api_params);
			////($version->version);
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
			return '<a href="'.esc_url($theme->download_link).'" target="_blank">'.esc_url($theme->download_link).'</a>';
	}
	}
}

new WP_Themes_Stats_Api();
