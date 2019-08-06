<?php
/**
 * The WP Advance Stats general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

// General setting Page.
wp_enqueue_style( 'bsf_wpas_stylesheet' );
wp_enqueue_script( 'bsf_wpas_jsfile' );
$wp_info        = get_option( 'wp_info' );
$frequency      = ( ! empty( $wp_info['Frequency'] ) ? sanitize_text_field( $wp_info['Frequency'] ) : 1 );
$choice         = ( ! empty( $wp_info['Choice'] ) ? sanitize_text_field( $wp_info['Choice'] ) : 'd-m-y' );
$hrchoice       = ( ! empty( $wp_info['Hrchoice'] ) ? sanitize_text_field( $wp_info['Hrchoice'] ) : 0 );
$rchoice        = ( ! empty( $wp_info['Rchoice'] ) ? sanitize_text_field( $wp_info['Rchoice'] ) : 'K' );
$wpas_field1    = ( ! empty( $wp_info['Field1'] ) ? sanitize_text_field( $wp_info['Field1'] ) : 'K' );
$wpas_field2    = ( ! empty( $wp_info['Field2'] ) ? sanitize_text_field( $wp_info['Field2'] ) : 'M' );
$symbol         = ( ! empty( $wp_info['Symbol'] ) ? sanitize_text_field( $wp_info['Symbol'] ) : '' );
$hrchoice_disp  = ( ( 0 === $hrchoice ) ? 'style="display:none"' : '' );
$numchoice_disp = ( ( 0 === $hrchoice ) ? 'style="display:block"' : 'style="display:none"' );
?>
<div class="wp-as-global-settings" id="wp-as-global-settings">
<form method="post" name="wp-as-settings-form">
<table class="wp-as-form-table" >
	<br>
		<tr class="wpas-frequency">
			<th scope="row">
				<label for="UpdateFrequency"><?php esc_html_e( 'Frequency', 'wp-as' ); ?></label>
			</th>
			<td>
					<input class="small-text" type="number" name="frequency" id="wpas-frequency" pattern="[1-9]+" title="Number Only" min="1" size="5" maxlength="5" style="text-align: center;" value="<?php echo esc_attr( $frequency ); ?>">
					<label><?php esc_html_e( 'Days', 'wp-as' ); ?></label>
			</td>
		</tr>
		<tr class="wp-as-description">
			<td>
			</td>
			<td class="wp-as-description" colspan="3">
				<p class="description wp-as-description">
					<?php esc_html_e( 'Set how frequently you want to update the API calls.', 'wp-as' ); ?>
				</p>
			</td>
		</tr>
		<tr class="wpas-hrformat">
			<th scope="row">
				<label for="hrformat"><?php esc_html_e( 'Human Readable Format', 'wp-as' ); ?></label>
			</th>
			<td class="wp-as-hroption">
				<input type="checkbox" name="wpas_hr_option" id="wpas_hr_option" onchange="bsf_hrFunction(this)" value="1"<?php checked( '1' === $hrchoice ); ?> />
				<label><?php esc_html_e( 'Enable', 'wp-as' ); ?></label>
				<br>
				<div id="hr_option" class="wp-as-hr-option" <?php echo wp_kses_post( $hrchoice_disp ); ?> >
					<br>
					<label>
					<input type="radio" name="wpas_r_option" id="thousand"  value="K"  <?php checked( 'K' === $rchoice ); ?> />
					<?php esc_html_e( 'Thousand', 'wp-as' ); ?>
						<input type="text" class="small-text wp-as-small-text1"  name="field1"  placeholder="K" value="<?php echo esc_attr( $wpas_field1 ); ?>" />
					</label>
					<br>
					<label>
					<input type="radio" name="wpas_r_option" id="million"  value="M"  <?php checked( 'M' === $rchoice ); ?>/>
					<?php esc_html_e( 'Million', 'wp-as' ); ?>
						<input type="text"  class="small-text wp-as-small-text2"  name="field2" placeholder="M" value="<?php echo esc_attr( $wpas_field2 ); ?>"  />
					</label>
				</div>
				<p class="description wp-as-description">
					<?php esc_html_e( 'Set human readable to display active installs, active installs counts and downloads counts i.e 247,704,360 -> 247.7 million.', 'wp-as' ); ?>
				</p>
				<div id="num_option" class="wp-as-num-option" <?php echo wp_kses_post( $numchoice_disp ); ?> >
					<br>
						<h4><label for="nubergroupsymbol" class="wp-as-number-group-symbol"><?php esc_html_e( 'Number Grouping Symbol', 'wp-as' ); ?></label></h4>
								<input type="input" name="wpas_number_group" size="1" maxlength="1"  class="small-text" pattern="[,.]" title="Only Comma and Dot are allowed." style="text-align: center;" value="<?php echo esc_attr( $symbol ); ?>" />
								<br> 
						<span>
							<p class="description wp-as-description">
							<?php esc_html_e( 'Set Symbol to display number like this 123,598,370.', 'wp-as' ); ?>
							</p>
						</span>
					<br>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php esc_html_e( 'Date Format', 'wp-as' ); ?></label></th>
				<td class="wp-as-date">
					<fieldset class="wp-as-date">
					<?php
						$date_formats = array_unique( apply_filters( 'date_formats', array( __( 'F j,Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );
						$format       = 'd-m-y';
						$custom       = true;
					foreach ( $date_formats as $format ) {
						echo "\t<label><input class='wp-as-option' type='radio' name='wpasoption' value='" . esc_attr( $format ) . "'";
						if ( $choice === $format ) {
							echo " checked='checked'";
							$custom = false;
						}
						echo ' /> <span class="wp-as-date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_attr( $format ) . '</code><br></label>';//PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
					}
						echo '<label><input type="radio" name="wpasoption" id="date_format_custom_radio" value="ok"';
						checked( 'ok' == $custom );//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
						echo '/> <span class="wp-as-date-time-text date-time-custom-text">' . esc_attr( 'Custom' ) . '<span class="screen-reader-text"> ' . esc_attr( 'enter a custom date format in the following field' ) . '</span></span></label>' .
							'<input type="text" name="wpas_date_format_custom" id="wpas_date_format_custom" value="' . esc_attr( $choice ) . '" class="small-text" />';
						echo '<p class="wp-as-shortdescription">' . esc_attr_e( '  (Please Note that before checking the custom, please change the format in the custom field).', 'wp-as' ) . '</p>';
					?>
					</fieldset>
				</td>
		</tr>
		<tr class="wp-as-description">
			<td>
			</td>
			<td class="wp-as-description" colspan="3">
				<p class="description wp-as-description">
					<?php esc_html_e( 'Choose date format either from the option provided or select a custom option.', 'wp-as' ); ?>
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
