<?php
/**
 * Responsible for setting up constants, classes and includes.
 *
 * @author BrainstormForce
 * @package WP Advanced Stats/Loader
 */

if ( ! class_exists( 'WP_as_loader' ) ) {
	/**
	 * Responsible for setting up constants, classes and includes.
	 *
	 * @since 1.0
	 */
	final class WP_as_loader {
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
			//add_action( 'admin_enqueue_scripts', array( $this, 'bsf_wpas_jsfile' ) );
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
			define( 'WP_AS_STATS_VERSION', '1.0.0' );
			define( 'WP_AS_STATS_BASE_DIR_NAME', plugin_basename( $file ) );
			define( 'WP_AS_STATS_BASE_FILE', trailingslashit( $file ) . WP_AS_STATS_BASE_DIR_NAME . '.php' );
			define( 'WP_AS_STATS_BASE_DIR', plugin_dir_path( WP_AS_STATS_BASE_FILE ) );
			define( 'WP_AS_STATS_BASE_URL', plugins_url( '/', WP_AS_STATS_BASE_FILE ) );
		}
		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0
		 * @return void
		 */
		static private function load_files() {
			require_once WP_AS_STATS_BASE_DIR . 'includes/class-wp-themes-stats-api.php';
			require_once WP_AS_STATS_BASE_DIR . 'includes/class-wp-plugins-stats-api.php';
		}
		/**
		 * Process plugin's Stylesheet to General setting Tab form Data.
		 */
		public function bsf_wpas_assets() {
			wp_register_style( 'bsf_wpas_stylesheet', BSF_AS_PLUGIN_URL . '/css/wpas-style.css', null, WP_AS_STATS_VERSION, false );
			wp_register_script( 'bsf_wpas_jsfile', BSF_AS_PLUGIN_URL . '/js/wpas-human-readable.js', null, WP_AS_STATS_VERSION, false );
		}
		/**
		 * Process plugin's Javascript to General setting Tab form Data(human Readable format unable).
		 */
		// public function bsf_wpas_jsfile() {
		//   wp_register_script( 'bsf_wpas_jsfile', BSF_AS_PLUGIN_URL . '/js/wpas-human-readable.js', null, WP_AS_STATS_VERSION, false );
		// }
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
		function bsf_wpas_create_admin_page() {
			require_once WP_AS_STATS_BASE_DIR . 'includes/wpas-frontend.php';
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
			if ( ! empty( $_POST['wpas-form'] ) && wp_verify_nonce( $_POST['wpas-form'], 'wpas-form-nonce' ) ) {
				$choice = ( ! empty( $_POST['wpasoption'] ) ? sanitize_text_field( $_POST['wpasoption'] ) : '' );
				if ( ! empty( $_POST['wpasoption'] ) && 'ok' === $_POST['wpasoption'] ) {

					$choice = $_POST['date_format_custom'];
				}

				$update_option = array(
					'Frequency' => ( ! empty( $_POST['frequency'] ) ? sanitize_text_field( $_POST['frequency'] ) : 1 ),
					'Choice'    => $choice,
					'Hrchoice'  => ( ! empty( $_POST['wpas_hr_option'] ) ? $_POST['wpas_hr_option'] : '' ),
					'Rchoice'   => ( ! empty( $_POST['wpas_r_option'] ) ? $_POST['wpas_r_option'] : '' ),
					'Symbol'    => ( ! empty( $_POST['wpas_number_group'] ) ? $_POST['wpas_number_group'] : '' ),

				);

				update_option( 'wp_info', $update_option );
			}
		}
	}
	$wp_as_loader = WP_as_Loader::get_instance();
}
