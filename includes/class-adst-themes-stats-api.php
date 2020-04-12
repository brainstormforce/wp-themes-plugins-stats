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
 * Helper class for the ADST Themes Stats API.
 *
 * @since 1.0.0
 */
class ADST_Themes_Stats_Api {
	/**
	 * The unique instance of the themes.
	 *
	 * @var Instance variable
	 */
	private static $instance;
	/**
	 * The unique per_page of the themes.
	 *
	 * @var Per_page variable
	 */
	private static $per_page = 1;

	/**
	 * Gets an instance of our themes.
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
		add_shortcode( 'adv_stats_themes', array( $this, 'shortcode' ) );
		add_shortcode( 'adv_stats_theme_active_count', array( $this, 'shortcode_for_total_theme_active_installs' ) );
		add_shortcode( 'adv_stats_theme_downloads_count', array( $this, 'shortcode_for_total_theme_downloads' ) );

		// old shortcodes.
		add_shortcode( 'adv_stats_theme_name', array( $this, 'display_theme_name' ) );
		add_shortcode( 'adv_stats_theme_active_install', array( $this, 'display_theme_active_installs' ) );
		add_shortcode( 'adv_stats_theme_version', array( $this, 'display_theme_version' ) );
		add_shortcode( 'adv_stats_theme_ratings', array( $this, 'display_theme_ratings' ) );
		add_shortcode( 'adv_stats_theme_ratings_5star', array( $this, 'display_theme_five_star_ratings' ) );
		add_shortcode( 'adv_stats_theme_ratings_average', array( $this, 'display_theme_average_ratings' ) );
		add_shortcode( 'adv_stats_theme_ratings_average_in_star', array( $this, 'display_theme_average_ratings_in_star' ) );
		add_shortcode( 'adv_stats_theme_downloads', array( $this, 'display_theme_totaldownloads' ) );
		add_shortcode( 'adv_stats_theme_last_updated', array( $this, 'display_theme_lastupdated' ) );
		add_shortcode( 'adv_stats_theme_download_link', array( $this, 'display_theme_downloadlink' ) );
		add_shortcode( 'adv_stats_theme_active_count', array( $this, 'display_theme_active_count' ) );
		add_shortcode( 'adv_stats_theme_downloads_count', array( $this, 'display_theme_downloaded_count' ) );
	}

		/**
		 * Shortcode: Get themes data from wp.org
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts  An array shortcode attributes.
		 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'  => 'single',
				'theme' => '',
				'field' => 'active_installs',
				'label' => '',
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
				'rating',
				'five_rating',
				'star_rating',
				'num_ratings',
				'last_updated',
				'homepage',
				'description',
				'download_link',
				'tags',
			),
		);

		// Return early is an incorrect field is passed.
		if ( ! in_array( $atts['field'], $allowed_fields[ $atts['type'] ], true ) ) {
			return 'Theme Not Found';
		}

		$second = 0;

		$day = 0;

		$adst_frequency = get_option( 'adst_info' );

		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

			$second = ( $second + $day );
		}

		// Get the themes data if it has already been stored as a transient.
		$theme_data = get_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['theme'] ) );

			// If there is no transient, get the themes data from wp.org.
		if ( ! $theme_data ) {
			$response = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=' . esc_attr( $atts['theme'] ) . '&request[fields][ratings]=true&request[fields][versions]=true&request[fields][active_installs]=true' );

			if ( is_wp_error( $response ) ) {
				return;
			} else {
				$theme_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

				// If someone typed in the themes slug incorrectly, the body will return null.
				if ( ! empty( $theme_data ) ) {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['theme'] ), $theme_data, $second );
				} else {
						return 'Theme slug is incorrect!';
				}
			}
		} else {
			$second = ( ! empty( $second ) ? $second : 86400 );
			set_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['theme'] ), $theme_data, $second );
			$theme_data = get_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['theme'] ) );
		}

			$output = $this->field_output( $atts, $theme_data );

			return $output;
	}

		/**
		 * Helper function for generating all field output.
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts         An array shortcode attributes.
		 * @param array $theme_data  An array of all retrived theme data from wp.org.
		 */
	public function field_output( $atts, $theme_data ) {
		// Generate the shortcode output, some fields need special handling.
		switch ( $atts['field'] ) {
			case 'active_installs':
				if ( isset( $theme_data['active_installs'] ) ) {
					$output = $this->bsf_display_human_readable( $theme_data['active_installs'] );
				} else {
					$output = 'Please verify theme slug.';
				}
				break;
			case 'downloaded':
				if ( isset( $theme_data['downloaded'] ) ) {
					$output = $this->bsf_display_human_readable( $theme_data['downloaded'] );
				} else {
					$output = 'Please verify theme slug.';
				}
				break;
			case 'five_rating':
				$rating = isset( $theme_data['rating'] ) ? $theme_data['rating'] : '';

				if ( ! empty( $rating ) ) {
					$output = ( $rating / 100 ) * 5;
				} else {
					$output = 'Please verify theme slug.';
				}
				break;
			case 'star_rating':
				$rating = isset( $theme_data['rating'] ) ? $theme_data['rating'] : '';

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
					$output = 'Please verify theme slug.';
				}
				break;
			case 'last_updated':
				$dateformat           = get_option( 'adst_info' );
				$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? sanitize_text_field( $dateformat['Choice'] ) : 'Y-m-d' );
				if ( isset( $theme_data['last_updated'] ) ) {
					$output = gmdate( $dateformat['Choice'], strtotime( $theme_data['last_updated'] ) );
				} else {
					$output = 'Please verify theme slug.';
				}
				break;
			case 'description':
				$sections = ( ! empty( $theme_data['sections'] ) ? (array) $theme_data['sections'] : '' );
				if ( ! empty( $sections['description'] ) ) {
					$output = $sections['description'];
				} else {
					$output = 'Please verify theme slug.';
				}
				break;
			case 'download_link':
				$label  = isset( $atts['label'] ) ? $atts['label'] : '';
				$link   = isset( $theme_data['download_link'] ) ? $theme_data['download_link'] : '';
				$label  = ( ! empty( $label ) ? esc_attr( $label ) : esc_url( $link ) );
				$output = '<a href="' . esc_url( $link ) . '" target="_blank">' . $label . '</a>';
				break;
			case 'tags':
				$tags = ( ! empty( $theme_data['tags'] ) ? (array) $theme_data['tags'] : '' );
				if ( ! empty( $tags ) ) {
					$output = implode( ', ', $tags );
				} else {
					$output = 'Please verify theme slug.';
				}
				break;
			default:
				$output = isset( $theme_data[ $atts['field'] ] ) ? $theme_data[ $atts['field'] ] : 'Please verify theme slug.';
		}

		return $output;
	}
		/**
		 * Convert number into particular format.
		 *
		 * @param int $theme_count Get Count of theme.
		 * @return float $theme_count Get human readable format.
		 */
	public function bsf_display_human_readable( $theme_count ) {
		if ( ! is_numeric( $theme_count ) ) {
			return false;
		} elseif ( null === $theme_count ) {
			return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
		}
		$theme_count = ( 0 + str_replace( ',', '', $theme_count ) );
		$choice      = get_option( 'adst_info' );
		if ( 'K' === $choice['Rchoice'] ) {
				return round( ( $theme_count / 1000 ), 2 ) . $choice['Field1'];
		} elseif ( 'M' === $choice['Rchoice'] ) {
			return round( ( $theme_count / 1000000 ), 3 ) . $choice['Field2'];
		} elseif ( 'normal' === $choice['Rchoice'] ) {
				return number_format( $theme_count, 0, '', $choice['Symbol'] );
		}
			return $theme_count;
	}

		/**
		 * Shortcode: Get themes total active installs  data from wp.org.
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts  An array shortcode attributes.
		 */
	public function shortcode_for_total_theme_active_installs( $atts ) {
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
			return 'Error! missing Theme Author';
		} else {
			$second = 0;

			$day = 0;

			$adst_frequency = get_option( 'adst_info' );

			if ( ! empty( $adst_frequency['Frequency'] ) ) {
				$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

				$second = ( $second + $day );
			}

			// Get the themes data if it has already been stored as a transient.
			$theme_data = get_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $atts['author'] ) );

			// If there is no transient, get the themes data from wp.org.
			if ( ! $theme_data ) {
				$response = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=query_themes&request[author]=' . esc_attr( $atts['author'] ) . '&request[fields][ratings]=true&request[fields][versions]=true&request[fields][active_installs]=true' );

				if ( is_wp_error( $response ) ) {
					return;
				} else {
						$theme_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

						// If someone typed in the themes author incorrectly, the body will return null.
					if ( ! empty( $theme_data ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $atts['author'] ), $theme_data, $second );
					} else {
						return 'Theme author is incorrect!';
					}
				}
			} else {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $atts['author'] ), $theme_data, $second );
					$theme_data = get_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $atts['author'] ) );
			}

			$output = isset( $theme_data['themes'][0]->active_installs ) ? $this->bsf_display_human_readable( $theme_data['themes'][0]->active_installs ) : '';
			return $output;
		}
	}

		/**
		 * Shortcode: Get total themes downloads data from wp.org.
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts  An array shortcode attributes.
		 */
	public function shortcode_for_total_theme_downloads( $atts ) {
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
			return 'Error! missing Theme Author';
		} else {
			$second = 0;

			$day = 0;

			$adst_frequency = get_option( 'adst_info' );

			if ( ! empty( $adst_frequency['Frequency'] ) ) {
				$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

				$second = ( $second + $day );
			}

			// Get the themes data if it has already been stored as a transient.
			$theme_data = get_transient( 'bsf_tr_themes_downloaded_Count_' . esc_attr( $atts['author'] ) );

			// If there is no transient, get the themes data from wp.org.
			if ( ! $theme_data ) {
				$response = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=query_themes&request[author]=' . esc_attr( $atts['author'] ) . '&request[fields][ratings]=true&request[fields][versions]=true&request[fields][downloaded]=true' );

				if ( is_wp_error( $response ) ) {
					return;
				} else {
						$theme_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

						// If someone typed in the themes author incorrectly, the body will return null.
					if ( ! empty( $theme_data ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_transient( 'bsf_tr_themes_downloaded_Count_' . esc_attr( $atts['author'] ), $theme_data, $second );
					} else {
						return 'Theme author is incorrect!';
					}
				}
			} else {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_themes_downloaded_Count_' . esc_attr( $atts['author'] ), $theme_data, $second );
					$theme_data = get_transient( 'bsf_tr_themes_downloaded_Count_' . esc_attr( $atts['author'] ) );
			}

			$output = isset( $theme_data['themes'][0]->downloaded ) ? $this->bsf_display_human_readable( $theme_data['themes'][0]->downloaded ) : '';
			return $output;
		}
	}

	/**
	 * Delete Transient
	 *
	 * @param int $wp_theme_slug Get slug of theme.
	 * @return var $wp_theme_slug to delete transient.
	 */
	public function bsf_delete_transient( $wp_theme_slug ) {
					$adst_info         = get_option( 'adst_info' );
					$expiration        = $adst_info['Frequency'];
					$update_theme_info = get_option( 'adst_theme_info' );
					$slug              = 'bsf_tr_theme_info_' . $wp_theme_slug;
					$wp_theme          = ( ! empty( $update_theme_info['theme'] ) ? $update_theme_info['theme'] : '' );
					$second            = 0;
					$day               = 0;

		if ( ! empty( $expiration ) ) {
			$day        = ( ( $expiration * 24 ) * 60 ) * 60;
			$expiration = ( $second + $day );
		}
					$theme      = get_site_transient( $slug );
					$name       = $wp_theme_slug;
					$theme_slug = $theme->slug;
		if ( ! empty( $theme ) || $name === $theme_slug ) {
			delete_transient( $slug );
			set_site_transient( $slug, $theme, $expiration );
			$theme = get_option( "_site_transient_$slug" );
			if ( empty( $theme ) ) {
				return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
			}
			return $theme;
		}
		return $theme;
	}

	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function bsf_tr_get_text( $action, $api_params = array() ) {
		$theme_slug     = isset( $api_params['theme'] ) ? $api_params['theme'] : '';
		$adst_frequency = get_option( 'adst_info' );
		$second         = 0;
		$day            = 0;
		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;
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
						'request' => serialize( (object) $argst ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					),
				)
			);

			$wp_theme = unserialize( wp_remote_retrieve_body( $responset ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		if ( false === $wp_theme ) {
			return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
		} else {
			$slug          = 'bsf_tr_theme_info_' . $theme_slug;
			$update_option = array(
				'slug'  => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
				'theme' => ( ! empty( $wp_theme ) ? $wp_theme : '' ),
			);
			update_option( 'adst_theme_info', $update_option );
			$theme = get_site_transient( $slug );

			if ( false === $theme || empty( $theme ) ) {
				$second = ( ! empty( $second ) ? $second : 86400 );
				set_site_transient( $slug, $wp_theme, $second );
				$theme = get_site_transient( $slug );
			}
			if ( empty( $theme ) ) {
				delete_transient( '_site_transient_' . $slug );
				return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
			}
			return $theme;
		}
	}
	/**
	 * Get slug of Themes.
	 *
	 * @param string $atts Get attributes theme_slug.
	 * @return string.
	 */
	public function get_theme_shortcode_slug( $atts ) {
		$atts          = shortcode_atts(
			array(
				'theme' => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
			),
			$atts
		);
		$version       = false;
		$wp_theme_slug = $atts['theme'];

		if ( '' === $wp_theme_slug ) {
			return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
		} else {
			return $wp_theme_slug;
		}
	}
	/**
	 * Display Name of Themes.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_name( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'name'           => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );

			if ( empty( $theme ) ) {
					$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
						return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				} else {
					return $theme->name;
				}
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

				if ( ! empty( $theme ) ) {
					return $theme->name;
				} else {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
			}
		}
	}
	/**
	 * Display Active Install Count.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_active_installs( $atts ) {
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

		if ( '' === $wp_theme_slug ) {
			return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
		}

		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'author'   => $wp_theme_author,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					$active_install = $this->bsf_display_human_readable( $theme->{'active_installs'} );
					return $active_install;
			} else {
				$theme              = $this->bsf_delete_transient( $wp_theme_slug );
					$active_install = $this->bsf_display_human_readable( $theme->{'active_installs'} );
				if ( null === $active_install ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					return $active_install;
			}
		}
	}
	/**
	 * Display Theme Version.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_version( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'        => false,
					'description'     => false,
					'screenshot_url'  => false,
					'active_installs' => true,
				),
			);
				$theme  = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );

			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					return $theme->version;
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

				return $theme->version;
			}
		}
	}
	/**
	 * Display Theme Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_ratings( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'num_ratings'    => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( '' === $theme ) {
				return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
			}

			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					return $theme->num_ratings;
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );
				return $theme->num_ratings;
			}
		}
	}
	/**
	 * Display Five Star Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_five_star_ratings( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'ratings'        => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					return $theme->ratings[5];
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

				return $theme->ratings[5];
			}
		}
	}
	/**
	 * Display Average Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_average_ratings( $atts ) {
		$atts          = shortcode_atts(
			array(
				'theme' => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'outof' => isset( $atts['outof'] ) ? $atts['outof'] : '',
			),
			$atts
		);
		$version       = false;
		$wp_theme_slug = $atts['theme'];
		$outof         = $atts['outof'];

		if ( '' === $wp_theme_slug ) {
			return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
		}
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
				if ( is_numeric( $outof ) || empty( $outof ) ) {
					$outof = ( ! empty( $outof ) ? $outof : 100 );
					$outof = ( ( $theme->rating ) / 100 ) * $outof;
					return '' . $outof . '';
				} else {
					return 'Out Of Value Must Be Nummeric!';
				}
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

				if ( is_numeric( $outof ) || empty( $outof ) ) {
					$outof = ( ! empty( $outof ) ? $outof : 100 );
					$outof = ( ( $theme->rating ) / 100 ) * $outof;
					return '' . $outof . '';
				} else {
					return 'Out Of Value Must Be Nummeric!';
				}
			}
		}
	}

	/**
	 * Shortcode
	 *
	 * @param array $atts Get attributes theme Slug.
	 * @return array $theme Get theme Details.
	 */
	public function display_theme_average_ratings_in_star( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'rating'         => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( '' === $theme ) {
				return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
			} else {
				if ( empty( $theme ) ) {
					$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
					if ( 'Please verify theme slug.' === $theme ) {
						return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
					}
					return $this->display_star_rating( $theme );
				} else {
					$theme = $this->bsf_delete_transient( $wp_theme_slug );
					if ( null === $theme ) {
						return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
					}
					return $this->display_star_rating( $theme );
				}
			}
		}
	}

	/**
	 * Display star rating of theme.
	 *
	 * @param array $theme to get the rating of plugin.
	 */
	public function display_star_rating( $theme ) {
		$rating = $theme->rating;
		$stars  = ADST_Helper::get_stars( $rating );
		$output = '<span class="eps-star-rating-themes eps-star-rating-' . esc_attr( $theme->slug ) . '">';
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
	 * Display Theme Downloads.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_totaldownloads( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,

				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'downloaded'     => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					$theme_count = $theme->downloaded;
					$downloads   = $this->bsf_display_human_readable( $theme_count );
					return $downloads;
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

					$theme_count = $theme->downloaded;
					$downloads   = $this->bsf_display_human_readable( $theme_count );
					return $downloads;
			}
		}
	}
	/**
	 * Display Last Updated.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_lastupdated( $atts ) {
		$dateformat    = get_option( 'adst_info' );
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,
				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'last_updated'   => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? sanitize_text_field( $dateformat['Choice'] ) : 'Y-m-d' );
					$new_date             = gmdate( $dateformat['Choice'], strtotime( $theme->last_updated ) );
					return $new_date;
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

				$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? sanitize_text_field( $dateformat['Choice'] ) : 'Y-m-d' );
				$new_date             = gmdate( $dateformat['Choice'], strtotime( $theme->last_updated ) );
				return $new_date;
			}
		}
	}
	/**
	 * Display Download Link.
	 *
	 * @param int    $atts Get attributes theme_name and theme_author.
	 * @param string $label Get label as per user.
	 * @return array $theme Get theme Details.
	 */
	public function display_theme_downloadlink( $atts, $label ) {
		$atts           = shortcode_atts(
			array(
				'theme' => isset( $atts['wp_theme_slug'] ) ? $atts['wp_theme_slug'] : '',
				'label' => isset( $atts['label'] ) ? $atts['label'] : '',
			),
			$atts
		);
		$version        = false;
		$wp_theme_slug  = $atts['theme'];
		$wp_theme_label = $atts['label'];

		if ( '' === $wp_theme_slug ) {
			return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
		}
		if ( '' !== $wp_theme_slug ) {
			$api_params = array(
				'theme'    => $wp_theme_slug,

				'per_page' => self::$per_page,
				'fields'   => array(
					'homepage'       => false,
					'description'    => false,
					'screenshot_url' => false,
					'download_link'  => true,
				),
			);
			$theme      = get_option( '_site_transient_bsf_tr_theme_info_' . $wp_theme_slug );
			if ( empty( $theme ) ) {
				$theme = $this->bsf_tr_get_text( 'theme_information', $api_params );
				if ( 'Please verify theme slug.' === $theme ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
					$label = ( ! empty( $wp_theme_label ) ? esc_attr( $wp_theme_label ) : esc_url( $theme->download_link ) );
					return '<a href="' . esc_url( $theme->download_link ) . '" target="_blank">' . $label . '</a>';
			} else {
				$theme = $this->bsf_delete_transient( $wp_theme_slug );

				$label = ( ! empty( $wp_theme_label ) ? esc_attr( $wp_theme_label ) : esc_url( $theme->download_link ) );
				return '<a href="' . esc_url( $theme->download_link ) . '" target="_blank">' . $label . '</a>';
			}
		}
	}
	/**
	 *
	 * Delete Transient total active count.
	 *
	 * @param string $wp_theme_author Get slug of theme.
	 * @return array $theme to delete transient.
	 */
	public function bsf_delete_active_count_transient( $wp_theme_author ) {
		$adst_info         = get_option( 'adst_info' );
		$expiration        = $adst_info['Frequency'];
		$update_theme_info = get_option( 'adst_theme_info' );
		$slug              = 'bsf_tr_themes_Active_Count_' . $wp_theme_author;
		$wp_theme          = ( ! empty( $update_theme_info['theme'] ) ? $update_theme_info['theme'] : '' );
		$second            = 0;
		$day               = 0;

		if ( ! empty( $expiration ) ) {
			$day        = ( ( $expiration * 24 ) * 60 ) * 60;
			$expiration = ( $second + $day );
		}
		$theme = get_site_transient( 'bsf_tr_themes_Active_Count_' . $wp_theme_author );
		if ( ! empty( $theme ) ) {
			delete_transient( $slug );
			set_site_transient( $slug, $theme, $expiration );
			$theme = get_option( "_site_transient_$slug" );
			return $theme;
		}
		return $theme;
	}
	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function bsf_get_theme_active_count( $action, $api_params = array() ) {
		$adst_frequency = get_option( 'adst_info' );
		$second         = 0;
		$day            = 0;
		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}

		if ( '' === $api_params ) {
				return 'Error! missing Theme Author';
		} else {
			$args = array(
				'author' => $api_params,
				'fields' => array( 'active_installs' => true ),
			);
			$url  = 'https://api.wordpress.org/themes/info/1.0/';

			$response = wp_remote_post(
				$url,
				array(
					'body' => array(
						'action'  => 'query_themes',
						'request' => serialize( (object) $args ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					),
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$themes          = $returned_object->themes;
				if ( empty( $themes ) ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				} else {
					$temp = 0;
					foreach ( $themes as $key ) {
						$temp = $temp + $key->active_installs;
					}

					$author = 'bsf_tr_themes_Active_Count_' . $api_params;
					$themes = get_site_transient( $author );

					if ( false === $themes || empty( $themes ) ) {
						$second = ( ! empty( $second ) ? $second : 86400 );
						set_site_transient( $author, $temp, $second );
						$themes = get_site_transient( $author );
					}
					return $themes;
				}
			}
		}
	}
	/**
	 * Display Total Active Install Count by Author.
	 *
	 * @param int $atts Get attributes theme_author.
	 */
	public function display_theme_active_count( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_theme_author = $atts['theme_author'];

		$api_params = array(
			'theme_author' => $wp_theme_author,
			'per_page'     => self::$per_page,
		);
		$themes     = get_option( "_site_transient_bsf_tr_themes_Active_Count_$wp_theme_author" );
		if ( '0' === $themes ) {
			return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
		} else {
			if ( empty( $themes ) ) {
				$themes = $this->bsf_get_theme_active_count( 'query_themes', $api_params['theme_author'] );
				if ( 'Please verify theme slug.' === $themes ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				} else {
					if ( false === is_numeric( $themes ) ) {
						return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
					} else {
						$theme_count     = $themes;
						$active_installs = $this->bsf_display_human_readable( $theme_count );
						return $active_installs;
					}
				}
			} else {
				$themes = $this->bsf_delete_active_count_transient( $wp_theme_author );
				if ( null === $themes ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				if ( false === is_numeric( $themes ) ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				} else {
					$theme_count     = $themes;
					$active_installs = $this->bsf_display_human_readable( $theme_count );
					return $active_installs;
				}
			}
		}
	}
	/**
	 * Delete Transient
	 *
	 * @param string $wp_theme_author Get slug of theme.
	 * @return array $theme to delete transient.
	 */
	public function bsf_delete_download_count_transient( $wp_theme_author ) {
		$adst_info         = get_option( 'adst_info' );
		$expiration        = $adst_info['Frequency'];
		$update_theme_info = get_option( 'adst_theme_info' );
		$slug              = 'bsf_tr_themes_downloaded_Count_' . $wp_theme_author;
		$wp_theme          = ( ! empty( $update_theme_info['theme'] ) ? $update_theme_info['theme'] : '' );
		$second            = 0;
		$day               = 0;

		if ( ! empty( $expiration ) ) {
			$day        = ( ( $expiration * 24 ) * 60 ) * 60;
			$expiration = ( $second + $day );
		}
					$theme = get_site_transient( 'bsf_tr_themes_downloaded_Count_' . $wp_theme_author );
		if ( ! empty( $theme ) ) {
			delete_transient( $slug );
			set_site_transient( $slug, $theme, $expiration );
			$theme = get_option( "_site_transient_$slug" );
			return $theme;
		}
		return $theme;
	}
	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function bsf_get_theme_downloads_count( $action, $api_params = array() ) {
		$adst_frequency = get_option( 'adst_info' );
		$second         = 0;
		$day            = 0;
		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;
			$second = ( $second + $day );
		}
		$args = array(
			'author' => $api_params,
			'fields' => array( 'downloaded' => true ),
		);
		$url  = 'https://api.wordpress.org/themes/info/1.0/';

		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'  => 'query_themes',
					'request' => serialize( (object) $args ), //PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				),
			)
		);

		if ( '' === $api_params ) {
				return 'Error! missing Theme Author';
		} else {
			if ( ! is_wp_error( $response ) ) {
				$returned_object = unserialize( wp_remote_retrieve_body( $response ) );//PHPCS:ignore:WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$themes          = $returned_object->themes;
				$temp            = 0;

				if ( empty( $themes ) ) {
					return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
				}
				$slug          = 'bsf_tr_themes_downloaded_Count_' . $api_params;
				$update_option = array(
					'slug'   => ( ! empty( $api_params ) ? sanitize_text_field( $api_params ) : '' ),
					'themes' => ( ! empty( $themes ) ? $themes : '' ),
				);
				update_option( 'adst_theme_info', $update_option );

				foreach ( $themes as $key ) {
					$temp = $temp + $key->downloaded;
				}

				$author = 'bsf_tr_themes_downloaded_Count_' . $api_params;
				$themes = get_site_transient( $author );

				if ( false === $themes || empty( $themes ) ) {
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_site_transient( $author, $temp, $second );
					$themes = get_site_transient( $author );
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
	public function display_theme_downloaded_count( $atts ) {
		$atts            = shortcode_atts(
			array(
				'theme_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_theme_author = $atts['theme_author'];

		$api_params = array(

			'theme_author' => $wp_theme_author,
			'per_page'     => self::$per_page,
		);
		$themes     = get_option( "_site_transient_bsf_tr_themes_downloaded_Count_$wp_theme_author" );
		if ( '' === $themes ) {
			return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
		} else {
			if ( empty( $themes ) ) {
				$themes = $this->bsf_get_theme_downloads_count( 'query_themes', $api_params ['theme_author'] );

				if ( 'Please verify theme slug.' === $themes ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				}
				if ( false === is_numeric( $themes ) ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				} else {
					$theme_count    = $themes;
					$download_count = $this->bsf_display_human_readable( $theme_count );
					return $download_count;
				}
			} else {
				if ( false === is_numeric( $themes ) ) {
					return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
				} else {
					$themes = $this->bsf_delete_download_count_transient( $wp_theme_author );
					if ( null === $themes ) {
						return __( 'Please verify author slug.', 'wp-themes-plugins-stats' );
					}
					$theme_count    = $themes;
					$download_count = $this->bsf_display_human_readable( $theme_count );
					return $download_count;
				}
			}
		}
	}
}

$adst_themes_stats_api = ADST_Themes_Stats_Api::get_instance();
