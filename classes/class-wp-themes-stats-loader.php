<?php
/**
 * Responsible for setting up constants, classes and includes.
 *
 * @author BrainstormForce
 * @package WP Advanced Stats/Loader
 */

if ( ! class_exists( 'WP_Themes_Stats_Loader' ) ) {
	/**
	 * Responsible for setting up constants, classes and includes.
	 *
	 * @since 1.0
	 */
	final class WP_Themes_Stats_Loader {
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
			add_action( 'admin_menu', array( $this, 'bsf_as_add_plugin_page' ) );
			add_action( 'admin_enqueue_scripts', array($this , 'bsf_wpas_stylesheet' ));
			add_action('init',array($this,'bsf_wpas_process_form_general_settings'));
		}

		/**
		 * Define constants.
		 *
		 * @since 1.0
		 * @return void
		 */
		private function define_constants() {

			$file = dirname( dirname( __FILE__ ) );
			define( 'WP_THEMES_STATS_VERSION', '1.0.0' );
			define( 'WP_THEMES_STATS_DIR_NAME', plugin_basename( $file ) );
			define( 'WP_THEMES_STATS_BASE_FILE', trailingslashit( $file ) . WP_THEMES_STATS_DIR_NAME . '.php' );
			define( 'WP_THEMES_STATS_BASE_DIR', plugin_dir_path( WP_THEMES_STATS_BASE_FILE ) );
			define( 'WP_THEMES_STATS_BASE_URL', plugins_url( '/', WP_THEMES_STATS_BASE_FILE ) );
			// define( 'BSF_AS_PLUGIN_URL', plugins_url( '', BSF_AS_PLUGIN_URL ) );
		}
		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0
		 * @return void
		 */
		static private function load_files() {
			require_once WP_THEMES_STATS_BASE_DIR . 'includes/class-wp-themes-stats-api.php';
			require_once WP_THEMES_STATS_BASE_DIR . 'includes/class-wp-plugins-stats-api.php';
		}
		public function bsf_wpas_stylesheet()
		{
			wp_register_style( 'bsf_wpas_stylesheet', BSF_AS_PLUGIN_URL . '/css/wpas-style.css', null, WP_THEMES_STATS_VERSION , false );
		}
		public function bsf_as_add_plugin_page()
   		 {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'WP Advanced Stats', 
            'manage_options', 
            'bsf-as-setting-admin', 
            array( $this, 'bsf_as_create_admin_page' )
       		 );
   		 }
   		 function bsf_as_create_admin_page()
    	{
        	require_once WP_THEMES_STATS_BASE_DIR . 'includes/wpas-frontend.php';
    	}
    	/**
		 * Process plugin's General setting Tab form Data.
		 *
		 * @return Nothing.
		 */
		public function bsf_wpas_process_form_general_settings() {

			$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

			if ( 'bsf-as-setting-admin' !== $page ) {
				return;
			}
			if ( isset( $_POST['wpas-form'] ) && wp_verify_nonce( $_POST['wpas-form'], 'wpas-form-nonce' ) ) {

				  $update_option = array( 
			'Frequency' => (!empty($_POST['frequency']) ? sanitize_text_field($_POST['frequency']) : 1 ),
			'Choice'    => (!empty($_POST['wpasoption']) ? sanitize_text_field($_POST['wpasoption']) : 'd/m/y'),
		);
		update_option('wp_info', $update_option);
	}
}
	}
	$wp_themes_stats_loader = WP_Themes_Stats_Loader::get_instance();
}
