<?php
/**
 * Calling W.ORG API Response.
 *
 * @package WP Advanced Stats
 * @author Brainstorm Force
 */

/**
 * Helper class for the ActiveCampaign API.
 *
 * @since 1.0.0
 */
class WP_plugin_Stats_Api {
	/**
	 * The unique instance of the plugin.
	 *
	 * @var Instance variable
	 */
	private static $instance;
		/**
		 * Gets an instance of our plugin.
		 */
		/**
		 * Gets an instance of our plugin.
		 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * Constructor calling W.ORG API Response.
	 */
	public function __construct() {
		add_shortcode( 'adv_stats_name', array( $this, 'bsf_display_plugin_name' ) );
		add_shortcode( 'adv_stats_active_install', array( $this, 'bsf_display_plugin_active_installs' ) );
		add_shortcode( 'adv_stats_version', array( $this, 'bsf_display_plugin__version' ) );
		add_shortcode( 'adv_stats_ratings', array( $this, 'bsf_display_plugin__ratings' ) );
		add_shortcode( 'adv_stats_ratings_5star', array( $this, 'bsf_display_plugin__fiveStar_ratings' ) );
		add_shortcode( 'adv_stats_ratings_average', array( $this, 'bsf_display_plugin__average_ratings' ) );
		add_shortcode( 'adv_stats_downloads', array( $this, 'bsf_display_plugin__totaldownloads' ) );
		add_shortcode( 'adv_stats_last_updated', array( $this, 'bsf_display_plugin__lastupdated' ) );
		add_shortcode( 'adv_stats_download_link', array( $this, 'bsf_display_plugin__downloadlink' ) );
		add_shortcode( 'adv_stats_downloads_counts', array( $this, 'bsf_display_download_count' ) );
		add_shortcode( 'adv_stats_total_active', array( $this, 'bsf_display_Active_installs' ) );
	}
	/**
	 * Get the plugin Details.
	 *
	 * @param int $action Get attributes plugin Details.
	 * @param int $api_params Get attributes plugin Details.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_plugin_get_text( $action, $api_params = array() ) {
		$plugin_slug = isset( $api_params['plugin'] ) ? $api_params['plugin'] : '';
		$frequency   = get_option( 'wp_info' );
		$second      = 0;
		$day         = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}
			$args     = (object) array(
				'slug'   => $plugin_slug,
				'fields' => array( 'active_installs' => true ),
			);
			$response = wp_remote_post(
				'http://api.wordpress.org/plugins/info/1.0/',
				array(
					'body' => array(
						'action'  => 'plugin_information',
						'request' => serialize( (object) $args ),
					),
				)
			);
		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$wp_plugin = unserialize( wp_remote_retrieve_body( $response ) );
			$slug      = 'bsf_tr_plugin_info_' . $plugin_slug;
			$plugin    = get_site_transient( $slug );
			if ( false === $plugin || empty( $plugin ) ) {
				$second = ( ! empty( $second ) ? $second : 86400 );
				set_site_transient( $slug, $wp_plugin, $second );
			}

			return $plugin;
		}
	}

	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin_name( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$update_option    = array(
			'wp_plugin_slug' => ( ! empty( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '' ),
		);
		update_option( 'wp_plugin_info', $update_option );
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'name'           => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );

			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
		return $plugin->name;

	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin_active_installs( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$update_option    = array(
			'wp_plugin_slug' => ( ! empty( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '' ),
		);
		update_option( 'wp_plugin_info', $update_option );
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );

			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
			$x = get_option( 'wp_info' );

		if ( 1 == $x['Hrchoice'] ) {
			$num = $plugin->active_installs;
			// var_dump($num);
			$n = $this->bsf_display_human_readable( $num );
			// var_dump($n);
			// // wp_die();
			return $n;
		} else {
			return number_format( $plugin->active_installs );
		}
	}
	function bsf_display_human_readable( $n ) {

		// first strip any formatting;
		$n = ( 0 + str_replace( ',', '', $n ) );

		// is this a number?
		if ( ! is_numeric( $n ) ) {
			return false;
		}

		// now filter it;
		// if ( $n > 1000000000000 ) {
		// 	return round( ( $n / 1000000000000 ), 1 ) . ' trillion';
		// } elseif ( $n > 1000000000 ) {
		// 	return round( ( $n / 1000000000 ), 1 ) . ' billion';
		// } elseif ( $n > 1000000 ) {
		// 	return round( ( $n / 1000000 ), 1 ) . ' million';
		// } elseif ( $n > 1000 ) {
		// 	return round( ( $n / 1000 ), 1 ) . ' thousand';
		// }
		$x = get_option( 'wp_info' );

		if ( 'K' === $x['Rchoice'] )
		{
				return round( ( $n / 1000 ), 6) . ' thousand';
		}
		elseif( 'M' === $x['Rchoice'] )
		{
			return round( ( $n / 1000000 ), 6 ) . ' million';
		}
		elseif( 'B' === $x['Rchoice'] )
		{
			return round( ( $n / 1000000000 ), 6 ) . ' billion';
		}
		elseif( 'T' === $x['Rchoice'] )
		{
			return round( ( $n / 1000000000000 ), 8 ) . ' trillion';
		}
		return  $n ;
	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__version( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
		return $plugin->version;

	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__ratings( $atts ) {

		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'num_ratings'    => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
		return $plugin->num_ratings;

	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__fiveStar_ratings( $atts ) {

		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'ratings'        => true,
				),
			);
			$plugin     = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
		return $plugin->ratings[5];
	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__average_ratings( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
				'outof'         => isset( $atts['outof'] ) ? $atts['outof'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$outof            = $atts['outof'];
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}

		if ( is_numeric( $outof ) || empty( $outof ) ) {
			$outof = ( ! empty( $outof ) ? $outof : 100 );
			$outof = ( ( $plugin->rating ) / 100 ) * $outof;
			return $outof;
		} else {
			return 'Out Of Value Must Be Nummeric!';
		}
	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__totaldownloads( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
		return $plugin->downloaded;
	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__lastupdated( $atts ) {
		$dateformat       = get_option( 'wp_info' );
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'last_updated'   => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );

			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
			$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
			$newDate              = date( $dateformat['Choice'], strtotime( $plugin->last_updated ) );
		return $newDate;

	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__downloadlink( $atts, $label ) {
		// $label= $label;
		$atts             = shortcode_atts(
			array(
				'plugin'        => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'plugin_author' => isset( $atts['plugin_author'] ) ? $atts['plugin_author'] : '',
				'label'         => isset( $atts['label'] ) ? $atts['label'] : '',
			),
			$atts
		);
		$wp_plugin_slug   = $atts['plugin'];
		$wp_plugin_author = $atts['plugin_author'];
		$wp_plugin_label  = $atts['label'];

		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' != $wp_plugin_slug && false != $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'author'   => $wp_plugin_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);

			$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
			// return if we get false response.
			if ( false == $plugin ) {
				return 'Please Verify plugin Details!';
			}
		}
			$label = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );

		return '<a href="' . esc_url( $plugin->download_link ) . '" target="_blank">' . $label . '</a>';
	}
	/**
	 * Get the plugin Details.
	 *
	 * @param int $action Get attributes plugin Details.
	 * @param int $api_params Get attributes plugin Details.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugins_active_count( $action, $api_params = array() ) {
		$frequency = get_option( 'wp_info' );
		$second    = 0;
		$day       = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}
		$args = (object) $args = array(
			'author' => $api_params,
			'fields' => array( 'active_installs' => true ),
		);
		$url  = 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'  => 'query_plugins',
					'request' => serialize( (object) $args ),
				),
			)
		);

		if ( '' === $api_params ) {
			// Response body does not contain an object/array
				return 'Error! missing Plugin Author';
		} else {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );
				 $plugins        = $returned_object->plugins;
				 // var_dump($response);
				 // wp_die();

				$temp = 0;
					// $t = 0;
				foreach ( $plugins as $key ) {
					$temp = $temp + $key->active_installs;
				}

				$author  = 'bsf_tr_plugin_Active_Count_' . $api_params;
				$plugins = get_site_transient( $author );

				if ( false === $plugins || empty( $plugins ) ) {

					$second = ( ! empty( $second ) ? $second : 86400 );
					set_site_transient( $author, $temp, $second );

				}
				if ( empty( $plugins ) ) {
					return '';
				}
				 return $plugins;
			}
		}
	}
	/**
	 * shortcode.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_Active_installs( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_plugin_author = $atts['plugin_author'];
		$api_params       = array(

			'plugin_author' => $wp_plugin_author,
			'per_page'      => 1,
		);
		$plugins          = $this->bsf_display_plugins_active_count( 'query_plugins', $api_params['plugin_author'] );

		if ( false === is_numeric( $plugins ) ) {
					return 'Please Verify plugin Author!';
		} else {
			$x = get_option( 'wp_info' );
			//var_dump($x['Symbol']);
			if ( 1 == $x['Hrchoice'] ) {
				$num = $plugins;
				// var_dump($this->bsf_display_human_readable($num));
				$n = $this->bsf_display_human_readable( $num );
				// return number_format($plugin->active_installs);
				return $n;
				// return number_format($plugins);
			} else {
				return number_format( $plugins,3,'.',$x['Symbol']);
			}
		}

	}
	/**
	 * Get the plugin Details.
	 *
	 * @param int $action Get attributes plugin Details.
	 * @param int $api_params Get attributes plugin Details.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_total_plugin_download_count( $action, $api_params = array() ) {
		$frequency = get_option( 'wp_info' );
		$second    = 0;
		$day       = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}
		$args = (object) $args = array(
			'author' => $api_params,
			'fields' => array( 'active_installs' => true ),
		);
		$url  = 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'  => 'query_plugins',
					'request' => serialize( (object) $args ),
				),
			)
		);
		if ( $api_params === '' ) {
			// Response body does not contain an object/array
				return 'Error! missing Plugin Author';
		} else {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );
				 $plugins        = $returned_object->plugins;
				$temp            = 0;

				foreach ( $plugins as $key ) {
					$temp = $temp + $key->downloaded;
				}

				$author  = 'bsf_tr_plugin_downloads_Count_' . $api_params;
				$plugins = get_site_transient( $author );

				if ( false === $plugins || empty( $plugins ) ) {

					$second = ( ! empty( $second ) ? $second : 86400 );
					set_site_transient( $author, $temp, $second );

				}
				if ( empty( $plugins ) ) {
					return '';
				}
				 return $plugins;
			}
		}

	}
	public function bsf_display_download_count( $atts ) {
		$atts             = shortcode_atts(
			array(
				'plugin_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_plugin_author = $atts['plugin_author'];
		$api_params       = array(

			'plugin_author' => $wp_plugin_author,
			'per_page'      => 1,
		);
		$plugins          = $this->bsf_display_total_plugin_download_count( 'query_plugins', $api_params['plugin_author'] );
		if ( false === is_numeric( $plugins ) ) {
					return 'Please Verify plugin Author!';
		} else {
			$x = get_option( 'wp_info' );
			// var_dump($x['Hrchoice']);
			if ( 1 == $x['Hrchoice'] ) {
				$num = $plugins;
				// var_dump($this->bsf_display_human_readable($num));
				$n = $this->bsf_display_human_readable( $num );
				// return number_format($plugin->active_installs);
				return $n;
				// return number_format($plugins);
			} else {
				return number_format( $plugins );
			}
		}
	}
}
new WP_plugin_Stats_Api();
$WP_plugin_Stats_Api = WP_plugin_Stats_Api::get_instance();
