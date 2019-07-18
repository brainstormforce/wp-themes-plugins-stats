<?php
/**
 * The WP Advance Stats general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */
wp_enqueue_style('bsf_wpas_stylesheet');
$wp_info = get_option('wp_info');
// var_dump($wp_info);
// wp_die();
$frequency = (!empty($wp_info['Frequency']) ? $wp_info['Frequency'] : 1); 
//$custom = (!empty($wp_info['Custom']) ? $wp_info['Custom'] : 'd-m-y');
$choice = (!empty($wp_info['Choice']) ? $wp_info['Choice'] : $custom);

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
				if( '' === $choice )
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
					<input type="radio" name="wpasoption" value="d/m/y"<?php checked( 'd/m/y' == $choice ); ?> />
					 <?php esc_html_e( 'd/m/y', 'wp-as' ); ?>
					 <code class="cd">(e.g 24/03/12)</code>
					<br></br>
						<input type="radio" name="wpasoption" value="dS F Y"<?php checked( 'dS F Y' == $choice ); ?> />
					 <?php esc_html_e( 'dS F Y', 'wp-as' ); ?>
					 <code class="cd">(e.g 24th March 2012)</code>
					<br></br>
					    <input type="radio" name="wpasoption" value="F jS Y"<?php checked( 'F jS Y' == $choice ); ?> />
					 <?php esc_html_e( 'F jS Y', 'wp-as' ); ?>
					 <code class="cd">(e.g March 24th, 2012)</code>
					<br></br>
					 	<input type="radio" name="wpasoption" value="d.m.Y"<?php checked( 'd.m.Y' == $choice ); ?> />
					 <?php esc_html_e( 'd.m.Y', 'wp-as' ); ?>
					 <code class="cd">(e.g 24.03.2012)</code>
					<br></br>
					 	<input type="radio" name="wpasoption" value="y M d"<?php checked( 'y M d' == $choice ); ?> />
					 <?php esc_html_e( 'y M d', 'wp-as' );?>
					 <code class="cd">(e.g 24 Mar 12)</code>
					 <?php
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