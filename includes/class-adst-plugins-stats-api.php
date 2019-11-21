<?php
/**
 * Calling W.ORG API Response.
 *
 * @package WP Themes & Plugins Stats
 * @author Brainstorm Force
759 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

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
	 * The unique per_page of the plugin.
	 *
	 * @var Per_page variable
	 */
	private static $per_page = 1;
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
		add_shortcode( 'adv_stats_name', array( $this, 'display_plugin_name' ) );
		add_shortcode( 'adv_stats_active_install', array( $this, 'display_plugin_active_installs' ) );
		add_shortcode( 'adv_stats_version', array( $this, 'display_plugin__version' ) );
		add_shortcode( 'adv_stats_ratings', array( $this, 'display_plugin__ratings' ) );
		add_shortcode( 'adv_stats_ratings_5star', array( $this, 'display_plugin__five_star_ratings' ) );
		add_shortcode( 'adv_stats_ratings_average', array( $this, 'display_plugin__average_ratings' ) );
		add_shortcode( 'adv_stats_plugin_ratings_average_in_star', array( $this, 'display_plugin_average_ratings_in_star' ) );
		add_shortcode( 'adv_stats_downloads', array( $this, 'display_plugin__totaldownloads' ) );
		add_shortcode( 'adv_stats_last_updated', array( $this, 'display_plugin__lastupdated' ) );
		add_shortcode( 'adv_stats_download_link', array( $this, 'display_plugin__downloadlink' ) );
		add_shortcode( 'adv_stats_downloads_counts', array( $this, 'display_download_count' ) );
		add_shortcode( 'adv_stats_total_active', array( $this, 'display_active_installs' ) );
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
		$args     = array(
			'slug'   => $plugin_slug,
			'fields' => array( 'active_installs' => true ),
		);
		$response = wp_remote_post(
			'https://api.wordpress.org/plugins/info/1.0/',
			array(
				'body' => array(
					'action'  => 'plugin_information',
					'request' => serialize( (object) $args ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				),
			)
		);

		if ( 'N;' !== $response['body'] ) {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$wp_plugin = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				if ( false === $wp_plugin ) {
					return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
				} else {
					$slug          = 'bsf_tr_plugin_info_' . $plugin_slug;
					$update_option = array(
						'slug'   => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
						'plugin' => ( ! empty( $wp_plugin ) ? $wp_plugin : '' ),
					);
					update_option( 'adst_plugin_info', $update_option );

					$plugin = get_site_transient( $slug );

					if ( false === $plugin || empty( $plugin ) || '' === $plugin ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_site_transient( $slug, $wp_plugin, $second );
						$plugin = get_site_transient( $slug );
					}

					if ( empty( $plugin ) ) {
						delete_transient( '_site_transient_' . $slug );
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}

					return $plugin;
				}
			}
		} else {
			return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
		}
	}
	/**
	 * Get slug of Plugins.
	 *
	 * @param string $atts Get attributes plugin_slug.
	 * @return string.
	 */
	public function get_plugin_shortcode_slug( $atts ) {
		$atts           = shortcode_atts(
			array(
				'plugin' => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
			),
			$atts
		);
		$wp_plugin_slug = $atts['plugin'];
		if ( '' === $wp_plugin_slug ) {
			return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
		} else {
			return $wp_plugin_slug;
		}
	}
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin_name( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'name'           => true,
				),
			);

			$plugin = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) || false === $plugin || '' === $plugin ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->name;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->name;
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
	public function display_plugin_active_installs( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$num = $plugin->active_installs;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$num = $plugin->active_installs;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				}
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
		$adst_info          = get_option( 'adst_info' );
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
	public function display_plugin__version( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->version;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->version;
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
	public function display_plugin__ratings( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'num_ratings'    => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->num_ratings;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->num_ratings;
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
	public function display_plugin__five_star_ratings( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'ratings'        => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->ratings[5];
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $plugin->ratings[5];
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
	public function display_plugin__average_ratings( $atts ) {
		$atts           = shortcode_atts(
			array(
				'plugin' => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'outof'  => isset( $atts['outof'] ) ? $atts['outof'] : '',
			),
			$atts
		);
		$wp_plugin_slug = $atts['plugin'];
		$outof          = $atts['outof'];
		if ( '' === $wp_plugin_slug ) {
			return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
		}
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
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
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
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
	}
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin_average_ratings_in_star( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $this->display_star_rating( $plugin );
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					return $this->display_star_rating( $plugin );
				}
			}
		}
	}
	/**
	 * Display star rating of plugin.
	 *
	 * @param array $plugin to get the rating of plugin.
	 */
	public function display_star_rating( $plugin ) {
		$rating = $plugin->rating;
		switch ( $rating ) {
			case ( 0 === $rating ):
				$stars = array( 0, 0, 0, 0, 0 );
				break;
			case ( $rating > 0 && $rating < 5 ):
				$stars = array( 0, 0, 0, 0, 0 );
				break;
			case ( $rating >= 5 && $rating < 15 ):
				$stars = array( 5, 0, 0, 0, 0 );
				break;
			case ( $rating >= 15 && $rating < 25 ):
				$stars = array( 1, 0, 0, 0, 0 );
				break;
			case ( $rating >= 25 && $rating < 35 ):
				$stars = array( 1, 5, 0, 0, 0 );
				break;
			case ( $rating >= 35 && $rating < 45 ):
				$stars = array( 1, 1, 0, 0, 0 );
				break;
			case ( $rating >= 45 && $rating < 55 ):
				$stars = array( 1, 1, 5, 0, 0 );
				break;
			case ( $rating >= 55 && $rating < 65 ):
				$stars = array( 1, 1, 1, 0, 0 );
				break;
			case ( $rating >= 65 && $rating < 75 ):
				$stars = array( 1, 1, 1, 5, 0 );
				break;
			case ( $rating >= 75 && $rating < 85 ):
				$stars = array( 1, 1, 1, 1, 0 );
				break;
			case ( $rating >= 85 && $rating < 95 ):
				$stars = array( 1, 1, 1, 1, 5 );
				break;
			case ( $rating >= 95 ):
				$stars = array( 1, 1, 1, 1, 1 );
				break;
			default:
				break;
		}
		$output = '<span class="eps-star-rating-plugins eps-star-rating-' . $plugin->slug . '">';
		foreach ( $stars as $star ) {
			if ( 0 === $star ) {
				$output .= '<span class="dashicons dashicons-star-empty" style=" color: #ffb900;"></span>';
			} elseif ( 5 === $star ) {
				$output .= '<span class="dashicons dashicons-star-half" style=" color: #ffb900;"></span>';
			} elseif ( 1 === $star ) {
				$output .= '<span class="dashicons dashicons-star-filled" style=" color: #ffb900;"></span>';
			}
		}
		$output .= '</span>';
		return $output;
	}
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin__totaldownloads( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);

			$plugin = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$num = $plugin->downloaded;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$num = $plugin->downloaded;
					$n   = $this->bsf_display_human_readable( $num );
					return $n;
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
	public function display_plugin__lastupdated( $atts ) {
		$dateformat     = get_option( 'adst_info' );
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'last_updated'   => true,
				),
			);

			$plugin = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) || false === $plugin ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
					$new_date             = gmdate( $dateformat['Choice'], strtotime( $plugin->last_updated ) );
					return $new_date;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
					$new_date             = gmdate( $dateformat['Choice'], strtotime( $plugin->last_updated ) );
					return $new_date;
				}
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
	public function display_plugin__downloadlink( $atts, $label ) {
		$atts            = shortcode_atts(
			array(
				'plugin' => isset( $atts['wp_plugin_slug'] ) ? $atts['wp_plugin_slug'] : '',
				'label'  => isset( $atts['label'] ) ? $atts['label'] : '',
			),
			$atts
		);
		$wp_plugin_slug  = $atts['plugin'];
		$wp_plugin_label = $atts['label'];

		if ( '' === $wp_plugin_slug ) {
			return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
		}
		if ( '' !== $wp_plugin_slug ) {
			$api_params = array(
				'plugin'   => $wp_plugin_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);
			$plugin     = get_option( '_site_transient_bsf_tr_plugin_info_' . $wp_plugin_slug );
			if ( '' === $plugin ) {
				return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $plugin ) ) {
					$plugin = $this->bsf_plugin_get_text( 'plugin_information', $api_params );
					if ( 'Please verify plugin slug.' === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$label = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );
					return '<a href="' . esc_url( $plugin->{'download_link'} ) . '" target="_blank">' . $label . '</a>';
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$label = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin->download_link ) );
					return '<a href="' . esc_url( $plugin->{'download_link'} ) . '" target="_blank">' . $label . '</a>';
				}
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
		$adst_info          = get_option( 'adst_info' );
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
		$url  = 'https://api.wordpress.org/plugins/info/1.0/';

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
			return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
		} else {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$plugins         = $returned_object->plugins;
				if ( empty( $plugins ) ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				} else {
					$temp = 0;
					foreach ( $plugins as $key ) {
						$temp = $temp + $key->active_installs;
					}

					$author  = 'bsf_tr_plugin_Active_Count_' . $api_params;
					$plugins = get_site_transient( $author );

					if ( false === $plugins || empty( $plugins ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_site_transient( $author, $temp, $second );
						$plugins = get_site_transient( $author );
					}
					if ( empty( $plugins ) ) {
						$plugins = get_option( '_site_transient_' . $author );
						delete_transient( '_site_transient_' . $author );
						return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
					}
					return $plugins;
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
	public function display_active_installs( $atts ) {
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

		if ( '0' === $plugins ) {
			return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
		} else {
			if ( empty( $plugins ) || false === $plugins ) {
				$plugins = $this->bsf_display_plugins_active_count( 'query_plugins', $api_params['plugin_author'] );
				if ( 'Please verify author slug.' === $plugins ) {
					return __( 'Please verify author slug. ', 'wp-themes-plugins-stats' );
				}
				$num = $plugins;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
			} else {
				$plugins = $this->bsf_delete_active_count_transient( $wp_plugin_author );
				if ( null === $plugins ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				$num = $plugins;
				$n   = $this->bsf_display_human_readable( $num );
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
	public function bsf_delete_download_count_transient( $wp_plugin_slug ) {
		$adst_info          = get_option( 'adst_info' );
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
		$url  = 'https://api.wordpress.org/plugins/info/1.0/';

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
			return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
		} else {
			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$plugins         = $returned_object->plugins;
				if ( empty( $plugins ) ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				} else {
					$temp = 0;

					foreach ( $plugins as $key ) {
						$temp = $temp + $key->downloaded;
					}

					$author  = 'bsf_tr_plugin_downloads_Count_' . $api_params;
					$plugins = get_site_transient( $author );

					if ( false === $plugins || empty( $plugins ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_site_transient( $author, $temp, $second );
						$plugins = get_site_transient( $author );
					}
					if ( empty( $plugins ) ) {
						$plugins = get_option( '_site_transient_' . $author );
						delete_transient( '_site_transient_' . $author );
						return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
					}
					return $plugins;
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
	public function display_download_count( $atts ) {
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
		if ( '0' === $plugins ) {
			return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
		} else {
			if ( empty( $plugins ) || false === $plugins ) {
				$plugins = $this->bsf_display_total_plugin_download_count( 'query_plugins', $api_params['plugin_author'] );

				if ( 'Please verify author slug.' === $plugins ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				$num = $plugins;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
			} else {
				$plugins = $this->bsf_delete_download_count_transient( $wp_plugin_author );
				if ( null === $plugins ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				$num = $plugins;
				$n   = $this->bsf_display_human_readable( $num );
				return $n;
			}
		}
	}
}

$adst_plugins_stats_api = ADST_Plugins_Stats_Api::get_instance();
