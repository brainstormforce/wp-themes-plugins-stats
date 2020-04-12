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
	<h2> <?php esc_html_e( 'Getting Started!', 'wp-themes-plugins-stats' ); ?></h2>
	<?php esc_html_e( 'This plugin automatically tracks theme and plugin information from the WordPress repository, store it  and display it on your website.', 'wp-themes-plugins-stats' ); ?><br></br><?php esc_html_e( 'The plugin uses shortcodes to display stats. You would just need to add theme/plugin slug/author name with respective field and paste the shortcode in the required location.', 'wp-themes-plugins-stats' ); ?>
	<label class="adst-page-title" for="howtouse"></label>
		<h2>
		<?php
		esc_html_e( 'How to Use?', 'wp-themes-plugins-stats' );
		?>
		</h2>
	<b><?php esc_attr_e( 'Step 1', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': Under the General tab set the required parameters, like time interval for updating stats, count, date format.', 'wp-themes-plugins-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 2', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': Choose and copy the shortcode from the following table.', 'wp-themes-plugins-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 3', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': Paste it on a required page/post.', 'wp-themes-plugins-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 4', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': Add a slug/author name for theme/plugin.', 'wp-themes-plugins-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 5', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': Add a field name for theme/plugin.', 'wp-themes-plugins-stats' ); ?><br><br>
	<b></b><?php esc_attr_e( ' That' . "'" . 's it! Visit Post/Page to see results.', 'wp-themes-plugins-stats' ); //PHPCS:ignore:WordPress.WP.I18n.NonSingularStringLiteralText ?><br><br><br>
<h2>
	<?php esc_attr_e( 'Shortcodes for Theme Stats', 'wp-themes-plugins-stats' ); ?>
</h2>
<table class="adst-table">
<tbody>
	<tr class="adst-table-header">
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Theme Name', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="name"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installs', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="active_installs"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Last Updated', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="last_updated"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Theme Version', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="version"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Theme Ratings Count', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="rating"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( '5 Star Ratings Count', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="star_rating"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Average Ratings in Number', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="five_rating"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Average Ratings in Star', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="num_ratings"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Downloads', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="downloaded"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Download Link', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_themes theme="theme_slug" field="download_link" label='Download Link']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installation of All Themes', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_active_count author='author_name']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Download Count of All Themes', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_theme_downloads_count author='author_name']</code>
	</td>
	</tr>
</tbody>
</table>
<ul>

	<li> <h4><?php esc_html_e( 'New Shortcode for Themes', 'wp-themes-plugins-stats' ); ?></h4> </li>

	<pre><code>[adv_stats_themes theme="theme_slug" field="parameter"]</code></pre>

	<p><?php esc_attr_e( 'For more information about the Themes', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': You can use the following parameters in the field of the themes shortcode to get the more details about theme.', 'wp-themes-plugins-stats' ); ?><br>
		<?php esc_attr_e( '( name,active_installs,last_updated,version,rating,star_rating,five_rating,num_ratings,downloaded,download_link,slug,author,homepage,description,tags )', 'wp-themes-plugins-stats' ); ?>
	</p>

</ul>
<br>
<br>
<h2>
	<?php esc_attr_e( 'Shortcodes for Plugin Stats', 'wp-themes-plugins-stats' ); ?>
</h2>
<table class="adst-table">
<tbody>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Plugin Name', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="name"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installs', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="active_installs"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Last Updated', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="last_updated"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Plugin Version', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="version"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Plugin Ratings Count', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="rating"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( '5 Star Ratings Count', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="star_rating"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Average Ratings in Number', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="five_rating"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Average Ratings in Star', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="num_ratings"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Downloads', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin='plugin_slug' field="downloaded"]</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Download Link', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_plugins plugin="plugin_slug" field="download_link" label='Download Link']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Active Installation of All Plugins', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_total_active author='author_name']</code>
	</td>
	</tr>
	<tr>
	<td class="adst-table-header-cell">
		<?php esc_attr_e( 'Total Download Count of All Plugins', 'wp-themes-plugins-stats' ); ?>
	</td>
	<td class="adst-table-header-cell">
		<code>[adv_stats_downloads_counts author='author_name']</code>
	</td>
	</tr>
</tbody>
</table>
<ul>

	<li> <h4><?php esc_html_e( 'New Shortcode for Plugins', 'wp-themes-plugins-stats' ); ?></h4> </li>

	<pre><code>[adv_stats_plugins plugin="plugin_slug" field="parameter"]</code></pre>

	<p><?php esc_attr_e( 'For more information about the Plugins', 'wp-themes-plugins-stats' ); ?></b><?php esc_attr_e( ': You can use the following parameters in the field of the plugins shortcode to get the more details about plugin.', 'wp-themes-plugins-stats' ); ?><br>
		<?php esc_attr_e(
			'( name,active_installs,last_updated,version,rating,star_rating,five_rating,num_ratings,downloaded,download_link,slug,author,author_profile,requires,tested,added,homepage,description,
			installation,screenshots,changelog,faq,support_link,tags,donate_link )',
			'wp-themes-plugins-stats'); ?>
	</p>

</ul>

