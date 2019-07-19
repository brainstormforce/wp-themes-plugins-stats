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
$frequency = (!empty($wp_info['Frequency']) ? $wp_info['Frequency'] : 1); 
$choice = (!empty($wp_info['Choice']) ? $wp_info['Choice'] :'d-m-y');
$hrchoice = (!empty($wp_info['Hrchoice']) ? $wp_info['Hrchoice'] : '0');

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
		<tr class="wpas-hrformat">
			<th scope="row">
				<label for="hrformat"><?php esc_html_e( 'Human Readable Format', 'wp-as' ); ?></label>
			</th>
			<td>
				<?php
				if( '' === $hrchoice )
				{
					?> <input type="checkbox" name="wpas_hr_option" value="1" checked/> 
					 <?php esc_html_e( 'Readable Formats', 'wp-as' );
				}else{?>
				<input type="checkbox" name="wpas_hr_option" value="1"<?php checked( '1' == $hrchoice ); ?> />
					 <?php esc_html_e( 'Readable Formats', 'wp-as' ); 
				}?>
			</td>
		</tr>
		<tr class="wpas_description">
			<td>
			</td>
			<td class="wpas_description" colspan="3">
				<p class="description wpas_description">
					<?php esc_html_e( 'Set human readable to display active installs and downloads counts i.e 247,704,360 -> 247.7 million.', 'wp-as' );?>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php esc_html_e('Date Format','wp-as') ?></label></th>
				<td class="wpas-date">
					<fieldset>
					<?php
						$date_formats = array_unique( apply_filters( 'date_formats', array( __('F j, Y'), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );
						$format = 'd-m-y';
						$custom = true;
						foreach ( $date_formats as $format ) {
						echo "\t<label><input type='radio' name='wpasoption' value='" . esc_attr( $format ) . "'";
						if ( $choice === $format ) {
							echo " checked='checked'";
							$custom = false;
						}
						echo ' /> <span class="wpas-date-time-text format-i18n">' . date_i18n( $format ) .'</span><code class="cd">'. esc_html( $format )."</code></label><br />\n";
						}
						echo '<label><input type="radio" name="wpasoption" id="date_format_custom_radio" value="ok"';
						checked( 'ok' == $custom);
						echo '/> <span class="wpas-date-time-text date-time-custom-text">' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom date format in the following field' ) . '</span></span></label>' .
							'<input type="text" name="date_format_custom" id="date_format_custom" value="' . $choice . '" class="small-text" />';
					?>
					</fieldset>
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