<?php
/**
 * Plugin Name: WP Themes & Plugins Stats
 * Description:  The plugin automatically fetch information about Themes and Plugins stats with the help of WordPress.org  API. Shortcodes make it easy to display those stats anywhere on the website.
 * Version:     1.1.1
 * Author:      Brainstorm Force
 * Author URI:  https://brainstormforce.com
 * Text Domain: wp-themes-plugins-stats.
 * Main
 *
 * PHP version 7
 *
 * @category PHP
 * @package  BSF WP Themes & Plugins Stats.
 * @author   Display Name <username@brainstormforce.com>
 * @license  https://brainstormforce.com
 * @link     https://brainstormforce.com
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

define( 'ADST_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
// Including class doc loader.
require_once 'classes/class-adst-loader.php';

