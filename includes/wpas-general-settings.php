<?php
/**
 * The WP Advance Stats general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */
$wp_info = get_option('wp_info');
$frequency = (!empty($wp_info['Frequency']) ? $wp_info['Frequency'] : ''); 
?>
<div class="wp_as_global_settings" id="wp_as_global_settings">
<form method="post" name="wpas_settings_form">
<table class="form-table" >
	<br>
	<tr class="wp-as-frequency">
			<th scope="row">
				<label for="UpdateFrequency"><?php esc_html_e( 'Frequency', 'wp-as' ); ?></label>
			</th>
			<td>
				<!-- <select name="wpas_frequency_reload"> -->
					<input type="input" name="frequency" id="wpas-frequency" size="5" maxlength="5" value="<?php echo $frequency ?>">
					<label><?php esc_html_e( 'Days', 'wp-as' ); ?></label>
				<!-- </select> -->
			</td>
			<!-- var_dump($_POST["frequency"]) -->
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
<?php 
	//var_dump($_POST["frequency"])
	    $update_option = array( 
			'Frequency' => (!empty($_POST['frequency']) ? $_POST['frequency'] : '' ),
		);
		update_option('wp_info', $update_option);
		//var_dump($update_option['Frequency']); 
?>
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