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

class WP_Themes_Stats_Api {
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
		add_shortcode( 'adv_stats_theme_name', array( $this, 'bsf_display_theme_name' ) );
		add_shortcode( 'adv_stats_theme_active_install', array( $this, 'bsf_display_theme_active_installs' ) );
		add_shortcode( 'adv_stats_theme_version', array( $this, 'bsf_display_theme_version' ) );
		add_shortcode( 'adv_stats_theme_ratings', array( $this, 'bsf_display_theme_ratings' ) );
		add_shortcode( 'adv_stats_theme_ratings_5star', array( $this, 'bbsf_display_theme_five_star_ratings' ) );
		add_shortcode( 'adv_stats_theme_ratings_average', array( $this, 'bsf_display_theme_AverageRatings' ) );
		add_shortcode( 'adv_stats_theme_downloads', array( $this, 'bsf_display_theme_totaldownloads' ) );
		add_shortcode( 'adv_stats_theme_last_updated', array( $this, 'bsf_display_theme_lastupdated' ) );
		add_shortcode( 'adv_stats_theme_download_link', array( $this, 'bsf_display_theme_downloadlink' ) );
		add_shortcode( 'adv_stats_theme_active_count', array( $this, 'bsf_display_theme_active_count' ) );
		add_shortcode( 'adv_stats_theme_downloads_count', array( $this, 'bsf_display_theme_downloaded_count' ) );
	}

	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function get_theme_activate_installs( $action, $api_params = array() ) {
		$theme_slug    = isset( $api_params['theme'] ) ? $api_params['theme'] : '';
		$update_option = array(
			'theme_slug' => ( ! empty( $api_params['theme'] ) ? $api_params['theme'] : '' ),
		);
		update_option( 'theme_info', $update_option );
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

			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

				$themes_list = ( is_object( $response ) && isset( $response->themes ) ) ? $response->themes : array();

				if ( ! isset( $themes_list[0] ) ) {
					return false;
				}

				$activet_installs = $themes_list[0]->active_installs;
				set_transient( "bsf_active_status_$theme_slug", $activet_installs, 604800 );
			}
		}
		 return $activet_installs;
	}
	/**
	 *
	 * @param int $n Get Count of plugin.
	 * @return float $n Get human readable format.
	 */
	function bsf_display_human_readable( $n ) {
		   // first strip any formatting;
		$n = ( 0 + str_replace( ',', '', $n ) );
		if ( ! is_numeric( $n ) ) {
			return false;
		}
		$x = get_option( 'wp_info' );
		if ( 'K' === $x['Rchoice'] ) {
				return round( ( $n / 1000 ), 0 ) . $x['Field1'];
		} elseif ( 'M' === $x['Rchoice'] ) {
			return round( ( $n / 1000000 ), 2 ) . $x['Field2'];
		} elseif ( 'B' === $x['Rchoice'] ) {
			return round( ( $n / 1000000000 ), 4) . $x['Field3'];
		} elseif ( 'T' === $x['Rchoice'] ) {
			return round( ( $n / 1000000000000 ), 8 ) . $x['Field4'];
		}
		return  $n;
	}
	public function bsf_tr_get_text( $action, $api_params = array() ) {
		$theme_slug = isset( $api_params['theme'] ) ? $api_params['theme'] : '';
		$frequency  = get_option( 'wp_info' );
		$second     = 0;
		$day        = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}

		$argst = array(
			'slug'   => $theme_slug,
			'fields' => array(
				'active_installs' => true,
				'screenshot_url'  => true,
				'versions'        => true,
				'ratings'         => true,
				'download_link'   => true,
			),
		);

			$responset = wp_remote_post(
				'http://api.wordpress.org/themes/info/1.0/?action=theme_information&request[fields][ratings]=true',
				array(
					'body' => array(
						'action'  => 'theme_information',
						'request' => serialize( (object) $argst ),
					),
				)
			);
		$wp_theme      = unserialize( wp_remote_retrieve_body( $responset ) );
		$slug          = 'bsf_tr_theme_info_' . $theme_slug;
		$theme         = get_site_transient( $slug );
		if ( false === $theme || empty( $theme ) ) {

			$second = ( ! empty( $second ) ? $second : 60 * 60 );
			set_site_transient( $slug, $wp_theme, $second );
		}

		if ( empty( $theme ) ) {
			return '';
		}
		return $theme;
	}
	/**
	 * Display Name of Themes.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_name( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'name'           => true,
				),
			);

			$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}

		return $theme->name;
	}
	/**
	 * Display Active Install Count.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_active_installs( $atts ) {

		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);
		$active_installs = false;
		$wp_theme_slug   = $atts['theme'];

		$wp_theme_author = $atts['theme_author'];

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

			if ( false == $active_installs ) {
				return 'Please Verify Theme Details!';
			}
		}

		$x = get_option( 'wp_info' );
		if ( 1 == $x['Hrchoice'] ) {
			$active_installs = $this->bsf_display_human_readable( $active_installs );
			return $active_installs;
		} else {
			return number_format( $active_installs, 0, '', $x['Symbol'] );
		}
	}
	/**
	 * Display Theme Version.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_version( $atts ) {

		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];
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
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );

			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		return $theme->version;
	}
	/**
	 * Display Theme Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_ratings( $atts ) {

		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'num_ratings'    => true,
				),
			);
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		return ( $theme->num_ratings );
	}
	/**
	 * Display Five Star Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bbsf_display_theme_five_star_ratings( $atts ) {
		$atts = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);

		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];

		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'ratings'        => true,
				),
			);
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );

			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		return ( $theme->ratings[5] );
	}
	/**
	 * Display Average Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_AverageRatings( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
				'outof'        => isset( $atts['outof'] ) ? $atts['outof'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];
		$outof           = $atts['outof'];

		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );

			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		if ( is_numeric( $outof ) || empty( $outof ) ) {
			$outof = ( ! empty( $outof ) ? $outof : 100 );
			$outof = ( ( $theme->rating ) / 100 ) * $outof;
			return '' . $outof . '';
		} else {
			return 'Out Of Value Must Be Nummeric!';
		}
	}
	/**
	 * Display Theme Downloads.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_totaldownloads( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];

		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'downloaded'     => true,
				),
			);
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		return '' . ( $theme->downloaded ) . '';
	}
	/**
	 * Display Last Updated.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_lastupdated( $atts ) {
		$dateformat      = get_option( 'wp_info' );
		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];
		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'last_updated'   => true,
				),
			);
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );

			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
		}
		$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? sanitize_text_field( $dateformat['Choice'] ) : 'Y-m-d' );
		$newDate              = date( $dateformat['Choice'], strtotime( $theme->last_updated ) );
		return $newDate;
	}
	/**
	 * Display Download Link.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function bsf_display_theme_downloadlink( $atts, $label ) {
		$atts            = shortcode_atts(
			array(
				'theme'        => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'theme_author' => isset( $atts['theme_author'] ) ? $atts['theme_author'] : '',
				'label'        => isset( $atts['label'] ) ? $atts['label'] : '',
			),
			$atts
		);
		$version         = false;
		$wp_theme_slug   = $atts['theme'];
		$wp_theme_author = $atts['theme_author'];
		$wp_theme_label  = $atts['label'];

		if ( '' == $wp_theme_slug ) {
			return 'Please Verify Theme Details!';
		}
		if ( '' != $wp_theme_slug && false != $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => 1,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'download_link'  => true,
				),
			);
			$theme      = $this->bsf_tr_get_text( 'theme_information', $api_params );
			// return if we get false response.
			if ( false == $theme ) {
				return 'Please Verify Theme Details!';
			}
			$label = ( ! empty( $wp_theme_label ) ? esc_attr( $wp_theme_label ) : esc_url( $theme->download_link ) );
			return '<a href="' . esc_url( $theme->download_link ) . '" target="_blank">' . $label . '</a>';
		}

	}
	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function bsf_get_theme_active_count( $action, $api_params = array() ) {

		$frequency = get_option( 'wp_info' );
		$second    = 0;
		$day       = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}

		if ( $api_params === '' ) {
			// Response body does not contain an object/array
				return 'Error! missing Theme Author';
		} else {
			$args = (object) $args = array(
				'author' => $api_params,
				'fields' => array( 'active_installs' => true ),
			);
			$url  = 'https://api.wordpress.org/themes/info/1.0/';

			$response = wp_remote_post(
				$url,
				array(
					'body' => array(
						'action'  => 'query_themes',
						'request' => serialize( (object) $args ),
					),
				)
			);
			if ( ! is_wp_error( $response ) ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );
				 $themes         = $returned_object->themes;
				$temp            = 0;
				foreach ( $themes as $key ) {
					$temp = $temp + $key->active_installs;

				}

				$author = 'bsf_tr_themes_Active_Count_' . $api_params;
				$themes = get_site_transient( $author );

				if ( false === $themes || empty( $themes ) ) {

					$second = ( ! empty( $second ) ? $second : 86400 );
					set_site_transient( $author, $temp, $second );

				}
				if ( empty( $themes ) ) {
					return '';
				}
				 return $themes;
			}
		}

	}
	/**
	 * Display Total Active Install Count by Author.
	 *
	 * @param int $atts Get attributes theme_author.
	 */
	public function bsf_display_theme_active_count( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_theme_author = $atts['theme_author'];

		$api_params = array(
			'theme_author' => $wp_theme_author,
			'per_page'     => 1,
		);

		$themes = $this->bsf_get_theme_active_count( 'query_themes', $api_params['theme_author'] );
		if ( false === is_numeric( $themes ) ) {
			return 'Author is missing!';
		} else {
			$x = get_option( 'wp_info' );
			if ( 1 == $x['Hrchoice'] ) {
				$num = $themes;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
			} else {
				return number_format( $themes, 0, '', $x['Symbol'] );
			}
		}
	}
	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function bsf_get_theme_downloads_count( $action, $api_params = array() ) {

		$frequency = get_option( 'wp_info' );
		$second    = 0;
		$day       = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}
		$args = (object) $args = array(
			'author' => $api_params,
			'fields' => array( 'downloaded' => true ),
		);
		$url  = 'https://api.wordpress.org/themes/info/1.0/';

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'  => 'query_themes',
					'request' => serialize( (object) $args ),
				),
			)
		);

		if ( '' === $api_params ) {
			// Response body does not contain an object/array
				return 'Error! missing Theme Author';
		} else {
			if ( ! is_wp_error( $response ) ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );
				 $themes         = $returned_object->themes;
				$temp            = 0;

				foreach ( $themes as $key ) {
					$temp = $temp + $key->downloaded;

				}

				$author = 'bsf_tr_themes_downloaded_Count_' . $api_params;
				$themes = get_site_transient( $author );

				if ( false === $themes || empty( $themes ) ) {

					$second = ( ! empty( $second ) ? $second : 86400 );
					set_site_transient( $author, $temp, $second );

				}

				 return $themes;
			}
		}

	}
	/**
	 * Display Total Download Count.
	 *
	 * @param int $atts Get attributes theme_author.
	 */
	public function bsf_display_theme_downloaded_count( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_theme_author = $atts['theme_author'];

		$api_params = array(

			'theme_author' => $wp_theme_author,
			'per_page'     => 1,
		);

		$themes = $this->bsf_get_theme_downloads_count( 'query_themes', $api_params ['theme_author'] );

		if ( false === is_numeric( $themes ) ) {
			return 'Author is missing!';
		} else {
			$x = get_option( 'wp_info' );
			if ( 1 == $x['Hrchoice'] ) {
				$num = $themes;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
			} else {
				return number_format( $themes, 0, '', $x['Symbol'] );
			}
		}
	}
}

new WP_Themes_Stats_Api();
$WP_Themes_Stats_Api = WP_Themes_Stats_Api::get_instance();
