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
	public function get_api_data( $action, $api_params = array() ) {
		$plugin_slug = isset( $api_params['plugin'] ) ? $api_params['plugin'] : '';

		$second = 0;

		$day = 0;

		$adst_frequency = get_option( 'adst_info' );

		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

			$second = ( $second + $day );
		}

		// Get the plugin data if it has already been stored as a transient.
		$plugin_data = get_transient( 'new_bsf_tr_plugin_info_' . esc_attr( $plugin_slug ) );

		// If there is no transient, get the plugin data from wp.org.
		if ( false === $plugin_data ) {
			$response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/' . esc_attr( $plugin_slug ) . '.json?fields=active_installs' );

			$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

			// If error in response or plugin slug incorrectly.
			if ( is_wp_error( $response ) || empty( $plugin_data ) ) {
				return __( 'Plugin slug is incorrect!', 'wp-themes-plugins-stats' );
			}

			$plugin_data = $this->sanitize_plugin_data( $plugin_data );

			$slug          = 'bsf_tr_plugin_info_' . $plugin_slug;
			$update_option = array(
				'slug'   => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
				'plugin' => ( ! empty( $plugin_data ) ? $plugin_data : '' ),
			);
			update_option( 'adst_plugin_info', $update_option );

			$plugin_db_data = get_option( 'adst_plugin_info' );// DB value.

			$second = ( ! empty( $second ) ? $second : 86400 );
			set_transient( 'bsf_tr_plugin_info_' . esc_attr( $plugin_slug ), $plugin_data, $second );
		} else {
			$second = ( ! empty( $second ) ? $second : 86400 );

			$plugin_data = $this->sanitize_plugin_data( $plugin_data );

			set_transient( 'bsf_tr_plugin_info_' . esc_attr( $plugin_slug ), $plugin_data, $second );

			$plugin_data = get_transient( 'bsf_tr_plugin_info_' . esc_attr( $plugin_slug ) );
		}

			return $plugin_data;
	}

	/**
	 * Sanitize the plugins ratings.
	 *
	 * @param array $data Get ratings attributes of plugin.
	 * @return string.
	 */
	public function sanitize_text_field( $data ) {
		return sanitize_text_field( $data );
	}

	/**
	 * Sanitize attributes of plugins api data.
	 *
	 * @param array $plugin_data Get attributes of plugin data.
	 * @return string.
	 */
	public function sanitize_plugin_data( $plugin_data ) {
		$data = array();

		$data['name'] = sanitize_text_field( $plugin_data['name'] );

		$data['slug'] = sanitize_text_field( $plugin_data['slug'] );

		$data['version'] = sanitize_text_field( $plugin_data['version'] );

		$plugin_data['ratings'] = json_decode( wp_json_encode( $plugin_data['ratings'] ), true );

		$data['ratings'] = array_map( array( $this, 'sanitize_text_field' ), $plugin_data['ratings'] );

		$data['rating'] = sanitize_text_field( $plugin_data['rating'] );

		$data['active_installs'] = sanitize_text_field( $plugin_data['active_installs'] );

		$data['num_ratings'] = sanitize_text_field( $plugin_data['num_ratings'] );

		$data['downloaded'] = sanitize_text_field( $plugin_data['downloaded'] );

		$data['last_updated'] = sanitize_text_field( $plugin_data['last_updated'] );

		$data['download_link'] = sanitize_text_field( $plugin_data['download_link'] );

		return $data;
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
	 * Display Name of Plugin.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin_name( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					return $plugin['name'];
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Active Install Count.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin_active_installs( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					return $this->bsf_display_human_readable( $plugin['active_installs'] );
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Human Readable Format
	 *
	 * @param int $plugin_count Get Count of plugin.
	 * @return float $plugin_count Get human readable format.
	 */
	public function bsf_display_human_readable( $plugin_count ) {
		$plugin_count = ( 0 + str_replace( ',', '', $plugin_count ) );
		if ( ! is_numeric( $plugin_count ) ) {
			return false;
		}
		$choice = get_option( 'adst_info' );
		if ( 'K' === $choice['Rchoice'] ) {
			return round( ( $plugin_count / 1000 ), 2 ) . $choice['Field1'];
		} elseif ( 'M' === $choice['Rchoice'] ) {
			return round( ( $plugin_count / 1000000 ), 4 ) . $choice['Field2'];
		} elseif ( 'normal' === $choice['Rchoice'] ) {
			return number_format( $plugin_count, 0, '', $choice['Symbol'] );
		}
		return $plugin_count;
	}
	/**
	 * Display Theme Version.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin__version( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					return $plugin['version'];
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Theme Ratings.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin__ratings( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					return $plugin['num_ratings'];
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Function for plugins api parameter.
	 *
	 * @param int $wp_plugin_slug Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function get_api_param_for_plugins( $wp_plugin_slug ) {
		$api_params = array(
			'plugin'   => $wp_plugin_slug,
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
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin__five_star_ratings( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					return ( $plugin['ratings']['5'] );
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Average Ratings.
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
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
				if ( is_numeric( $outof ) || empty( $outof ) ) {
					$outof = ( ! empty( $outof ) ? $outof : 100 );
					$outof = ( ( $plugin['rating'] ) / 100 ) * $outof;
					return $outof;
				} else {
					return __( 'Out Of Value Must Be Nummeric!', 'wp-themes-plugins-stats' );
				}
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Shortcode for ratings in star.
	 *
	 * @param array $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin_average_ratings_in_star( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					$five_rating = ( $plugin['rating'] / 100 ) * 5;

					$output = '<span class="adv-stats-star-rating" title="' . $five_rating . ' ' . __( 'out of 5 stars', 'wp-themes-plugins-stats' ) . '">';
					$stars  = ADST_Helper::get_stars( $plugin['rating'] );

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
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Theme Downloads.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin__totaldownloads( $atts ) {
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					return $this->bsf_display_human_readable( $plugin['downloaded'] );
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Last Updated.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_plugin__lastupdated( $atts ) {
		$dateformat     = get_option( 'adst_info' );
		$wp_plugin_slug = $this->get_plugin_shortcode_slug( $atts );
		if ( '' !== $wp_plugin_slug ) {
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					$dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? $dateformat['Choice'] : 'Y-m-d' );
					$new_date             = gmdate( $dateformat['Choice'], strtotime( $plugin['last_updated'] ) );
					return $new_date;
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
			}
		}
	}
	/**
	 * Display Download Link.
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
			$api_params = $this->get_api_param_for_plugins( $wp_plugin_slug );

			$plugin = $this->get_api_data( 'plugin_information', $api_params );

			if ( ! empty( $plugin ) ) {
					$label = ( ! empty( $wp_plugin_label ) ? esc_attr( $wp_plugin_label ) : esc_url( $plugin['download_link'] ) );
					return '<a href="' . esc_url( $plugin['download_link'] ) . '" target="_blank">' . $label . '</a>';
			} else {
				return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
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
		$author_slug = isset( $api_params['plugin_author'] ) ? $api_params['plugin_author'] : '';

		$second = 0;

		$day = 0;

		$adst_frequency = get_option( 'adst_info' );

		if ( ! empty( $adst_frequency['Frequency'] ) ) {
			$day = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

			$second = ( $second + $day );
		}

		// Get the plugin data if it has already been stored as a transient.
		$plugin_data = get_transient( 'bsf_tr_plugin_Active_Count_' . esc_attr( $author_slug ) );

		// If there is no transient, get the plugin data from wp.org.
		if ( false === $plugin_data ) {
			$response = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.1/?action=query_plugins&request[author]=' . $author_slug . '&request[fields][active_installs]=true' );

			$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );

			if ( is_wp_error( $response ) || empty( $plugin_data ) ) {
				return __( 'Plugin slug is incorrect!', 'wp-themes-plugins-stats' );
			}

				$slug          = 'bsf_tr_plugin_Active_Count_' . $author_slug;
				$update_option = array(
					'slug'   => ( ! empty( $slug ) ? sanitize_text_field( $slug ) : '' ),
					'plugin' => ( ! empty( $plugin_data ) ? $plugin_data : '' ),
				);
				update_option( 'adst_plugin_info', $update_option );

				$plugin_db_data = get_option( 'adst_plugin_info' );// DB value.

					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_plugin_Active_Count_' . esc_attr( $author_slug ), $plugin_data, $second );
		} else {
			$second = ( ! empty( $second ) ? $second : 86400 );
			set_transient( 'bsf_tr_plugin_Active_Count_' . esc_attr( $author_slug ), $plugin_data, $second );
			$plugin_data = get_transient( 'bsf_tr_plugin_Active_Count_' . esc_attr( $author_slug ) );
		}

			return $plugin_data;
	}
	/**
	 * Get author slug of Plugins.
	 *
	 * @param string $atts Get attributes plugin_slug.
	 * @return string.
	 */
	public function get_plugin_author_shortcode_slug( $atts ) {
		$atts                  = shortcode_atts(
			array(
				'plugin_author' => isset( $atts['author'] ) ? $atts['author'] : '',
			),
			$atts
		);
		$wp_plugin_author_slug = $atts['plugin_author'];
		if ( '' === $wp_plugin_author_slug ) {
			return __( 'Please verify plugin author.', 'wp-themes-plugins-stats' );
		} else {
			return $wp_plugin_author_slug;
		}
	}
	/**
	 * Shortcode
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_active_installs( $atts ) {
		$wp_plugin_author_slug = $this->get_plugin_author_shortcode_slug( $atts );

		$api_params = array(

			'plugin_author' => $wp_plugin_author_slug,
			'per_page'      => 1,

		);

		$plugin = $this->bsf_display_plugins_active_count( 'query_plugins', $api_params );

		if ( ! empty( $plugin ) ) {
				$total_active_count = 0;

				$plugins_array = $plugin['plugins'];

			foreach ( $plugins_array as $key ) {
				$total_active_count = $total_active_count + $key->active_installs;
			}

				return $this->bsf_display_human_readable( $total_active_count );
		} else {
			return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
		}
	}
	/**
	 * Display Total Download Count.
	 *
	 * @param int $atts Get attributes plugin Slug.
	 * @return array $plugin Get plugin Details.
	 */
	public function display_download_count( $atts ) {
		$wp_plugin_author_slug = $this->get_plugin_author_shortcode_slug( $atts );

		$api_params = array(

			'plugin_author' => $wp_plugin_author_slug,
			'per_page'      => 1,

		);

		$plugin = $this->bsf_display_plugins_active_count( 'query_plugins', $api_params );

		if ( ! empty( $plugin ) ) {
				$total_downloaded_count = 0;

				$plugins_array = $plugin['plugins'];

			foreach ( $plugins_array as $key ) {
				$total_downloaded_count = $total_downloaded_count + $key->downloaded;
			}

			return $this->bsf_display_human_readable( $total_downloaded_count );
		} else {
			return __( 'Plugin data is empty!', 'wp-themes-plugins-stats' );
		}
	}
}

$adst_plugins_stats_api = ADST_Plugins_Stats_Api::get_instance();
