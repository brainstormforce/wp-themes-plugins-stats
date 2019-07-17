<?php
/**
 * Getting Started Tab on Setting Page.
 *
 * @package WP Advanced Stats
 * @author Brainstorm Force
 * @since 1.0.0
 */
// Getting Started tab
wp_enqueue_style('bsf_wpas_stylesheet');
	?>
	<h2> <?php esc_html_e('Welcome to Advanced Stats!','wp-as'); ?></h2>
	<p><?php esc_html_e('The Advanced Stats plugin is built to track plugins and themes information on your website. Just paste the shortcode in the desired position!','wp-as')?></p>
	<br><label class="wpas_page_title" for="howtouse">
		<?php
		echo 'How to Use? </label><br><br>
	<b>Step 1</b> : Set frequency to update the API (by default Frequency is one day).<br><br>
	<b>Step 2</b> : Set Date format if required (by default Date format is y-m-d).<br><br>
	<b>Step 3</b> : Paste the shortcode in the desired position.<br><br>
	<b>Step 4</b> : That' . "'" . 's it! Visit Post/Page to see results.  <br><br><br>';
		?>
		<div class="wp-asresp-table">
	   			<div class="wp-asresp-table-caption"><?php  esc_attr_e('THEME SHORTCODE TABLE','wp-as'); ?></div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Theme Name','wp-as'); ?> 
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_name theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Total Active Installs','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_active_install theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Last Updated','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_last_updated theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Theme Version','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_version theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
						<?php esc_attr_e('Theme Ratings','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_ratings theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('5 Star Ratings','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_ratings_5star theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Average Ratings','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_ratings_average theme='theme_slug' outof='5']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Total Downloads','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_theme_downloads theme='theme_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							 <?php esc_attr_e('Download','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							 [adv_stats_theme_download_link theme='theme_slug' label='label']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							 <?php esc_attr_e('Total Active Installation of All Themes','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							 [adv_stats_theme_active_count author='author_name']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							 <?php esc_attr_e('Total Download Count of All Themes','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							 [adv_stats_theme_downloads_count author='author_name']
						</div>
					</div>
				</div>
				<div class="wp-asresp-table">
	   			<div class="wp-asresp-table-caption"><?php esc_attr_e('PLUGIN SHORTCODE TABLE','wp-as'); ?></div>
	   				<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('plugin Name','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							 [adv_stats_name plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Total Active Installs','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_active_install plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Last Updated','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_last_updated plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Plugin Version','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_version plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Plugin Ratings','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_ratings plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('5 Star Ratings','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_ratings_5star plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Average Ratings','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_ratings_average plugin='plugin_slug' outof='5']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							<?php esc_attr_e('Total Downloads','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							[adv_stats_downloads plugin='plugin_slug']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							 <?php esc_attr_e('Download','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							  [adv_stats_download_link plugin='plugin_slug' label='label']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							 <?php esc_attr_e('Total Active Installation of All Plugins','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							 [adv_stats_total_active author='author_name']
						</div>
					</div>
					<div class="wp-asresp-table-header">
						<div class="wp-astable-header-cell">
							 <?php esc_attr_e('Total Download Count of All Plugins','wp-as'); ?>
						</div>
						<div class="wp-astable-body-cell">
							 [adv_stats_downloads_counts author='author_name']
						</div>
					</div>
				</div>
	<?php
?>