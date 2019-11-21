<?php
/**
 * Helper Functions.
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
 * Helper class for the .
 *
 * @since 1.0.0
 */
class ADST_Helper {
	/**
	 * Get the stars details.
	 *
	 * @param int $rating Get rating of plugins/themes.
	 * @return array $stars Get plugins/themes star rating.
	 */
	public static function get_stars( $rating ) {
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
		return $stars;
	}
}

