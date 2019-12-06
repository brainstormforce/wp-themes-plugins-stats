<?php
/**
 * Responsible for setting up constants, classes and includes.
 *
 * @author BrainstormForce
 * @package WP Themes & Plugins Stats/Loader
 */

	/**
	 * Exit if accessed directly.
	 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

	/**
	 * Responsible for setting up constants, classes and includes.
	 *
	 * @since 1.0
	 */
class ADST_Loader {
	/**
	 * The unique instance of the plugin.
	 *
	 * @var Instance variable
	 */
	private static $instance;
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
	 * Constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->load_files();
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'init', array( $this, 'process_form_general_settings' ) );
	}
	/**
	 * Define constants.
	 *
	 * @since 1.0
	 * @return void
	 */
	private function define_constants() {
		$file = dirname( dirname( __FILE__ ) );
		define( 'ADST_STATS_VERSION', '1.0.0' );
		define( 'ADST_STATS_BASE_DIR_NAME', plugin_basename( $file ) );
		define( 'ADST_STATS_BASE_FILE', trailingslashit( $file ) . ADST_STATS_BASE_DIR_NAME . '.php' );
		define( 'ADST_STATS_BASE_DIR', plugin_dir_path( ADST_STATS_BASE_FILE ) );
		define( 'ADST_STATS_BASE_URL', plugins_url( '/', ADST_STATS_BASE_FILE ) );
	}
	/**
	 * Loads classes and includes.
	 *
	 * @since 1.0
	 * @return void
	 */
	private static function load_files() {
		require_once ADST_STATS_BASE_DIR . 'includes/class-adst-helper.php';
		require_once ADST_STATS_BASE_DIR . 'includes/class-adst-themes-stats-api.php';
		require_once ADST_STATS_BASE_DIR . 'includes/class-adst-plugins-stats-api.php';
	}
	/**
	 * Process plugin's Stylesheet to General setting Tab form Data.
	 */
	public function assets() {
		wp_register_style( 'adst_stylesheet', ADST_PLUGIN_URL . '/assets/css/adst-style.css', null, ADST_STATS_VERSION, false );
		wp_register_script( 'adst_jsfile', ADST_PLUGIN_URL . '/assets/js/adst-human-readable.js', null, ADST_STATS_VERSION, false );
	}
	/**
	 * WP Advanced Stats Option in Setting Page.
	 */
	public function add_plugin_page() {
		// This page will be under "Settings".
		add_options_page(
			'Settings Admin',
			'WP Themes & Plugins Stats',
			'manage_options',
			'bsf-as-setting-admin',
			array( $this, 'adst_create_admin_page' )
		);
	}
	/**
	 * Creating Admin Page.
	 */
	public function adst_create_admin_page() {
		require_once ADST_STATS_BASE_DIR . 'includes/adst-frontend.php';
		wp_enqueue_style( 'adst_stylesheet' );
		wp_enqueue_script( 'adst_jsfile' );
	}
	/**
	 * Process plugin's General setting Tab form Data.
	 *
	 * @return Void.
	 */
	public function process_form_general_settings() {
		$page = ! empty( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

		if ( 'bsf-as-setting-admin' !== $page ) {
			return;
		}
		if ( ! empty( $_POST['wpas-form'] ) && wp_verify_nonce( sanitize_text_field( $_POST['wpas-form'] ), 'wpas-form-nonce' ) ) {
			$choice = ( ! empty( $_POST['wpasoption'] ) ? sanitize_text_field( $_POST['wpasoption'] ) : '' );
			if ( ! empty( $_POST['wpasoption'] ) && 'ok' === $_POST['wpasoption'] ) {
				$choice = ( ! empty( $_POST['wpas_date_format_custom'] ) ? sanitize_text_field( $_POST['wpas_date_format_custom'] ) : 'd-m-y' );
			}
			$update_option = array(
				'Frequency' => ( ! empty( $_POST['frequency'] ) ? sanitize_text_field( $_POST['frequency'] ) : 1 ),
				'Choice'    => $choice,
				'Rchoice'   => ( ! empty( $_POST['adst_r_option'] ) ? sanitize_text_field( $_POST['adst_r_option'] ) : '' ),
				'Field1'    => ( ! empty( $_POST['field1'] ) ? sanitize_text_field( $_POST['field1'] ) : 'K' ),
				'Field2'    => ( ! empty( $_POST['field2'] ) ? sanitize_text_field( $_POST['field2'] ) : 'M' ),
				'Symbol'    => ( ! empty( $_POST['wpas_number_group'] ) ? sanitize_text_field( $_POST['wpas_number_group'] ) : '' ),
			);
			update_option( 'adst_info', $update_option );
		}
	}
}
	$adst_loader = ADST_Loader::get_instance();
