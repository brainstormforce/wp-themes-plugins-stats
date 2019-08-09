<?php
/**
 * Getting Started Tab on Setting Page.
 *
 * @package WP Advanced Stats
 * @author Brainstorm Force
 * @since 1.0.0
 */

// Getting Started tab.
wp_enqueue_style( 'bsf_wpas_stylesheet' );
?>
	<div class="advanced-stats-global-settings">
	<h2> <?php esc_html_e( 'Welcome to Advanced Stats!', 'advanced-stats' ); ?></h2>
	<h4><?php esc_html_e( 'The Advanced Stats plugin is built to track plugins and themes information on your website. Just paste the shortcode in the desired position!', 'advanced-stats' ); ?></h4>
	<br><label class="advanced-stats-page-title" for="howtouse"></label>
		<h2>
		<?php
		esc_html_e( 'How to Use?', 'advanced-stats' );
		?>
		</h2>
	<b><?php esc_attr_e( 'Step 1' ); ?></b><?php esc_attr_e( ': Set Frequency to update the API (by default Frequency is one day)', 'advanced-stats' ); ?>.<br><br>
	<b><?php esc_attr_e( 'Step 2' ); ?></b><?php esc_attr_e( ': Set Human Readable format if required (if unchecked then set Number symbol ).', 'advanced-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 3' ); ?></b><?php esc_attr_e( ': Set Date format if required (by default Date format is d-m-y).', 'advanced-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 4' ); ?></b><?php esc_attr_e( ': Paste the shortcode in the desired position.', 'advanced-stats' ); ?><br><br>
	<b><?php esc_attr_e( 'Step 5' ); ?></b><?php esc_attr_e( ': That' . "'" . 's it! Visit Post/Page to see results.', 'advanced-stats' ); //PHPCS:ignore:WordPress.WP.I18n.NonSingularStringLiteralText ?><br><br><br>
<div class="advanced-stats-resp-table">
				<div class="advanced-stats-resp-table-caption"><?php esc_attr_e( 'THEME SHORTCODE TABLE', 'advanced-stats' ); ?></div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Theme Name', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_name theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Total Active Installs', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_active_install theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Last Updated', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_last_updated theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Theme Version', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_version theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
						<?php esc_attr_e( 'Theme Ratings', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_ratings theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( '5 Star Ratings', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_ratings_5star theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Average Ratings', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_ratings_average theme='theme_slug' outof='5']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Total Downloads', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_downloads theme='theme_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
								<?php esc_attr_e( 'Download', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_download_link theme='theme_slug' label='label']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
								<?php esc_attr_e( 'Total Active Installation of All Themes', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_active_count author='author_name']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
								<?php esc_attr_e( 'Total Download Count of All Themes', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_theme_downloads_count author='author_name']</code>
						</div>
					</div>
				</div>
				<div class="advanced-stats-resp-table">
				<div class="advanced-stats-resp-table-caption"><?php esc_attr_e( 'PLUGIN SHORTCODE TABLE', 'advanced-stats' ); ?></div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Plugin Name', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_name plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Total Active Installs', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_active_install plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Last Updated', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_last_updated plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Plugin Version', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_version plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Plugin Ratings', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_ratings plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( '5 Star Ratings', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_ratings_5star plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Average Ratings', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_ratings_average plugin='plugin_slug' outof='5']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Total Downloads', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_downloads plugin='plugin_slug']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
								<?php esc_attr_e( 'Download', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_download_link plugin='plugin_slug' label='label']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Total Active Installation of All Plugins', 'advanced-stats' ); ?>
						</div>
						<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_total_active author='author_name']</code>
						</div>
					</div>
					<div class="advanced-stats-resp-table-header">
						<div class="advanced-stats-table-header-cell">
							<?php esc_attr_e( 'Total Download Count of All Plugins', 'advanced-stats' ); ?>
						</div>
					<div class="advanced-stats-table-body-cell">
							<code>[adv_stats_downloads_counts author='author_name']</code>
						</div>
					</div>
				</div>
	</div>
