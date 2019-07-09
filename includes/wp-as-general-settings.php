<?php
/**
 * The WP Advance Stats general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */
$option = get_option('wp_as_general_settings');
$wp_as_frequency_reload = isset( $wp_as_get_form_value['frequency_reload'] ) ? $wp_as_get_form_value['frequency_reload'] : '';
$wp_as_font_size = ( ! empty( $options['wp_as_font_size'] ) ? $options['wp_as_font_size'] : 10 );
$wp_as_background_color = ( ! empty( $options['wp_as_background_color'] ) ? $options['wp_as_background_color'] : '' );

?>
<div class="wp_as_global_settings" id="wp_as_global_settings">
<form method="post" name="wp_as_settings_form">
<table class="form-table" >
	<br>
	<tr class="wp-as-frequency">
			<th scope="row">
				<label for="UpdateFrequency"><?php esc_html_e( 'Frequency', 'wp-as' ); ?></label>
			</th>
			<td>
				<select name="wpas_frequency_reload">
					<option value="manual" <?php selected( $wp_as_frequency_reload, 'manual' ); ?>>
						<?php esc_html_e( 'Manual', 'wp-as' ); ?>		
					</option>
					<option value="hourly" <?php selected( $wp_as_frequency_reload, 'hourly' ); ?>>
						<?php esc_html_e( 'Hourly', 'wp-as' ); ?>
					</option>
					<option value="daily" <?php selected( $wp_as_frequency_reload, 'daily' ); ?>>
						<?php esc_html_e( 'Daily', 'wp-as' ); ?>							
					</option>
					<option value="weekly" <?php selected( $wp_as_frequency_reload, 'weekly' ); ?>>
						<?php esc_html_e( 'Weekly', 'wp-as' ); ?>							
					</option>
				</select>
			</td>
			
		</tr>
		<tr class="wp_as__api_note">
			<td>
			</td>
			<td class="wp_as_api_note_td" colspan="3">
				<p class="description wp_as_apidescription">
					<?php esc_html_e( 'Set how frequently you want to update the API . This setting is helpful to reduce API calls.', 'wp-as' );?>
				</p>
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
 // var_dump($all_data['wp_as_frequency_reload']);