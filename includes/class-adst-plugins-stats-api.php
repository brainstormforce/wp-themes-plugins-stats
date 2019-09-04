<?php
/**
 * Calling W.ORG API Response.
 *
 * @package WP Themes & Plugins Stats
 * @author Brainstorm Force
 */

/**
 * Helper class for the ActiveCampaign API.
 *
 * @since 1.0.0
 */
class ADST_Plugins_Stats_Api {
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
		$plugin_slug    = isset( $api_params['plugin'] ) ? $api_params['plugin'] : '';
		$second         = 0;
		$day            = 0;
		$adst_frequency = get_option( 'adst_info' );
		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;
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
			$wp_plugin     = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
			$slug          = 'bsf_tr_plugin_info_' . $plugin_slug;
			$update_option = array(
				'slug'   => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
				'plugin' => ( ! empty( $wp_plugin ) ? $wp_plugin : '' ),
			);
			update_option( 'adst_plugin_info', $update_option );
			$plugin = get_site_transient( $slug );
			if ( false === $plugin || empty( $plugin ) ) {
				$second = ( ! empty( $second ) ? $second : 86400 );
				set_site_transient( $slug, $wp_plugin, $second );
			}
			if ( empty( $plugin ) ) {
				$plugin = get_option( '_site_transient_' . $slug );
				if ( empty( $plugin ) ) {
					delete_transient( '_site_transient_' . $slug );
				}
					delete_transient( '_site_transient_' . $slug );
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

			$plugin = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) || false === $plugin ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
					return $plugin->name;
			} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
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
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
					$num = $plugin->active_installs;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
			} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					$num    = $plugin->active_installs;
					$n      = $this->bsf_display_human_readable( $num );
					return $n;
			}
		}
	}
	/**
	 * Delete Transient
	 *
	 * @param int $wp_plugin_slug Get slug of plugin.
	 * @return var $wp_plugin_slug to delete transient.
	 */
	public function bsf_delete_transient( $wp_plugin_slug ) {
		$adst_info                      = get_option( 'adst_info' );
					$expiration         = $adst_info['Frequency'];
					$update_plugin_info = get_option( 'adst_plugin_info' );
					$slug               = 'bsf_tr_plugin_info_' . $wp_plugin_slug;
					$wp_plugin          = $update_plugin_info['plugin'];
					$second             = 0;
					$day                = 0;

		if ( ! empty( $expiration ) ) {
			$day        = ( ( $expiration * 24 ) * 60 ) * 60;
			$expiration = ( $second + $day );
		}
					$plugin      = get_site_transient( 'bsf_tr_plugin_info_' . $wp_plugin_slug );
					$name        = $wp_plugin_slug;
					$plugin_slug = $plugin->slug;
		if ( ! empty( $plugin ) && $name === $plugin_slug ) {
			delete_transient( $slug );
			set_site_transient( $slug, $plugin, $expiration );
			$plugin = get_option( "_site_transient_$slug" );
			return $plugin;
		}
	}
	/**
	 * Human Readable Format
	 *
	 * @param int $n Get Count of plugin.
	 * @return float $n Get human readable format.
	 */
	public function bsf_display_human_readable( $n ) {
		$n = ( 0 + str_replace( ',', '', $n ) );
		if ( ! is_numeric( $n ) ) {
			return false;
		}
		$x = get_option( 'adst_info' );
		if ( 'K' === $x['Rchoice'] ) {
				return round( ( $n / 1000 ), 2 ) . $x['Field1'];
		} elseif ( 'M' === $x['Rchoice'] ) {
			return round( ( $n / 1000000 ), 4 ) . $x['Field2'];
		} elseif ( 'normal' === $x['Rchoice'] ) {
				return number_format( $n, 0, '', $x['Symbol'] );
		}
		return $n;
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
				$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
				return $plugin->version;
			} else {
				$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
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
				$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
				return $plugin->num_ratings;
			} else {
				$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
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
				$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
				return $plugin->ratings[5];
			} else {
				$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
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
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
				if ( is_numeric( $outof ) || empty( $outof ) ) {
					$outof = ( ! empty( $outof ) ? $outof : 100 );
					$outof = ( ( $plugin->rating ) / 100 ) * $outof;
					return $outof;
				} else {
					return 'Out Of Value Must Be Nummeric!';
				}
			} else {
				$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
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

			$plugin = get_option( "_site_transient_bsf_tr_plugin_info_$wp_plugin_slug" );
			if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
					$num = $plugin->downloaded;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
			} else {
				$plugin  = $this->bsf_delete_transient( $wp_plugin_slug );
					$num = $plugin->downloaded;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
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
		$dateformat       = get_option( 'adst_info' );
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
			if ( empty( $plugin ) || false === $plugin ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
					$new_date             = date( $dateformat['Choice'], strtotime( $plugin->last_updated ) );
					return $new_date;
			} else {
				$plugin               = $this->bsf_delete_transient( $wp_plugin_slug );
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
				if ( 'Please Verify plugin Details!' === $plugin ) {
					return 'Please Verify plugin Details!';
				}
				$label = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );
				return '<a href="' . esc_url( $plugin->{'download_link'} ) . '" target="_blank">' . $label . '</a>';
			} else {
				$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
				$label  = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );
				return '<a href="' . esc_url( $plugin->{'download_link'} ) . '" target="_blank">' . $label . '</a>';
			}
		}
	}
	/**
	 * Delete Transient
	 *
	 * @param int $wp_plugin_slug Get slug of plugin.
	 * @return var $wp_plugin_slug to delete transient.
	 */
	public function bsf_delete_active_count_transient( $wp_plugin_slug ) {
		$adst_info                      = get_option( 'adst_info' );
					$expiration         = $adst_info['Frequency'];
					$update_plugin_info = get_option( 'adst_plugin_info' );
					$slug               = 'bsf_tr_plugin_Active_Count_' . $wp_plugin_slug;
					$wp_plugin          = $update_plugin_info['plugin'];
					$second             = 0;
					$day                = 0;
		if ( ! empty( $expiration ) ) {
			$day        = ( ( $expiration * 24 ) * 60 ) * 60;
			$expiration = ( $second + $day );
		}
					$plugin = get_site_transient( 'bsf_tr_plugin_Active_Count_' . $wp_plugin_slug );
		if ( ! empty( $plugin ) ) {
			delete_transient( $slug );
			set_site_transient( $slug, $plugin, $expiration );
			$plugin = get_option( "_site_transient_$slug" );
			return $plugin;
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
		$adst_frequency = get_option( 'adst_info' );
		$second         = 0;
		$day            = 0;
		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;
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
				if ( empty( $plugins ) ) {
					$plugins = get_option( '_site_transient_' . $author );
					if ( empty( $plugins ) ) {
						delete_transient( '_site_transient_' . $author );
					}
					delete_transient( '_site_transient_' . $author );
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

		if ( empty( $plugins ) || false === $plugins ) {
				$plugins = $this->bsf_display_plugins_active_count( 'query_plugins', $api_params['plugin_author'] );
			if ( 'Please Verify Author Details!' === $plugins || 'Error! missing Plugin Author' === $plugins ) {
				return 'Please Verify Author Details!';
			}
				$num = $plugins;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
		} else {
				$plugins = $this->bsf_delete_active_count_transient( $wp_plugin_author );
				$num     = $plugins;
				$n       = $this->bsf_display_human_readable( $num );
				return $n;
		}
	}
	/**
	 * Delete Transient
	 *
	 * @param int $wp_plugin_slug Get slug of plugin.
	 * @return var $wp_plugin_slug to delete transient.
	 */
	public function bsf_delete_download_count_transient( $wp_plugin_slug ) {
		$adst_info                      = get_option( 'adst_info' );
					$expiration         = $adst_info['Frequency'];
					$update_plugin_info = get_option( 'adst_plugin_info' );
					$slug               = 'bsf_tr_plugin_downloads_Count_' . $wp_plugin_slug;
					$wp_plugin          = $update_plugin_info['plugin'];
					$second             = 0;
					$day                = 0;
		if ( ! empty( $expiration ) ) {
			$day        = ( ( $expiration * 24 ) * 60 ) * 60;
			$expiration = ( $second + $day );
		}
					$plugin = get_site_transient( 'bsf_tr_plugin_downloads_Count_' . $wp_plugin_slug );
		if ( ! empty( $plugin ) ) {
			delete_transient( $slug );
			set_site_transient( $slug, $plugin, $expiration );
			$plugin = get_option( "_site_transient_$slug" );
			return $plugin;
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
		$adst_frequency = get_option( 'adst_info' );
		$second         = 0;
		$day            = 0;
		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;
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
					$plugins = get_option( '_site_transient_' . $author );
					if ( empty( $plugins ) ) {
						delete_transient( '_site_transient_' . $author );
					}
					delete_transient( '_site_transient_' . $author );
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

		if ( empty( $plugins ) || false === $plugins ) {
				$plugins = $this->bsf_display_total_plugin_download_count( 'query_plugins', $api_params['plugin_author'] );

			if ( 'Please Verify Author Details!' === $plugins || 'Error! missing Plugin Author' === $plugins ) {
					return 'Please Verify Author Details!';
			}
				$num = $plugins;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
		} else {
				$plugins = $this->bsf_delete_download_count_transient( $wp_plugin_author );
				$num     = $plugins;
				$n       = $this->bsf_display_human_readable( $num );
				return $n;
		}
	}
}
new ADST_Plugins_Stats_Api();
$adst_plugins_stats_api = ADST_Plugins_Stats_Api::get_instance();
