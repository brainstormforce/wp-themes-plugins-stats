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
	 * Convert number into particular format.
	 *
	 * @param int $theme_count Get Count of theme.
	 * @return float $theme_count Get human readable format.
	 */
	public function bsf_display_human_readable( $theme_count ) {
		if(!empty($theme_count)){
			$theme_count = ( 0 + str_replace( ',', '', $theme_count ) );
			if ( ! is_numeric( $theme_count ) ) {
				return false;
			} elseif ( null === $theme_count ) {
				return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
			}
			$choice = get_option( 'adst_info' );
			if ( 'K' === $choice['Rchoice'] ) {
					return round( ( $theme_count / 1000 ), 2 ) . $choice['Field1'];
			} elseif ( 'M' === $choice['Rchoice'] ) {
				return round( ( $theme_count / 1000000 ), 3 ) . $choice['Field2'];
			} elseif ( 'normal' === $choice['Rchoice'] ) {
					return number_format( $theme_count, 0, '', $choice['Symbol'] );
			}
			return $theme_count;
		}
	}

	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function get_api_data( $action, $api_params = array() ) {
		$theme_slug = isset( $api_params['theme'] ) ? $api_params['theme'] : '';

		$second = 0;

		$day = 0;

		$adst_frequency = get_option( 'adst_info' );

		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

			$second = ( $second + $day );
		}

		// Get the theme data if it has already been stored as a transient.
		$theme_data = get_transient( 'bsf_tr_theme_info_' . esc_attr( $theme_slug ) );

		// If there is no transient, get the theme data from wp.org.
		if ( false === $theme_data ) {
			$response = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=' . esc_attr( $theme_slug ) . '&request[fields][ratings]=true&request[fields][versions]=true&request[fields][active_installs]=true' );

			$theme_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

			if ( is_wp_error( $response ) || empty( $theme_data ) ) {
				return __( 'Please verify theme slug.', 'wp-themes-plugins-stats' );
			}

			$theme_data = $this->sanitize_theme_data( $theme_data );

			$slug          = 'bsf_tr_theme_info_' . $theme_slug;
			$update_option = array(
				'slug'  => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
				'theme' => ( ! empty( $theme_data ) ? $theme_data : '' ),
			);
			update_option( 'adst_theme_info', $update_option );

			$theme_db_data = get_option( 'adst_theme_info' );// DB value.

			$second = ( ! empty( $second ) ? $second : 86400 );
			set_transient( 'bsf_tr_theme_info_' . esc_attr( $theme_slug ), $theme_data, $second );
		} else {
			$second     = ( ! empty( $second ) ? $second : 86400 );
			$theme_data = $this->sanitize_theme_data( $theme_data );
			set_transient( 'bsf_tr_theme_info_' . esc_attr( $theme_slug ), $theme_data, $second );
			$theme_data = get_transient( 'bsf_tr_theme_info_' . esc_attr( $theme_slug ) );
		}

			return $theme_data;
	}

	/**
	 * Sanitize Themes ratings.
	 *
	 * @param array $data Get attributes of theme ratings.
	 * @return string.
	 */
	public function sanitize_text_field( $data ) {
		return sanitize_text_field( $data );
	}

	/**
	 * Sanitize attributes of theme api data.
	 *
	 * @param array $theme_data Get attributes of theme data.
	 * @return string.
	 */
	public function sanitize_theme_data( $theme_data ) {
		$data = array();

		$data['name'] = sanitize_text_field( $theme_data['name'] );

		$data['slug'] = sanitize_text_field( $theme_data['slug'] );

		$data['version'] = sanitize_text_field( $theme_data['version'] );

		if (!empty($theme_data['ratings'])) {

		$theme_data['ratings'] = json_decode( wp_json_encode( $theme_data['ratings'] ), true );

		$data['ratings'] = array_map( array( $this, 'sanitize_text_field' ), $theme_data['ratings'] );

		}

		$data['rating'] = sanitize_text_field( $theme_data['rating'] );

		$data['active_installs'] = sanitize_text_field( $theme_data['active_installs'] );

		$data['num_ratings'] = sanitize_text_field( $theme_data['num_ratings'] );

		$data['downloaded'] = sanitize_text_field( $theme_data['downloaded'] );

		$data['last_updated'] = sanitize_text_field( $theme_data['last_updated'] );

		$data['download_link'] = sanitize_text_field( $theme_data['download_link'] );

		return $data;
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
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					return $theme['name'];
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
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

		$wp_theme_slug = $atts['theme'];

		$wp_theme_author = $atts['theme_author'];

		if ( '' !== $wp_theme_slug ) {
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					return $this->bsf_display_human_readable( $theme['active_installs'] );
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
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
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					return $theme['version'];
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
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
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					return $theme['num_ratings'];
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Function for themes ratings api parameter.
	 *
	 * @param int $wp_theme_slug Get attributes plugin Slug.
	 * @return array $theme Get themes Details.
	 */
	public function get_api_param_for_theme( $wp_theme_slug ) {
		$api_params = array(
			'theme'    => $wp_theme_slug,
			'per_page' => self::$per_page,
			'fields'   => array(
				'homepage'        => false,
				'description'     => false,
				'screenshot_url'  => false,
				'rating'          => true,
				'active_installs' => true,
				'downloaded'      => true,
				'name'            => true,
				'slug'            => true,
				'version'         => true,
				'author'          => true,
				'five_rating'     => true,
				'star_rating'     => true,
				'num_ratings'     => true,
				'last_updated'    => true,
				'download_link'   => true,
			),
		);

		return $api_params;
	}
	/**
	 * Display Five Star Ratings.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_five_star_ratings( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					return $theme['ratings']['5'];
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
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

		if ( '' !== $wp_theme_slug ) {
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
				if ( is_numeric( $outof ) || empty( $outof ) ) {
					$outof = ( ! empty( $outof ) ? $outof : 100 );
					$outof = ( ( $theme['rating'] ) / 100 ) * $outof;
					return $outof;
				} else {
					return __( 'Out Of Value Must Be Nummeric!', 'wp-themes-plugins-stats' );
				}
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}

	/**
	 * Shortcode for average ratings in star.
	 *
	 * @param array $atts Get attributes theme Slug.
	 * @return array $theme Get theme Details.
	 */
	public function display_theme_average_ratings_in_star( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					$five_rating = ( $theme['rating'] / 100 ) * 5;

					$output = '<span class="adv-stats-star-rating" title="' . $five_rating . ' ' . __( 'out of 5 stars', 'wp-themes-plugins-stats' ) . '">';
					$stars  = ADST_Helper::get_stars( $theme['rating'] );

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
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Theme Downloads.
	 *
	 * @param int $atts Get attributes theme_name and theme_author.
	 */
	public function display_theme_totaldownloads( $atts ) {
		$wp_theme_slug = $this->get_theme_shortcode_slug( $atts );
		if ( '' !== $wp_theme_slug ) {
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					return $this->bsf_display_human_readable( $theme['downloaded'] );
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
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
			$api_params = $this->get_api_param_for_theme( $wp_theme_slug );

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
					$new_date             = gmdate( $dateformat['Choice'], strtotime( $theme['last_updated'] ) );
					return $new_date;
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
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

			$theme = $this->get_api_data( 'theme_information', $api_params );

			if ( ! empty( $theme ) ) {
					$label = ( ! empty( $wp_theme_label ) ? esc_attr( $wp_theme_label ) : esc_url( $theme['download_link'] ) );
					return '<a href="' . esc_url( $theme['download_link'] ) . '" target="_blank">' . $label . '</a>';
			} else {
				return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}

	/**
	 * Sanitize attributes of themes api data.
	 *
	 * @param array $theme_data Get attributes of themes data.
	 * @return string.
	 */
	public function sanitize_themes_data( $theme_data ) {
		$data = array();

		$data['name'] = sanitize_text_field( $theme_data->name );

		$data['slug'] = sanitize_text_field( $theme_data->slug );

		$data['version'] = sanitize_text_field( $theme_data->version );

		$data['active_installs'] = sanitize_text_field( $theme_data->active_installs );

		$data['downloaded'] = sanitize_text_field( $theme_data->downloaded );

		return $data;
	}
	/**
	 * Get the theme Details.
	 *
	 * @param int $action Get attributes theme Details.
	 * @param int $api_params Get attributes theme Details.
	 * @return array $theme Get theme Details.
	 */
	public function bsf_get_theme_active_count( $action, $api_params = array() ) {
		$author_slug = isset( $api_params['theme_author'] ) ? $api_params['theme_author'] : '';

		$second = 0;

		$day = 0;

		$adst_frequency = get_option( 'adst_info' );

		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

			$second = ( $second + $day );
		}

		// Get the plugin data if it has already been stored as a transient.
		$themes_data = get_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $author_slug ) );
		
		// If there is no transient, get the plugin data from wp.org.
		if ( false === $themes_data ) {
			$response = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=query_themes&request[author]=' . $author_slug . '&request[fields][active_installs]=true&request[fields][downloaded]=true' );

			$themes_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

			if ( is_wp_error( $response ) || empty( $themes_data ) ) {
				return __( 'Author slug is incorrect!', 'wp-themes-plugins-stats' );
			}

			$themes_array = $themes_data['themes'];

			$total_active_count = 0;

			$total_downloaded_count = 0;

			foreach ( $themes_array as $key ) {
				$themes_data = $this->sanitize_themes_data( $key );

				$total_active_count = $total_active_count + $themes_data['active_installs'];

				$total_downloaded_count = $total_downloaded_count + $themes_data['downloaded'];
			}

				$slug          = 'bsf_tr_themes_Active_Count_' . $author_slug;
				$update_option = array(
					'slug'                   => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
					'total_active_count'     => ( ! empty( $total_active_count ) ? $total_active_count : '' ),
					'total_downloaded_count' => ( ! empty( $total_downloaded_count ) ? $total_downloaded_count : '' ),
				);
				update_option( 'adst_themes_info', $update_option );

				$theme_db_data = get_option( 'adst_themes_info' );// DB value.

				$second = ( ! empty( $second ) ? $second : 86400 );
				set_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $author_slug ), $theme_db_data, $second );
		} else {
			$theme_db_data = get_option( 'adst_themes_info' );// DB value.

			$second = ( ! empty( $second ) ? $second : 86400 );

			set_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $author_slug ), $theme_db_data, $second );

			$themes_data = get_transient( 'bsf_tr_themes_Active_Count_' . esc_attr( $author_slug ) );
		}

			return $themes_data;
	}

	/**
	 * Get author slug of Plugins.
	 *
	 * @param string $atts Get attributes plugin_slug.
	 * @return string.
	 */
	public function get_theme_author_shortcode_slug( $atts ) {
		$atts                 = shortcode_atts(
			array(
				'theme_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_theme_author_slug = $atts['theme_author'];
		if ( '' === $wp_theme_author_slug ) {
			return __( 'Please verify theme author.', 'wp-themes-plugins-stats' );
		} else {
			return $wp_theme_author_slug;
		}
	}

	/**
	 * Display Total Active Install Count by Author.
	 *
	 * @param int $atts Get attributes theme_author.
	 */
	public function display_theme_active_count( $atts ) {
		$wp_theme_author_slug = $this->get_theme_author_shortcode_slug( $atts );

		$api_params = array(
			'theme_author' => $wp_theme_author_slug,
			'per_page'     => self::$per_page,
		);

		$theme = $this->bsf_get_theme_active_count( 'query_plugins', $api_params );
		
		if ( ! empty( $theme ) ) {
				return $this->bsf_display_human_readable( $theme['total_active_count'] );
		} else {
			return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
		}
	}
	/**
	 * Display Total Download Count.
	 *
	 * @param int $atts Get attributes theme_author.
	 */
	public function display_theme_downloaded_count( $atts ) {
		$wp_theme_author_slug = $this->get_theme_author_shortcode_slug( $atts );

		$api_params = array(

			'theme_author' => $wp_theme_author_slug,
			'per_page'     => self::$per_page,
		);

		$theme = $this->bsf_get_theme_active_count( 'query_plugins', $api_params );

		if ( ! empty( $theme ) ) {
				return $this->bsf_display_human_readable( $theme['total_downloaded_count'] );
		} else {
			return __( 'Themes data is empty!', 'wp-themes-plugins-stats' );
		}
	}
}

$adst_themes_stats_api = ADST_Themes_Stats_Api::get_instance();
