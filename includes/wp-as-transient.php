<?php
add_action( 'wp_head', 'tr_head' );
function tr_head() {
	$GLOBALS['tr_time_start'] = microtime( true );
}

add_action( 'wp_footer', 'tr_footer' );
function tr_footer() {
	$start = $GLOBALS['tr_time_start'];
	$end   = microtime( true );

	echo sprintf( '<p>Page generated in %0.2f seconds.</p>',
		$end - $start
	);
}

add_filter( 'the_content', 'tr_the_content' );
function tr_the_content( $content ) {
	return $content . tr_get_text();
}
// fuction delete_exp_transient(){
// 	global $wpdb;
// 	var_dump($wpdb);
// 	$time_now = time();
// 		$expired  = $wpdb->get_col( "SELECT option_name FROM $wpdb->options where option_name LIKE '%_transient_timeout_%' AND option_value+0 < $time_now" );
// }

function tr_get_text() {
	$frequency = get_option('wpas_general_settings');
	 var_dump($frequency);
	// die();
	$data = file_get_contents('http://api.wordpress.org/stats/themes/1.0/downloads.php?slug={astra}&limit=1');
	$arr1 = json_decode($data);
	$data = get_site_transient( 'tr_theme_info' );
	// echo '<pre>';
	// var_dump($arr1->$frequency['wpas_frequency_reload']);
	// echo '</pre>';

	if( 'manual' === $frequency['wpas_frequency_reload'] ){
		//
		$text = sprintf( '<p>Did you know that <a href="%1$s">%2$s</a> has been downloaded %3$d times, and has an average rating of %4$d&percnt; ?</p>',
		$arr1->homepage,
		$arr1->name,
		$arr1->{date('Y-m-d')},
		$arr1->rating
	);
		return $text;
	}
	$data = get_site_transient( 'tr_theme_info' );
	//if($data == delete_exp_transient)
	//var_dump($data);
	if ( false === $data ) {
		$data = tr_fetch_data();

		if('hourly' == $frequency['wpas_frequency_reload'])
		{
			//delete_transient('_site_transient_timeout_tr_theme_info');
			set_site_transient( 'tr_theme_info', tr_fetch_data(), 60*60 );
		} elseif ('daily' == $frequency['wpas_frequency_reload'] )
		{
			set_site_transient( 'tr_theme_info', tr_fetch_data(), 24*3600 );
		}
		elseif ('weekly' == $frequency['wpas_frequency_reload']) {
			set_site_transient( 'tr_theme_info', tr_fetch_data(), 7 * 86400 );
		}
	}

	if ( empty( $data ) ) {
		return '';
	}

	$text = sprintf( '<p>Did you know that <a href="%1$s">Astra</a> has been downloaded %3$d times, and has an average rating of %4$d&percnt; ?</p>',
		$data->homepage,
		$data->name,
		$data->{date('Y-m-d')},
		$data->rating
	);
	//var_dump($text);
	return $text;
}

function tr_fetch_data() {
	$url = 'http://api.wordpress.org/stats/themes/1.0/downloads.php?slug={astra}&limit=1';
	$response = wp_remote_request( $url, array(
    'ssl_verify' => true
) );

	if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
		$body = wp_remote_retrieve_body( $response );
		$json = json_decode( $body );
		if ( ! is_null( $json ) ) {
			return $json;
		}
	}
	//var_dump($response);
	return false;
}
?>