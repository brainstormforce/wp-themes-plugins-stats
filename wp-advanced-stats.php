<?php
/**
 * Plugin Name: WP Advanced Stats.
 * Description:  This plugin is geared towards developers with Themes in the WordPress.org repository and anyone else that wants to  easily display information about a Themes and Plugins that is in the repository.
 * Version:     1.0.0
 * Author:      Brainstorm Force
 * Author URI:  https://brainstormforce.com
 * Text Domain: wp-as.
 * Main
 *
 * PHP version 7
 *
 * @category PHP
 * @package  BSF WP Advanced Stats.
 * @author   Display Name <username@brainstormforce.com>
 * @license  https://brainstormforce.com
 * @link     https://brainstormforce.com
 */

define( 'BSF_AS_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
// Including class doc loader.
require_once 'classes/class-wp-as-loader.php';

