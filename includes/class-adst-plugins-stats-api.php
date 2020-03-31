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
		add_shortcode( 'adv_stats_plugins', array( $this, 'shortcode' ) );
		add_shortcode( 'adv_stats_total_active', array( $this, 'shortcode_for_total_plugins_active_installs' ) );
		add_shortcode( 'adv_stats_downloads_counts', array( $this, 'shortcode_for_total_plugins_downloads' ) );
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
			'single'    => array(
				'active_installs',
				'downloaded',
				'name',
				'slug',
				'version',
				'author',
				'author_profile',
				'contributors',
				'requires',
				'tested',
				// 'compatibility',
				'rating',
				'five_rating',
				'star_rating',
				'num_ratings',
				// 'ratings',
				'last_updated',
				'added',
				'homepage',
				'description',
				'installation',
				'screenshots',
				'changelog',
				'faq',
				'short_description',
				'download_link',
				'support_link',
				'tags',
				'donate_link',
			),
			'aggregate' => array(
				'active_installs',
				'downloaded',
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
				return;
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
				$tags = (array) $plugin_data['tags'];

				if ( ! empty( $tags ) ) {
					$output = implode( ', ', $tags );
				}
				break;
			default:
				$output = isset( $plugin_data[ $atts['field'] ] ) ? $plugin_data[ $atts['field'] ] : '';
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
		 * Shortcode: Get plugins total active installs  data from wp.org.
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
					return;
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
					return;
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

}

$adst_plugins_stats_api = ADST_Plugins_Stats_Api::get_instance();
