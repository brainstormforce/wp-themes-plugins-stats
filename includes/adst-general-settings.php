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
$adst_info           = get_option( 'adst_info' );
$adst_frequency      = ( ! empty( $adst_info['Frequency'] ) ? sanitize_text_field( $adst_info['Frequency'] ) : 1 );
$adst_choice         = ( ! empty( $adst_info['Choice'] ) ? sanitize_text_field( $adst_info['Choice'] ) : 'd-m-y' );
$adst_rchoice        = ( ! empty( $adst_info['Rchoice'] ) ? sanitize_text_field( $adst_info['Rchoice'] ) : 'normal' );
$adst_field1         = ( ! empty( $adst_info['Field1'] ) ? sanitize_text_field( $adst_info['Field1'] ) : 'K' );
$adst_field2         = ( ! empty( $adst_info['Field2'] ) ? sanitize_text_field( $adst_info['Field2'] ) : 'M' );
$adst_symbol         = ( ! empty( $adst_info['Symbol'] ) ? sanitize_text_field( $adst_info['Symbol'] ) : '' );
$adst_numchoice_disp = ( ( 'normal' === $adst_rchoice ) ? 'style="display:table-row"' : 'style="display:none"' );
?>
<div class="adst-global-settings" id="adst-global-settings">
<form method="post" name="adst-settings-form">
<table class="adst-form-table" >
	<br>
		<tr class="wpas-frequency">
			<th scope="row">
				<label for="UpdateFrequency"><?php esc_html_e( 'Update Stats After', 'advanced-stats' ); ?></label>
			</th>
			<td class="adst-frequency-input">
					<input class="small-text" type="number" name="frequency" id="wpas-frequency" pattern="[1-9]+" title="Number Only" min="1" size="5" maxlength="5" style="text-align: center;" value="<?php echo esc_attr( $adst_frequency ); ?>">
					<label><?php esc_html_e( 'Day(s)', 'advanced-stats' ); ?></label>
			</td>
		</tr>
		<tr class="adst-description">
			<th scope="row"></th>
			<td class="adst-description" colspan="3">
				<p class="description adst-description">
					<?php esc_html_e( 'This will check and update stats after selected day(s).', 'advanced-stats' ); ?>
				</p>
			</td>
		</tr>
		<tr class="wpas-hrformat">
			<th scope="row">
				<label for="hrformat"><?php esc_html_e( 'Installation & Download Count Format', 'advanced-stats' ); ?></label>
			</th>
			<td class="adst-hroption">
				<div id="hr_option" class="adst-hr-option">
					<label>
						<input type="radio" name="adst_r_option" id="normal"  value="normal" <?php checked( 'normal' === $adst_rchoice ); ?> />
					<?php esc_html_e( 'Default (1000000)', 'advanced-stats' ); ?>
					</label>
					<br>
					<label>
					<input type="radio" name="adst_r_option" id="thousand"  value="K"  <?php checked( 'K' === $adst_rchoice ); ?> />
					<?php esc_html_e( 'In Thousands (1000K)', 'advanced-stats' ); ?>
						<input type="text" class="small-text adst-small-text1"  name="field1"  placeholder="K" value="<?php echo esc_attr( $adst_field1 ); ?>" />
					</label>
					<br>
					<label>
					<input type="radio" name="adst_r_option" id="million"  value="M"  <?php checked( 'M' === $adst_rchoice ); ?>/>
					<?php esc_html_e( 'In Millions (1M)', 'advanced-stats' ); ?>
						<input type="text"  class="small-text adst-small-text2"  name="field2" placeholder="M" value="<?php echo esc_attr( $adst_field2 ); ?>"  />
					</label>
					<p class="description adst-description">
					<?php esc_html_e( 'Generally, installation & download count is a big number. You can display it in a more readable format.', 'advanced-stats' ); ?><br><?php esc_html_e( 'For Example, A number 1,000,000 can be displayed as 1M or 1000K.', 'advanced-stats' ); ?>
					</p>
				</div>
			</td>
		</tr>

		<tr id="title_num_option" <?php echo wp_kses_post( $adst_numchoice_disp ); ?>>
			<th class="adst-num-option"><?php esc_html_e( 'Number Grouping Format', 'advanced-stats' ); ?></th>
			<td class="adst-number-group-symbol">
				<select name="wpas_number_group">
							<?php
							if ( '' === $adst_symbol ) {
								echo '<option selected value="">Default (123456789)</option>';
							} else {
								echo '<option  value="">Default (123456789)</option>';
							}
							if ( ',' === $adst_symbol ) {
								echo '<option selected value=",">By Comma (123,456,789)</option>';
							} else {
								echo '<option  value=",">By Comma (123,456,789)</option>';
							}
							if ( '.' === $adst_symbol ) {
								echo '<option selected value=".">By Dot (123.456.789)</option>';
							} else {
								echo '<option  value=".">By Dot (123.456.789)</option>';
							}
							?>
				</select></td>
		</tr>
		<tr>
			<th scope="row"><label><?php esc_html_e( 'Date Format', 'advanced-stats' ); ?></label></th>
				<td class="adst-date">
					<fieldset class="adst-date">
					<?php
						$adst_date_formats = array_unique( apply_filters( 'adst_date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );//PHPCS:ignore:WordPress.WP.I18n.MissingArgDomain
						$adst_format       = 'd-m-y';
						$adst_custom       = true;
					foreach ( $adst_date_formats as $adst_format ) {
						echo "\t<label><input class='adst-option' type='radio' name='wpasoption' value='" . esc_attr( $adst_format ) . "'";
						if ( $adst_choice === $adst_format ) {
							echo " checked='checked'";
							$adst_custom = false;
						}
						echo ' /> <span class="adst-date-time-text format-i18n">' . date_i18n( $adst_format ) . '</span><code>' . esc_attr( $adst_format ) . '</code><br></label>';//PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
					}
						echo '<label><input type="radio" name="wpasoption" id="date_format_custom_radio" value="ok"';
						checked( 'ok' == $adst_custom );//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison
						echo '/> <span class="adst-date-time-text date-time-custom-text">' . esc_attr( 'Custom' ) . '<span class="screen-reader-text"> ' . esc_attr( 'enter a custom date format in the following field' ) . '</span></span></label>' .
							'<input type="text" name="wpas_date_format_custom" id="wpas_date_format_custom" value="' . esc_attr( $adst_choice ) . '" class="small-text" />';
						echo '<p class="description adst-description"><a href="https://wordpress.org/support/article/formatting-date-and-time/">' . wp_kses_post( ' Refer article on date formatting', 'advanced-stats' ) . '</a></p>';
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
