<?php
/**
 * The WP Advance Stats general Settings tab
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */
$option = get_option('wp_as_general_settings');
$wp_as_font_size = ( ! empty( $options['wp_as_font_size'] ) ? $options['wp_as_font_size'] : 10 );
$wp_as_background_color = ( ! empty( $options['wp_as_background_color'] ) ? $options['wp_as_background_color'] : '' );
?>
<div class="wp_as_global_settings" id="wp_as_global_settings">
<form method="post" name="wp_as_settings_form">
<table class="form-table" >
	<br>
<tr>
<th scope="row">
<label for="wp_as_FontSize"><?php esc_attr_e( 'Font Size', 'wp-as' ); ?>  :</label>
</th>
<td>
<?php
echo '<input type="number" name="wp_as_font_size" max="50" min="10" class="small-text" value="' . esc_attr( $wp_as_font_size ) . '"  >&nbsp px';
?>
<p class="description">
<?php esc_attr_e( 'Keep blank for default value.', 'wp-as' ); ?>                  
</p>  
</td>
</tr>
<tr>
<th scope="row"> 
<label for="wp-as-BackgroundColor"> <?php esc_attr_e( 'Background Color', 'wp-as' ); ?> :</label>
</th>
<td>
<?php
echo '<div id="wp_as_bg">';
if ( isset( $wp_as_background_color ) ) {

	echo '<input  name="wp_as_background_color" class="my-color-field" value="' . esc_attr( $wp_as_background_color ) . '">';
} else {
	?>
<input  name="wp_as_background_color" class="my-color-field" value="#eeeeee">
	<?php
}
echo '</div>';
?>
<table class="form-table">
<tr>
<th>
<?php wp_nonce_field( 'wp-adv-stats', 'wp-as' ); ?>
<input type="submit" value="Save" class="bt button button-primary" name="submit">
</th>
</tr>
</table>
</table>
</form>
<?php