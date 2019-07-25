<?php
/**
 * Tab on Setting Page.
 *
 * @package WP Advanced Stats
 * @author Brainstorm Force
 * @since 1.0.0
 */

// To get the tab value from URL and store in $active_tab variable.
echo '<h1 class="bsf_wp_as_main_title">';
esc_attr_e( 'WP Advanced Stats', 'wp-as' );
echo '</h1>';
$active_tab = 'wp_as_general_settings';

if ( isset( $_GET['tab'] ) ) {

	if ( 'wp_as_general_settings' === $_GET['tab'] ) {

		$active_tab = 'wp_as_general_settings';

	} elseif ( 'wp_as_user_manual' === $_GET['tab'] ) {

		$active_tab = 'wp_as_user_manual';
	}
}

?>


<h2 class="nav-tab-wrapper">
<a href="?page=bsf-as-setting-admin&tab=wp_as_general_settings" class="nav-tab tb 
	<?php
	if ( 'wp_as_general_settings' === $active_tab ) {
					echo 'nav-tab-active';
	}
	?>
	"><?php esc_attr_e( 'General Settings', 'wp-as' ); ?></a>
		<a href="?page=bsf-as-setting-admin&tab=wp_as_user_manual" class="nav-tab tb 
		<?php
		if ( 'wp_as_user_manual' === $active_tab ) {
						echo 'nav-tab-active';
		}
		?>
		"><?php esc_attr_e( 'Getting Started', 'wp-as' ); ?></a>
</h2>

<?php

if ( isset( $_GET['tab'] ) ) {

	if ( 'wp_as_general_settings' === $_GET['tab'] ) {

		require_once 'wpas-general-settings.php';

	} elseif ( 'wp_as_user_manual' === $_GET['tab'] ) {

		require_once 'wpas-user-manual.php';
	}
} else {

	require_once 'wpas-general-settings.php';
}
?>
