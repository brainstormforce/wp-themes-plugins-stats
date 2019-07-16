<?php
/**
 * The WP Advance Stats general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */
wp_enqueue_style('bsf_wpas_as_stylesheet');
$wp_info = get_option('wp_info');
$frequency = (!empty($wp_info['Frequency']) ? $wp_info['Frequency'] : 1); 

?>
<div class="wp_as_global_settings" id="wp_as_global_settings">
<form method="post" name="wpas_settings_form">
<table class="wpas-form-table" >
	<br>
	<tr class="wpas-frequency">
			<th scope="row">
				<label for="UpdateFrequency"><?php esc_html_e( 'Frequency', 'wp-as' ); ?></label>
			</th>
			<td>
				
					<input class="small-text" type="input" name="frequency" id="wpas-frequency" size="5" maxlength="5" value="<?php echo $frequency ?>">
					<label><?php esc_html_e( 'Days', 'wp-as' ); ?></label>
				
			</td>
			
		</tr>
		<tr class="wpas_description">
			<td>
			</td>
			<td class="wpas_description" colspan="3">
				<p class="description wpas_description">
					<?php esc_html_e( 'Set how frequently you want to update the API calls.', 'wp-as' );?>
				</p>
			</td>
		</tr>
		<tr class="wpas-dateformat">
			<th scope="row">
				<label for="dateformat"><?php esc_html_e( 'Date Format', 'wp-as' ); ?></label>
			</th>
			<td>
				<?php
				if( $wp_info['Choice'] == '')
				{
					?> <input type="radio" name="wpasoption" value="d/m/y" checked/>
					 <?php esc_html_e( 'Day, month and two digit year ( e.g 24/03/12)', 'wp-as' );?>
					 <br></br>
						<input type="radio" name="wpasoption" value="dS F Y" />
					 <?php esc_html_e( 'Day, textual month and year ( e.g 24th March 2012)', 'wp-as' ); ?>
					<br></br>
					    <input type="radio" name="wpasoption" value="F jS Y" />
					 <?php esc_html_e( 'Textual month, day and year ( e.g March 24th, 2012)', 'wp-as' ); ?>
					<br></br>
					 	<input type="radio" name="wpasoption" value="d.m.Y" />
					 <?php esc_html_e( 'Month, day and year ( e.g 24.03.2012)', 'wp-as' ); ?>
					<br></br>
					 	<input type="radio" name="wpasoption" value="y M d" />
					 <?php esc_html_e( 'Year, month abbreviation and day ( e.g 19 Mar 24)', 'wp-as' ); 
				} else {
				?>
					<input type="radio" name="wpasoption" value="d/m/y"<?php checked( 'd/m/y' == $wp_info['Choice'] ); ?> />
					 <?php esc_html_e( 'Day, month and two digit year ( e.g 24/03/12)', 'wp-as' ); ?>
					<br></br>
						<input type="radio" name="wpasoption" value="dS F Y"<?php checked( 'dS F Y' == $wp_info['Choice'] ); ?> />
					 <?php esc_html_e( 'Day, textual month and year ( e.g 24th March 2012)', 'wp-as' ); ?>
					<br></br>
					    <input type="radio" name="wpasoption" value="F jS Y"<?php checked( 'F jS Y' == $wp_info['Choice'] ); ?> />
					 <?php esc_html_e( 'Textual month, day and year ( e.g March 24th, 2012)', 'wp-as' ); ?>
					<br></br>
					 	<input type="radio" name="wpasoption" value="d.m.Y"<?php checked( 'd.m.Y' == $wp_info['Choice'] ); ?> />
					 <?php esc_html_e( 'Month, day and year ( e.g 24.03.2012)', 'wp-as' ); ?>
					<br></br>
					 	<input type="radio" name="wpasoption" value="y M d"<?php checked( 'y M d' == $wp_info['Choice'] ); ?> />
					 <?php esc_html_e( 'Year, month abbreviation and day ( e.g 19 Mar 24)', 'wp-as' ); 
				}
				?>
			</td>
		</tr>
</table>

<table class="form-table">
<tr>
<th>
<?php wp_nonce_field( 'wpas-form-nonce', 'wpas-form' ); ?>
<input type="submit" value="Save" class="bt button button-primary" name="submit">
</th>
</tr>
</table>
</form>
</div>
<?php