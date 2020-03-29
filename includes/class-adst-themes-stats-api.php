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
		add_shortcode( 'adv_stats_themes', array( $this, 'shortcode' ) );
	}

		/**
		 * Shortcode: Get themes data from wp.org
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts  An array shortcode attributes
		 */
		public function shortcode( $atts ) {
			
			$atts = shortcode_atts( array( 
				'type'		 => 'single',
				'slug' 	  	 => '',
				'field'      => 'active_installs', 
				'label'	 => '',
			), $atts );
		
			// The list of currently allowed fields
			$allowed_fields = array( 
				'single' => array( 
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
					//'compatibility',
					'rating',
					'five_rating',
					'star_rating',
					'num_ratings',
					//'ratings',
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
					'downloaded'
				)
			);
			
			// Return early is an incorrect field is passed
			if ( ! in_array( $atts['field'], $allowed_fields[ $atts['type'] ] ) ) {
				return "Theme Not Found";
			}

			$second         = 0;

			$day            = 0;

			$adst_frequency = get_option( 'adst_info' );

			if ( ! empty( $adst_frequency['Frequency'] ) ) {

				$day    = ( ( $adst_frequency['Frequency'] * 24 ) * 60 ) * 60;

				$second = ( $second + $day );

			}

	           // Get the themes data if it has already been stored as a transient
				$theme_data = get_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['slug'] ) );

				// If there is no transient, get the themes data from wp.org
				if ( ! $theme_data ) {
					$response = wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]='. esc_attr( $atts['slug'] ).'&request[fields][ratings]=true&request[fields][versions]=true&request[fields][active_installs]=true');
			
					if ( is_wp_error( $response ) ) {
						return;
					} else {
						$theme_data = (array) json_decode( wp_remote_retrieve_body( $response ) );
				
						// If someone typed in the themes slug incorrectly, the body will return null
						if ( ! empty( $theme_data ) ) {
							$second = ( ! empty( $second ) ? $second : 86400 );
							set_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['slug'] ), $theme_data, $second );
						} else {
							return "Theme slug is incorrect!";
						}
					}
				} else{
					$second = ( ! empty( $second ) ? $second : 86400 );
					set_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['slug'] ), $theme_data, $second );
					$theme_data = get_transient( 'bsf_tr_theme_info_' . esc_attr( $atts['slug'] ) );
				}

				$output = $this->field_output( $atts, $theme_data );

				return $output;
		}

		/**
		 * Helper function for generating all field output
		 *
		 * @since 1.0.0
		 *
		 * @param array $atts         An array shortcode attributes
		 * @param array $theme_data  An array of all retrived theme data from wp.org
		 */
		public function field_output( $atts, $theme_data ) {		
			// Generate the shortcode output, some fields need special handling
			switch ( $atts['field'] ) {
				case 'active_installs':
					$output = $this->bsf_display_human_readable( $theme_data[ 'active_installs' ] );
					break;
				case 'downloaded':
					$output = $this->bsf_display_human_readable( $theme_data[ 'downloaded' ] );
					break;   
				case 'contributors':
					$contributors = (array) $theme_data[ 'contributors' ];
			
					if ( ! empty ( $contributors ) ) {
						foreach ( $contributors as $contributor => $link ) {
							$output[] = '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $contributor ) . '</a>';
						}
						$output = implode( ', ', $output );
					}
					break;
				case 'five_rating':
					$rating = $theme_data[ 'rating' ];
			
					if ( ! empty ( $rating ) ) {
						$output = ( $rating / 100 ) * 5;
					}
					break;
				case 'star_rating':
					$rating = $theme_data[ 'rating' ];
			
					if ( ! empty ( $rating ) ) {
						$five_rating = ( $rating / 100 ) * 5;
				
						$output = '<span class="adv-stats-star-rating" title="' . $five_rating . " " . __( 'out of 5 stars', 'wp-themes-plugins-stats' ) . '">';
						$stars  = ADST_Helper::get_stars( $rating );
				
						foreach( $stars as $star ) {
							if ( $star == 0 ) {
								$output .= '<span class="dashicons dashicons-star-empty" style=" color: #ffb900;"></span>';
							} else if ( $star == 5 ) {
								$output .= '<span class="dashicons dashicons-star-half" style=" color: #ffb900;"></span>';
							} else if ( $star == 1 ) {
								$output .= '<span class="dashicons dashicons-star-filled" style=" color: #ffb900;"></span>';
							}
						}
				
						$output .= '</span>';
					}	
					break;
				case 'last_updated':
					$dateformat           = get_option( 'adst_info' );
				    $dateformat['Choice'] = ( ! empty( $dateformat['Choice'] ) ? sanitize_text_field( $dateformat['Choice'] ) : 'Y-m-d' );
					$output               = gmdate( $dateformat['Choice'], strtotime( $theme_data['last_updated'] ) );
					break;
				case 'description':
					$sections = (array) $theme_data['sections'];
					$output   = $sections['description'];
					break;
				case 'installation':
					$sections = (array) $theme_data['sections'];
					$output   = $sections['installation'];
					break;
				case 'screenshots':
					$sections = (array) $theme_data['sections'];
					$output   = $sections['screenshots'];
					break;
				case 'changelog':
					$sections = (array) $theme_data['sections'];
					$output   = $sections['changelog'];
					break;
				case 'faq':
					$sections = (array) $theme_data['sections'];
					$output   = $sections['faq'];
					break;
				case 'download_link':
				    $label = isset( $atts['label'] ) ? $atts['label'] : '';
					$link = $theme_data[ 'download_link' ];
					$label = ( ! empty( $label ) ? esc_attr( $label ) : esc_url( $link ) );				
					$output = '<a href="' . esc_url( $link ) . '" target="_blank">' . $label . '</a>';
					break;
				case 'support_link':
					$slug = $theme_data[ 'slug' ];
					$output = 'https://wordpress.org/support/plugin/' . $slug;
					break;
				case 'tags':
					$tags = (array) $theme_data[ 'tags' ];
					if ( ! empty( $tags ) ) {
						$output = implode( ', ', $tags );
					}
					break;
				default:
					$output = $theme_data[ $atts['field'] ];
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

$adst_themes_stats_api = ADST_Themes_Stats_Api::get_instance();