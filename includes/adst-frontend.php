<?php
/**
 * Tab on Setting Page.
 *
 * @package WP Themes & Plugins Stats
 * @author Brainstorm Force
 * @since 1.0.0
 */

// To get the tab value from URL and store in $adst_active_tab variable.
echo '<h1 class="bsf_wp_as_main_title">';
esc_attr_e( 'WP Themes & Plugins Stats', 'advanced-stats' );
echo '</h1>';
$adst_active_tab = 'wp_as_general_settings';

if ( isset( $_GET['tab'] ) ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

	if ( 'wp_as_general_settings' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		$adst_active_tab = 'wp_as_general_settings';
	} elseif ( 'wp_as_user_manual' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		$adst_active_tab = 'wp_as_user_manual';
	}
}

?>


<h2 class="nav-tab-wrapper">
<a href="?page=bsf-as-setting-admin&tab=wp_as_general_settings" class="nav-tab tb 
	<?php
	if ( 'wp_as_general_settings' === $adst_active_tab ) {
					echo 'nav-tab-active';
	}
	?>
	"><?php esc_attr_e( 'General', 'advanced-stats' ); ?></a>
		<a href="?page=bsf-as-setting-admin&tab=wp_as_user_manual" class="nav-tab tb 
		<?php
		if ( 'wp_as_user_manual' === $adst_active_tab ) {
						echo 'nav-tab-active';
		}
		?>
		"><?php esc_attr_e( 'Shortcodes', 'advanced-stats' ); ?></a>
</h2>

<?php

if ( isset( $_GET['tab'] ) ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

	if ( 'wp_as_general_settings' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		require_once 'adst-general-settings.php';
	} elseif ( 'wp_as_user_manual' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		require_once 'adst-user-manual.php';
	}
} else {
	require_once 'adst-general-settings.php';
}
?>
