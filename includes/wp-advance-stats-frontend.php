<?php
// Navigation.

// To get the tab value from URL and store in $active_tab variable.
echo '<h1 class="bsf_rt_main_title">';
esc_attr_e( 'WP Advanced Stats', 'wp-as' );
echo '</h1>';
$active_tab = 'wp_as_general_settings';

if ( isset( $_GET['tab'] ) ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

	if ( 'wp_as_general_settings' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		$active_tab = 'wp_as_general_settings';

	} elseif ( 'wp_as_user_manual' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		$active_tab = 'wp_as_user_manual';
	}
}

?>

<!-- WordPress provides the styling for tabs. -->

<!-- when tab buttons are clicked we jump back to the same page but with a new parameter that represents the clicked tab. accordingly we make it active -->

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
// here we display the sections and options in the settings page based on the active tab.
if ( isset( $_GET['tab'] ) ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

	if ( 'wp_as_general_settings' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
		//echo "klcgbklg";
		require_once 'wp-as-general-settings.php';

	} 
	elseif ( 'bsf_rt_user_manual' === $_GET['tab'] ) {//PHPCS:ignore:WordPress.Security.NonceVerification.Recommended

		require_once 'wp-as-user-manual.php';
	}
} else {

	require_once 'wp-as-general-settings.php';
}

?>