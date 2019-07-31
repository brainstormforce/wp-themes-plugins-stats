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
class WP_Plugins_Stats_Api {
	/**
	 * The unique instance of the plugin.
	 *
	 * @var Instance variable
	 */
	private static $instance;
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
		add_shortcode( 'adv_stats_ratings_5star', array( $this, 'bsf_display_plugin__five_star_ratings' ) );
		add_shortcode( 'adv_stats_ratings_average', array( $this, 'bsf_display_plugin__average_ratings' ) );
		add_shortcode( 'adv_stats_downloads', array( $this, 'bsf_display_plugin__totaldownloads' ) );
		add_shortcode( 'adv_stats_last_updated', array( $this, 'bsf_display_plugin__lastupdated' ) );
		add_shortcode( 'adv_stats_download_link', array( $this, 'bsf_display_plugin__downloadlink' ) );
		add_shortcode( 'adv_stats_downloads_counts', array( $this, 'bsf_display_download_count' ) );
		add_shortcode( 'adv_stats_total_active', array( $this, 'bsf_display_active_installs' ) );
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
						'request' => serialize( (object) $args ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					),
				)
			);
		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$wp_plugin = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
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
	 * Shortcode
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
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					return $plugin->name;
			} else {
				return $plugin->name;
			}
		}
	}
	/**
	 * Shortcode
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
		if ( '' === $wp_plugin_slug ) {
			return 'Please Verify plugin Details!';
		}
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->active_installs ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					$x      = get_option( 'wp_info' );
					if ( 1 == $x['Hrchoice'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
						$num = $plugin->active_installs;
						$n   = $this->bsf_display_human_readable( $num );
						return $n;
					} else {
						return number_format( $plugin->active_installs, 0, '', $x['Symbol'] );
					}
				}
			} else {
				$x = get_option( 'wp_info' );
				if ( 1 == $x['Hrchoice'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
					$num = $plugin->active_installs;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					return number_format( $plugin->active_installs, 0, '', $x['Symbol'] );
				}
			}
		}

	}
	/**
	 * Human Readable Format
	 *
	 * @param int $n Get Count of plugin.
	 * @return float $n Get human readable format.
	 */
	public function bsf_display_human_readable( $n ) {
		// first strip any formatting.
		$n = ( 0 + str_replace( ',', '', $n ) );
		if ( ! is_numeric( $n ) ) {
			return false;
		}
		$x = get_option( 'wp_info' );
		if ( 'K' === $x['Rchoice'] ) {
				return round( ( $n / 1000 ), 2 ) . $x['Field1'];
		} elseif ( 'M' === $x['Rchoice'] ) {
			return round( ( $n / 1000000 ), 4 ) . $x['Field2'];
		}
		return number_format( $n );
	}
	/**
	 * Shortcode
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
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->version ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					return $plugin->version;
				}
			} else {
				return $plugin->version;
			}
		}
	}
	/**
	 * Shortcode
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
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->num_ratings ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					return $plugin->num_ratings;
				}
			} else {
				return $plugin->num_ratings;
			}
		}
	}
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__five_star_ratings( $atts ) {

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
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->ratings[5] ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					return $plugin->ratings[5];
				}
			} else {
				return $plugin->ratings[5];
			}
		}

	}
	/**
	 * Shortcode
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
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->rating ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( is_numeric( $outof ) || empty( $outof ) ) {
						$outof = ( ! empty( $outof ) ? $outof : 100 );
						$outof = ( ( $plugin->rating ) / 100 ) * $outof;
						return $outof;
					} else {
						return 'Out Of Value Must Be Nummeric!';
					}
				}
			} else {
				if ( is_numeric( $outof ) || empty( $outof ) ) {
					$outof = ( ! empty( $outof ) ? $outof : 100 );
					$outof = ( ( $plugin->rating ) / 100 ) * $outof;
					return $outof;
				} else {
					return 'Out Of Value Must Be Nummeric!';
				}
			}
		}
	}
	/**
	 * Shortcode
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
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->downloaded ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					return $plugin->downloaded;
				}
			} else {
				return $plugin->downloaded;
			}
		}

	}
	/**
	 * Shortcode
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
		if ( '' !== $wp_plugin_slug ) {
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

			$plugin = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				if ( empty( $plugin->last_updated ) ) {
					return 'Wrong Plugin Information!';
				} else {
					$plugin               = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
					$new_date             = date( $dateformat['Choice'], strtotime( $plugin->last_updated ) );
					return $new_date;
				}
			} else {
				$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
				$new_date             = date( $dateformat['Choice'], strtotime( $plugin->last_updated ) );
				return $new_date;
			}
		}
	}
	/**
	 * Shortcode
	 *
	 * @param int    $atts Get attributes plugin Slug.
	 * @param string $label Get label as per user.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_plugin__downloadlink( $atts, $label ) {
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
		if ( '' !== $wp_plugin_slug ) {
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
			$plugin     = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
				$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				$label  = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );
				return '<a href="' . esc_url( $plugin->{'download_link'} ) . '" target="_blank">' . $label . '</a>';
			} else {
				$label = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );
				return '<a href="' . esc_url( $plugin->{'download_link'} ) . '" target="_blank">' . $label . '</a>';
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
	public function bsf_display_plugins_active_count( $action, $api_params = array() ) {
		$frequency = get_option( 'wp_info' );
		$second    = 0;
		$day       = 0;
		if ( ! empty( $frequency['Frequency'] ) ) {
			$day    = ( ( $frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}
		$args = array(
			'author' => $api_params,
			'fields' => array( 'active_installs' => true ),
		);
		$url  = 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'  => 'query_plugins',
					'request' => serialize( (object) $args ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				),
			)
		);

		if ( '' === $api_params ) {
			// Response body does not contain an object/array.
				return 'Error! missing Plugin Author';
		} else {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$plugins         = $returned_object->plugins;
				$temp            = 0;
				foreach ( $plugins as $key ) {
					$temp = $temp + $key->active_installs;
				}

				$author  = 'bsf_tr_plugin_Active_Count_' . $api_params;
				$plugins = get_site_transient( $author );

				if ( false === $plugins || empty( $plugins ) ) {

					$second = ( ! empty( $second ) ? $second : 86400 );
					set_site_transient( $author, $temp, $second );

				}
				return $plugins;
			}
		}
	}
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function bsf_display_active_installs( $atts ) {
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
		$plugins          = get_option( "_site_transient_bsf_tr_plugin_Active_Count_$wp_plugin_author" );
		if ( empty( $plugins ) ) {
				$plugins = $this->bsf_display_plugins_active_count( 'query_plugins', $api_params['plugin_author'] );
			if ( false === is_numeric( $plugins ) ) {
				return 'Please Verify plugin Author!';
			} else {
				$x = get_option( 'wp_info' );
				if ( 1 == $x['Hrchoice'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
					$num = $plugins;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					return number_format( $plugins, 0, '', $x['Symbol'] );
				}
			}
		} else {
			if ( false === is_numeric( $plugins ) ) {
				return 'Please Verify plugin Author!';
			} else {
				$x = get_option( 'wp_info' );
				if ( 1 == $x['Hrchoice'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
					$num = $plugins;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					return number_format( $plugins, 0, '', $x['Symbol'] );
				}
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
		$args = array(
			'author' => $api_params,
			'fields' => array( 'active_installs' => true ),
		);
		$url  = 'http://api.wordpress.org/plugins/info/1.0/';

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'  => 'query_plugins',
					'request' => serialize( (object) $args ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				),
			)
		);
		if ( '' === $api_params ) {
			// Response body does not contain an object/array.
				return 'Error! missing Plugin Author';
		} else {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$plugins         = $returned_object->plugins;
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
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
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
		$plugins          = get_option( "_site_transient_bsf_tr_plugin_downloads_Count_$wp_plugin_author" );

		if ( empty( $plugins ) ) {
			$plugins = $this->bsf_display_total_plugin_download_count( 'query_plugins', $api_params['plugin_author'] );
			if ( false === is_numeric( $plugins ) ) {
				return 'Please Verify plugin Author!';
			} else {
				$x = get_option( 'wp_info' );
				if ( 1 == $x['Hrchoice'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
					$num = $plugins;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					return number_format( $plugins, 0, '', $x['Symbol'] );
				}
			}
		} else {
			if ( false === is_numeric( $plugins ) ) {
				return 'Please Verify plugin Author!';
			} else {
				$x = get_option( 'wp_info' );
				if ( 1 == $x['Hrchoice'] ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
					$num = $plugins;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					return number_format( $plugins, 0, '', $x['Symbol'] );
				}
			}
		}

	}
}
new WP_Plugins_Stats_Api();
$wp_plugins_stats_api = WP_Plugins_Stats_Api::get_instance();
