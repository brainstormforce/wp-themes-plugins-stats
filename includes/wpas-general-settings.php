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
$wpas_info           = get_option( 'wp_info' );
$wpas_frequency      = ( ! empty( $wpas_info['Frequency'] ) ? sanitize_text_field( $wpas_info['Frequency'] ) : 1 );
$wpas_choice         = ( ! empty( $wpas_info['Choice'] ) ? sanitize_text_field( $wpas_info['Choice'] ) : 'd-m-y' );
$wpas_hrchoice       = ( ! empty( $wpas_info['Hrchoice'] ) ? sanitize_text_field( $wpas_info['Hrchoice'] ) : 0 );
$wpas_rchoice        = ( ! empty( $wpas_info['Rchoice'] ) ? sanitize_text_field( $wpas_info['Rchoice'] ) : 'K' );
$wpas_field1         = ( ! empty( $wpas_info['Field1'] ) ? sanitize_text_field( $wpas_info['Field1'] ) : 'K' );
$wpas_field2         = ( ! empty( $wpas_info['Field2'] ) ? sanitize_text_field( $wpas_info['Field2'] ) : 'M' );
$wpas_symbol         = ( ! empty( $wpas_info['Symbol'] ) ? sanitize_text_field( $wpas_info['Symbol'] ) : '' );
$wpas_hrchoice_disp  = ( ( 0 === $wpas_hrchoice ) ? 'style="display:none"' : '' );
$wpas_numchoice_disp = ( ( 0 === $wpas_hrchoice ) ? 'style="display:block"' : 'style="display:none"' );
?>
<div class="advanced-stats-global-settings" id="advanced-stats-global-settings">
<form method="post" name="advanced-stats-settings-form">
<table class="advanced-stats-form-table" >
	<br>
		<tr class="wpas-frequency">
			<th scope="row">
				<label for="UpdateFrequency"><?php esc_html_e( 'Frequency', 'advanced-stats' ); ?></label>
			</th>
			<td>
					<input class="small-text" type="number" name="frequency" id="wpas-frequency" pattern="[1-9]+" title="Number Only" min="1" size="5" maxlength="5" style="text-align: center;" value="<?php echo esc_attr( $wpas_frequency ); ?>">
					<label><?php esc_html_e( 'Days', 'advanced-stats' ); ?></label>
			</td>
		</tr>
		<tr class="advanced-stats-description">
			<td>
			</td>
			<td class="advanced-stats-description" colspan="3">
				<p class="description advanced-stats-description">
					<?php esc_html_e( 'Set how frequently you want to update the API calls.', 'advanced-stats' ); ?>
				</p>
			</td>
		</tr>
		<tr class="wpas-hrformat">
			<th scope="row">
				<label for="hrformat"><?php esc_html_e( 'Human Readable Format', 'advanced-stats' ); ?></label>
			</th>
			<td class="advanced-stats-hroption">
				<input type="checkbox" name="wpas_hr_option" id="wpas_hr_option" onchange="bsf_hrFunction(this)" value="1"<?php checked( '1' === $wpas_hrchoice ); ?> />
				<label><?php esc_html_e( 'Enable', 'advanced-stats' ); ?></label>
				<br>
				<div id="hr_option" class="advanced-stats-hr-option" <?php echo wp_kses_post( $wpas_hrchoice_disp ); ?> >
					<br>
					<label>
					<input type="radio" name="wpas_r_option" id="thousand"  value="K"  <?php checked( 'K' === $wpas_rchoice ); ?> />
					<?php esc_html_e( 'Thousand', 'advanced-stats' ); ?>
						<input type="text" class="small-text advanced-stats-small-text1"  name="field1"  placeholder="K" value="<?php echo esc_attr( $wpas_field1 ); ?>" />
					</label>
					<br>
					<label>
					<input type="radio" name="wpas_r_option" id="million"  value="M"  <?php checked( 'M' === $wpas_rchoice ); ?>/>
					<?php esc_html_e( 'Million', 'advanced-stats' ); ?>
						<input type="text"  class="small-text advanced-stats-small-text2"  name="field2" placeholder="M" value="<?php echo esc_attr( $wpas_field2 ); ?>"  />
					</label>
				</div>
				<p class="description advanced-stats-description">
					<?php esc_html_e( 'Set human readable to display active installs, active installs counts and downloads counts i.e 247,704,360 -> 247.7 million.', 'advanced-stats' ); ?>
				</p>
				<div id="num_option" class="advanced-stats-num-option" <?php echo wp_kses_post( $wpas_numchoice_disp ); ?> >
					<br>
						<h4><label for="nubergroupsymbol" class="advanced-stats-number-group-symbol"><?php esc_html_e( 'Number Grouping Symbol', 'advanced-stats' ); ?></label></h4>
								<input type="input" name="wpas_number_group" size="1" maxlength="1"  class="small-text" pattern="[,.]" title="Only Comma and Dot are allowed." style="text-align: center;" value="<?php echo esc_attr( $wpas_symbol ); ?>" />
								<br> 
						<span>
							<p class="description advanced-stats-description">
							<?php esc_html_e( 'Set Symbol to display number like this 123,598,370.', 'advanced-stats' ); ?>
							</p>
						</span>
					<br>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php esc_html_e( 'Date Format', 'advanced-stats' ); ?></label></th>
				<td class="advanced-stats-date">
					<fieldset class="advanced-stats-date">
					<?php
						$wpas_date_formats = array_unique( apply_filters( 'date_formats', array( __( 'F j,Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );
						$wpas_format       = 'd-m-y';
						$wpas_custom       = true;
					foreach ( $wpas_date_formats as $wpas_format ) {
						echo "\t<label><input class='advanced-stats-option' type='radio' name='wpasoption' value='" . esc_attr( $wpas_format ) . "'";
						if ( $wpas_choice === $wpas_format ) {
							echo " checked='checked'";
							$wpas_custom = false;
						}
						echo ' /> <span class="advanced-stats-date-time-text format-i18n">' . date_i18n( $wpas_format ) . '</span><code>' . esc_attr( $wpas_format ) . '</code><br></label>';//PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
					}
						echo '<label><input type="radio" name="wpasoption" id="date_format_custom_radio" value="ok"';
						checked( 'ok' == $wpas_custom );//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
						echo '/> <span class="advanced-stats-date-time-text date-time-custom-text">' . esc_attr( 'Custom' ) . '<span class="screen-reader-text"> ' . esc_attr( 'enter a custom date format in the following field' ) . '</span></span></label>' .
							'<input type="text" name="wpas_date_format_custom" id="wpas_date_format_custom" value="' . esc_attr( $wpas_choice ) . '" class="small-text" />';
						echo '<p class="advanced-stats-shortdescription">' . esc_attr_e( '  (Please Note that before checking the custom, please change the format in the custom field).', 'advanced-stats' ) . '</p>';
					?>
					</fieldset>
				</td>
		</tr>
		<tr class="advanced-stats-description">
			<td>
			</td>
			<td class="advanced-stats-description" colspan="3">
				<p class="description advanced-stats-description">
					<?php esc_html_e( 'Choose date format either from the option provided or select a custom option.', 'advanced-stats' ); ?>
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
