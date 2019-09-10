<?php
/**
 * Getting Started Tab on Setting Page.
 *
 * @package WP Themes & Plugins Stats
 * @author Brainstorm Force
 * @since 1.0.0
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// Getting Started tab.

?>
	<div class="adst-global-settings">
	<h2> <?php esc_html_e( 'Getting Started!', 'advanced-stats' ); ?></h2>
	<?php esc_html_e( 'This plugin automatically tracks theme and plugin information from the WordPress repository, store it  and display it on your website.', 'advanced-stats' ); ?><br></br><?php esc_html_e( 'The plugin uses shortcodes to display stats. You would just need to add theme/plugin slug/author name and paste the shortcode in the required location.', 'advanced-stats' ); ?>
	<label class="adst-page-title" for="howtouse"></label>
		<h2>
		<?php
		esc_html_e( 'How to Use?', 'advanced-stats' );
		?>
		</h2>
	<b><?php esc_attr_e( 'Step 1', 'advanced-stats' ); ?></b><?php esc_attr_e( ': Under the General tab set the required parameters, like time interval for updating stats, count, date format.', 'advanced-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 2', 'advanced-stats' ); ?></b><?php esc_attr_e( ': Choose and copy the shortcode from the following table.', 'advanced-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 3', 'advanced-stats' ); ?></b><?php esc_attr_e( ': Paste it on a required page/post.', 'advanced-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 4', 'advanced-stats' ); ?></b><?php esc_attr_e( ': Add a slug/author name for theme/plugin.', 'advanced-stats' ); ?><br><br>
	<b></b><?php esc_attr_e( ' That' . "'" . 's it! Visit Post/Page to see results.', 'advanced-stats' ); //PHPCS:ignore:WordPress.WP.I18n.NonSingularStringLiteralText ?><br><br><br>
<h2>
	<?php esc_attr_e( 'Shortcodes for Theme Stats', 'advanced-stats' ); ?>
</h2>
<table class="adst-table">
<tbody>
	<tr class="adst-table-header">
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Theme Name', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_name theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installs', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_active_install theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Last Updated', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_last_updated theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Theme Version', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_version theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Theme Ratings', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_ratings theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( '5 Star Ratings', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_ratings_5star theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Average Ratings', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_ratings_average theme='theme_slug' outof='5']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Downloads', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_downloads theme='theme_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Download Link', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_download_link theme='theme_slug' label='Download Link']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installation of All Themes', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_active_count author='author_name']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Download Count of All Themes', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_downloads_count author='author_name']</code>
	</td>
	</tr>
</tbody>
</table>
<br>
<h2>
	<?php esc_attr_e( 'Shortcodes for Plugin Stats', 'advanced-stats' ); ?>
</h2>
<table class="adst-table">
<tbody>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Plugin Name', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_name plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installs', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_active_install plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Last Updated', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_last_updated plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Plugin Version', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_version plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Plugin Ratings', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_ratings plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( '5 Star Ratings', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_ratings_5star plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Average Ratings', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_ratings_average plugin='plugin_slug' outof='5']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Downloads', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_downloads plugin='plugin_slug']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Download Link', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_download_link plugin='plugin_slug' label='Download Link']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installation of All Plugins', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_total_active author='author_name']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Download Count of All Plugins', 'advanced-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_downloads_counts author='author_name']</code>
	</td>
	</tr>
</tbody>
</table>
