<?php
/**
 * Responsible for setting up constants, classes and includes.
 *
 * @author BrainstormForce
 * @package WP_STATS/Loader
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
			add_action( 'wp_enqueue_scripts', array($this , 'bsf_stylesheet' ));
			add_action( 'admin_menu', array( $this, 'bsf_as_add_plugin_page' ) );
           // add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
            add_action( 'init', array( $this, 'wpas_save_form_data' ) );
            add_action( 'wpas_schedule_hook', array( $this, 'wpas_schedule_event' ) );
			$this->define_constants();
			$this->load_files();
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
		}
        /**
     * Custom corn schedule for various event exchange request..
     *
     * @param string $schedules which return schedule time.
     *
     * @since  1.0.0
     * @return $schedules.
     */
    // public function cron_schedules( $schedules ) {
    //     if ( ! isset( $schedules['hourly'] ) ) {
    //         $schedules['hourly'] = array(
    //             'interval' => 60 * 60, // Every Hour.
    //             'display'  => __( 'Once hourly' ),
    //         );
    //     }
    //     if ( ! isset( $schedules['daily '] ) ) {
    //         $schedules['daily'] = array(
    //             'interval' => 24 * 3600, // Every Day.
    //             'display'  => __( 'Once daily' ),
    //         );
    //     }
    //     if ( ! isset( $schedules['weekly'] ) ) {
    //         $schedules['weekly'] = array(
    //             'interval' => 7 * 86400, // Every Week.
    //             'display'  => __( 'Once every week' ),
    //         );
    //     }
    //     return $schedules;
    // }

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0
		 * @return void
		 */
		static private function load_files() {
			require_once WP_THEMES_STATS_BASE_DIR . 'includes/class-wp-themes-stats-api.php';
            require_once WP_THEMES_STATS_BASE_DIR . 'includes/wp-as-transient.php';
		}

		public function bsf_stylesheet()
		{
			wp_enqueue_style('bsf_as_stylesheet',BSF_AS_PLUGIN_URL . '/css/as-style.css');
		}
    /**
     * Add options page
     */
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
    public function wpas_save_form_data(){
                
        $page = isset( $_GET['page'] ) ? $_GET['page'] : null;

          if ( 'bsf-as-setting-admin' !== $page ) {
              return;
          }

          if ( ! isset( $_POST['wpas-form'] ) ) {
              return;
          }

          if ( ! wp_verify_nonce( $_POST['wpas-form'], 'wpas-form-nonce' ) ) {
              return;
          }
           // $all_data = get_option('wp_as_general_settings');
           // echo '<pre>';
            // print_r($_POST);
           // wp_die();
          //  $wp_as_get_form_value = get_option( 'wp_as_form_data' );
         // $wp_as_get_form_value = get_option('wpas-form');
         //  var_dump($wp_as_get_form_value);
         //  wp_die();
        
            

                $wp_as_frequency_reload = ( ! empty( $_POST['wpas_frequency_reload'] ) ?  $_POST['wpas_frequency_reload']  : '' );
                 // var_dump($wp_as_frequency_reload);
                 // wp_die();


                // $wp_as_font_size = ( ! empty( $_POST['wp_as_font_size'] ) ? sanitize_text_field( $_POST['wp_as_font_size'] ) : '' );

                // $wp_as_background_color = ( ! empty( $_POST['wp_as_background_color'] ) ? sanitize_text_field( $_POST['wp_as_background_color'] ) : '' );

                $update_options = array(
                    'wpas_frequency_reload' => $wp_as_frequency_reload,
                    //'wp_as_font_size'       => $wp_as_font_size,
                    // 'wp_as_background_color' => $wp_as_background_color,
                );
                
                //update_option( 'cswp_form_data', $savevalues );
                update_option( 'wpas_general_settings', $update_options );
                $all_data = get_option('wpas_general_settings');
                    // var_dump($all_data);
                    // wp_die();
                 $options=get_option('cca_data');
              $new_frequency = isset( $_POST['wpas_frequency_reload'] ) ? $_POST['wpas_frequency_reload'] : '';
              $old_frequency = isset( $options['wpas_frequency_reload'] ) ? $options['wpas_frequency_reload'] : '';

              if( empty( $old_frequency ) && ! empty( $new_frequency ) ) {
                  // Schedule an action if it's not already scheduled.
                 wp_schedule_event(time(), $new_frequency, 'wpas_schedule_hook');
              } else if( ! empty( $new_frequency ) && ( $new_frequency !== $old_frequency ) ) {
                // Get the timestamp for the next event.
                $timestamp = wp_next_scheduled( 'wpas_schedule_hook' );
  
                // If this event was created with any special arguments, you need to get those too.
                if( $timestamp ) {
                    wp_unschedule_event($timestamp, 'wpas_schedule_hook' );
                }

                 // Schedule an action if it's not already scheduled.
                 wp_schedule_event(time(), $new_frequency, 'wpas_schedule_hook');
              }
    }  
    function wpas_schedule_event(){

            $url= file_get_contents('http://api.wordpress.org/stats/themes/1.0/downloads.php?slug={astra}&limit=1');
            $arr1 = json_decode($url);
            $a=update_options('current_dowmloads',$arr1);
           // var_dump($a);
           //  wp_die();
            $s='xvvbbvvbbdf';
            echo 'xnfkjngnf';
            return $s;
           //  return $arr1->{date('Y-m-d')};

    }         
    /**
     * Options page callback
     */
    function bsf_as_create_admin_page()
    {
        require_once WP_THEMES_STATS_BASE_DIR . 'includes/wp-advance-stats-frontend.php';
    }


	}

	$wp_themes_stats_loader = WP_Themes_Stats_Loader::get_instance();

}
