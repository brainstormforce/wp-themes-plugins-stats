<?php
/**
 * Responsible for setting up constants, classes and includes.
 *
 * @author BrainstormForce
 * @package WP Advanced Stats/Loader
 */

if ( ! class_exists( 'Wpas_Loader' ) ) {
	/**
	 * Responsible for setting up constants, classes and includes.
	 *
	 * @since 1.0
	 */
	final class Wpas_Loader {
		/**
		 * The unique instance of the plugin.
		 *
		 * @var Instance variable
		 */
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 */
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
			add_action( 'admin_menu', array( $this, 'bsf_wpas_add_plugin_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bsf_wpas_assets' ) );
			add_action( 'init', array( $this, 'bsf_wpas_process_form_general_settings' ) );
		}
		/**
		 * Define constants.
		 *
		 * @since 1.0
		 * @return void
		 */
		private function define_constants() {

			$file = dirname( dirname( __FILE__ ) );
			define( 'WPAS_STATS_VERSION', '1.0.0' );
			define( 'WPAS_STATS_BASE_DIR_NAME', plugin_basename( $file ) );
			define( 'WPAS_STATS_BASE_FILE', trailingslashit( $file ) . WPAS_STATS_BASE_DIR_NAME . '.php' );
			define( 'WPAS_STATS_BASE_DIR', plugin_dir_path( WPAS_STATS_BASE_FILE ) );
			define( 'WPAS_STATS_BASE_URL', plugins_url( '/', WPAS_STATS_BASE_FILE ) );
		}
		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0
		 * @return void
		 */
		private static function load_files() {
			require_once WPAS_STATS_BASE_DIR . 'includes/class-wpas-themes-stats-api.php';
			require_once WPAS_STATS_BASE_DIR . 'includes/class-wpas-plugins-stats-api.php';
		}
		/**
		 * Process plugin's Stylesheet to General setting Tab form Data.
		 */
		public function bsf_wpas_assets() {
			wp_register_style( 'bsf_wpas_stylesheet', WPAS_PLUGIN_URL . '/assets/css/wpas-style.css', null, WPAS_STATS_VERSION, false );
			wp_register_script( 'bsf_wpas_jsfile', WPAS_PLUGIN_URL . '/assets/js/wpas-human-readable.js', null, WPAS_STATS_VERSION, false );
		}
		/**
		 * WP Advanced Stats Option in Setting Page.
		 */
		public function bsf_wpas_add_plugin_page() {
			// This page will be under "Settings".
			add_options_page(
				'Settings Admin',
				'WP Advanced Stats',
				'manage_options',
				'bsf-as-setting-admin',
				array( $this, 'bsf_wpas_create_admin_page' )
			);
		}
		/**
		 * Creating Admin Page.
		 */
		public function bsf_wpas_create_admin_page() {
			require_once WPAS_STATS_BASE_DIR . 'includes/wpas-frontend.php';
		}
		/**
		 * Process plugin's General setting Tab form Data.
		 *
		 * @return Void.
		 */
		public function bsf_wpas_process_form_general_settings() {

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
					'Hrchoice'  => ( ! empty( $_POST['wpas_hr_option'] ) ? sanitize_text_field( $_POST['wpas_hr_option'] ) : '' ),
					'Rchoice'   => ( ! empty( $_POST['wpas_r_option'] ) ? sanitize_text_field( $_POST['wpas_r_option'] ) : '' ),
					'Field1'    => ( ! empty( $_POST['field1'] ) ? sanitize_text_field( $_POST['field1'] ) : 'K' ),
					'Field2'    => ( ! empty( $_POST['field2'] ) ? sanitize_text_field( $_POST['field2'] ) : 'M' ),
					'Symbol'    => ( ! empty( $_POST['wpas_number_group'] ) ? sanitize_text_field( $_POST['wpas_number_group'] ) : '' ),
				);
				update_option( 'wp_info', $update_option );
			}
		}
	}
	$wpas_loader = Wpas_Loader::get_instance();
}
