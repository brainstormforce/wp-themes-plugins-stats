<?php
/**
 * Calling W.ORG API Response.
 *
 * @package WP Themes & Plugins Stats
 * @author Brainstorm Force
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Helper class for the ADST Plugin Stats API.
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
		// new shortcodes.
		add_shortcode( 'adv_stats_plugins', array( $this, 'shortcode' ) );
		add_shortcode( 'adv_stats_total_active', array( $this, 'shortcode_for_total_plugins_active_installs' ) );
		add_shortcode( 'adv_stats_downloads_counts', array( $this, 'shortcode_for_total_plugins_downloads' ) );

		// old shortcodes.
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
	 * Shortcode: Get plugin data from wp.org.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts  An array shortcode attributes.
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'   => 'single',
				'plugin' => '',
				'field'  => 'active_installs',
				'label'  => '',
			),
			$atts
		);

		// The list of currently allowed fields.
		$allowed_fields = array(
			'single' => array(
				'active_installs',
				'downloaded',
				'name',
				'slug',
				'version',
				'author',
				'author_profile',
				'requires',
				'tested',
				'rating',
				'five_rating',
				'star_rating',
				'num_ratings',
				'last_updated',
				'added',
				'homepage',
				'description',
				'installation',
				'screenshots',
				'changelog',
				'faq',
				'download_link',
				'support_link',
				'tags',
				'donate_link',
			),
		);

		// Return early is an incorrect field is passed.
		if ( ! in_array( $atts['field'], $allowed_fields[ $atts['type'] ], true ) ) {
			return 'Plugin Not Found';
		}

		$second = 0;

		$day = 0;

		$adst_frequency = get_option( 'adst_info' );

		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

			$second = ( $second + $day );
		}

		// Get the plugin data if it has already been stored as a transient.
		$plugin_data = get_transient( 'bsf_tr_plugin_info_' . esc_attr( $atts['plugin'] ) );

		// If there is no transient, get the plugin data from wp.org.
		if ( ! $plugin_data ) {
			$response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/' . esc_attr( $atts['plugin'] ) . '.json?fields=active_installs' );

			if ( is_wp_error( $response ) ) {
				return 'Plugin slug is incorrect!';
			} else {
				$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

				// If someone typed in the plugin slug incorrectly, the body will return null.
				if ( ! empty( $plugin_data ) ) {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_plugin_info_' . esc_attr( $atts['plugin'] ), $plugin_data, $second );
				} else {
					return 'Plugin slug is incorrect!';
				}
			}
		} else {
				$second = ( ! empty( $second ) ? $second : 86400 );
				set_transient( 'bsf_tr_plugin_info_' . esc_attr( $atts['plugin'] ), $plugin_data, $second );
				$plugin_data = get_transient( 'bsf_tr_plugin_info_' . esc_attr( $atts['plugin'] ) );
		}
			$output = $this->field_output( $atts, $plugin_data );

			return $output;
	}

	/**
	 * Helper function for generating all field output
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts         An array shortcode attributes.
	 * @param array $plugin_data  An array of all retrived plugin data from wp.org.
	 */
	public function field_output( $atts, $plugin_data ) {

		// Generate the shortcode output, some fields need special handling.
		switch ( $atts['field'] ) {
			case 'active_installs':
				if ( ! empty( $plugin_data['active_installs'] ) ) {
					$output = $this->bsf_display_human_readable( $plugin_data['active_installs'] );
				} else {
					$output = 'Please verify plugin slug.';
				}
				break;
			case 'downloaded':
				if ( ! empty( $plugin_data['downloaded'] ) ) {
					$output = $this->bsf_display_human_readable( $plugin_data['downloaded'] );
				} else {
					$output = 'Please verify plugin slug.';
				}
				break;
			case 'contributors':
				$contributors = (array) $plugin_data['contributors'];

				if ( ! empty( $contributors ) ) {
					foreach ( $contributors as $contributor => $link ) {
						$output[] = '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_attr( $contributor ) . '</a>';
					}
					$output = implode( ', ', $output );
				} else {
					$output = 'Please verify plugin slug.';
				}
				break;
			case 'five_rating':
				$rating = isset( $plugin_data['rating'] ) ? $plugin_data['rating'] : '';
				if ( ! empty( $rating ) ) {
					$output = ( $rating / 100 ) * 5;
				} else {
					$output = 'Please verify plugin slug.';
				}
				break;
			case 'star_rating':
				$rating = isset( $plugin_data['rating'] ) ? $plugin_data['rating'] : '';

				if ( ! empty( $rating ) ) {
					$five_rating = ( $rating / 100 ) * 5;

					$output = '<span class="adv-stats-star-rating" title="' . $five_rating . ' ' . __( 'out of 5 stars', 'wp-themes-plugins-stats' ) . '">';
					$stars  = ADST_Helper::get_stars( $rating );

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
				} else {
					$output = 'Please verify plugin slug.';
				}
				break;
			case 'last_updated':
				$dateformat           = get_option( 'adst_info' );
				$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? sanitize_text_field( $dateformat['Choice'] ) : 'Y-m-d' );
				$output               = isset( $plugin_data['last_updated'] ) ? gmdate( $dateformat['Choice'], strtotime( $plugin_data['last_updated'] ) ) : 'Please verify plugin slug.';
				break;
			case 'description':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['description'];
				break;
			case 'installation':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['installation'];
				break;
			case 'screenshots':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['screenshots'];
				break;
			case 'changelog':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['changelog'];
				break;
			case 'faq':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['faq'];
				break;
			case 'download_link':
				$label  = isset( $atts['label'] ) ? $atts['label'] : '';
				$link   = isset( $plugin_data['download_link'] ) ? $plugin_data['download_link'] : '';
				$label  = ( ! empty( $label ) ? esc_attr( $label ) : esc_url( $link ) );
				$output = '<a href="' . esc_url( $link ) . '" target="_blank">' . $label . '</a>';
				break;
			case 'support_link':
				$slug   = $plugin_data['slug'];
				$output = 'https://wordpress.org/support/plugin/' . $slug;
				break;
			case 'tags':
				$tags = ( ! empty( $plugin_data['tags'] ) ? (array) $plugin_data['tags'] : '' );
				if ( ! empty( $tags ) ) {
					$output = implode( ', ', $tags );
				} else {
					$output = 'Please verify plugin slug.';
				}
				break;
			default:
				$output = isset( $plugin_data[ $atts['field'] ] ) ? $plugin_data[ $atts['field'] ] : 'Please verify plugin slug.';
		}

		return $output;
	}
	/**
	 * Convert number into particular format.
	 *
	 * @param int $plugins_count Get Count of plugins.
	 * @return float $plugins_count Get human readable format.
	 */
	public function bsf_display_human_readable( $plugins_count ) {
		if ( ! is_numeric( $plugins_count ) ) {
			return false;
		} elseif ( null === $plugins_count ) {
			return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
		}
		$plugins_count = ( 0 + str_replace( ',', '', $plugins_count ) );
		$choice        = get_option( 'adst_info' );
		if ( 'K' === $choice['Rchoice'] ) {
				return round( ( $plugins_count / 1000 ), 2 ) . $choice['Field1'];
		} elseif ( 'M' === $choice['Rchoice'] ) {
			return round( ( $plugins_count / 1000000 ), 3 ) . $choice['Field2'];
		} elseif ( 'normal' === $choice['Rchoice'] ) {
				return number_format( $plugins_count, 0, '', $choice['Symbol'] );
		}
			return $plugins_count;
	}

		/**
		 * Shortcode: Get plugins total active installs data from wp.org.
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts  An array shortcode attributes.
		 */
	public function shortcode_for_total_plugins_active_installs( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'   => 'single',
				'author' => '',
				'field'  => 'active_installs',
				'label'  => '',
			),
			$atts
		);

		if ( '' === $atts['author'] ) {
			return 'Error! missing plugin Author';
		} else {
			$second = 0;

			$day = 0;

			$adst_frequency = get_option( 'adst_info' );

			if ( ! empty( $adst_frequency['Frequency'] ) ) {
				$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

				$second = ( $second + $day );
			}

			// Get the plugins data if it has already been stored as a transient.
			$plugin_data = get_transient( 'bsf_tr_plugins_Active_Count_' . esc_attr( $atts['author'] ) );

			// If there is no transient, get the plugins data from wp.org.
			if ( ! $plugin_data ) {
				$response = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.1/?action=query_plugins&request[author]=' . esc_attr( $atts['author'] ) . '&request[fields][active_installs]=true' );

				if ( is_wp_error( $response ) ) {
					return 'Plugin author is incorrect!';
				} else {
						$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

						$total_active_installs = 0;

					foreach ( $plugin_data['plugins'] as $key => $value ) {
						$total_active_installs = $total_active_installs + $plugin_data['plugins'][ $key ]->active_installs;
					}
						// If someone typed in the plugins author incorrectly, the body will return null.
					if ( ! empty( $plugin_data ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );

						set_transient( 'bsf_tr_plugins_Active_Count_' . esc_attr( $atts['author'] ), $total_active_installs, $second );
					} else {
						return 'Plugin author is incorrect!';
					}
				}
			} else {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_plugins_Active_Count_' . esc_attr( $atts['author'] ), $plugin_data, $second );
					$plugin_data = get_transient( 'bsf_tr_plugins_Active_Count_' . esc_attr( $atts['author'] ) );
			}

			$output = isset( $plugin_data ) ? $this->bsf_display_human_readable( $plugin_data ) : 'Please verify plugin slug.';
			return $output;
		}
	}

			/**
			 * Shortcode: Get total plugins downloads data from wp.org.
			 *
			 * @since 1.0.0
			 *
			 * @param array $atts  An array shortcode attributes.
			 */
	public function shortcode_for_total_plugins_downloads( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'   => 'single',
				'author' => '',
				'field'  => 'downloaded',
				'label'  => '',
			),
			$atts
		);

		if ( '' === $atts['author'] ) {
			return 'Error! missing Plugin Author';
		} else {
			$second = 0;

			$day = 0;

			$adst_frequency = get_option( 'adst_info' );

			if ( ! empty( $adst_frequency['Frequency'] ) ) {
				$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

				$second = ( $second + $day );
			}

			// Get the plugins data if it has already been stored as a transient.
			$plugin_data = get_transient( 'bsf_tr_plugin_downloaded_Count_' . esc_attr( $atts['author'] ) );

			// If there is no transient, get the plugins data from wp.org.
			if ( ! $plugin_data ) {
				$response = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.1/?action=query_plugins&request[author]=' . esc_attr( $atts['author'] ) . '&request[fields][downloaded]=true' );

				if ( is_wp_error( $response ) ) {
					return 'Plugin author is incorrect!';
				} else {
						$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

						$total_downloads_count = 0;

					foreach ( $plugin_data['plugins'] as $key => $value ) {
						$total_downloads_count = $total_downloads_count + $plugin_data['plugins'][ $key ]->downloaded;
					}

						// If someone typed in the plugins author incorrectly, the body will return null.
					if ( ! empty( $plugin_data ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_transient( 'bsf_tr_plugin_downloaded_Count_' . esc_attr( $atts['author'] ), $total_downloads_count, $second );
					} else {
						return 'Plugin author is incorrect!';
					}
				}
			} else {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_plugin_downloaded_Count_' . esc_attr( $atts['author'] ), $plugin_data, $second );
					$plugin_data = get_transient( 'bsf_tr_plugin_downloaded_Count_' . esc_attr( $atts['author'] ) );
			}

			$output = isset( $plugin_data ) ? $this->bsf_display_human_readable( $plugin_data ) : 'Please verify plugin slug.';
			return $output;
		}
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
					$plugin_count    = $plugin->active_installs;
					$active_installs = $this->bsf_display_human_readable( $plugin_count );
					return $active_installs;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$plugin_count    = $plugin->active_installs;
					$active_installs = $this->bsf_display_human_readable( $plugin_count );
					return $active_installs;
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
	 * @param array $atts Get attributes plugin Slug.
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
		$stars  = ADST_Helper::get_stars( $rating );
		$output = '<span class="eps-star-rating-plugins eps-star-rating-' . esc_attr( $plugin->slug ) . '">';
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
					$plugin_count = $plugin->downloaded;
					$downloads    = $this->bsf_display_human_readable( $plugin_count );
					return $downloads;
				} else {
					$plugin = $this->bsf_delete_transient( $wp_plugin_slug );
					if ( null === $plugin ) {
						return __( 'Please verify plugin slug.', 'wp-themes-plugins-stats' );
					}
					$plugin_count = $plugin->downloaded;
					$downloads    = $this->bsf_display_human_readable( $plugin_count );
					return $downloads;
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
				$plugin_count = $plugins;
				$installs     = $this->bsf_display_human_readable( $plugin_count );
				return $installs;
			} else {
				$plugins = $this->bsf_delete_active_count_transient( $wp_plugin_author );
				if ( null === $plugins ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				$plugin_count = $plugins;
				$installs     = $this->bsf_display_human_readable( $plugin_count );
				return $installs;
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
				$plugin_count   = $plugins;
				$download_count = $this->bsf_display_human_readable( $plugin_count );
				return $download_count;
			} else {
				$plugins = $this->bsf_delete_download_count_transient( $wp_plugin_author );
				if ( null === $plugins ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				$plugin_count   = $plugins;
				$download_count = $this->bsf_display_human_readable( $plugin_count );
				return $download_count;
			}
		}
	}
}

$adst_plugins_stats_api = ADST_Plugins_Stats_Api::get_instance();
