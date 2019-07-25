<?php
/**
 * Plugin Name: WP Advanced Stats
 * Plugin URI: http://brainstormforce.com/
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Contributors: brainstormforce, Anil
 * Version: 1.0.0
 * Description: This plugin is geared towards developers with Themes in the WordPress.org repository and anyone else that wants to  easily display information about a Themes that is in the repository.
 * Text Domain: wp-as
 *
 * @package WP-Advanced-Stats
 */

define( 'BSF_AS_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
// Including class doc loader.
require_once 'classes/class-wp-as-loader.php';

