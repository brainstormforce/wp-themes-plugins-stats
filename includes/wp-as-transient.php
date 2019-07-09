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
	
	
	//var_dump(get_option('wp_info'));
		//var_dump($theme);

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
	//var_dump($frequency);
	//
	 //var_dump($wp_theme_slug);
	// die();
	// $data = file_get_contents('https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=astra');
	// $arr1 = json_decode($data);
	$namet = get_option('wp_info');
	$args = array(
		    'slug' => $namet['theme_slug'],
		    'fields' => array( 'active_installs' => true,'screenshot_url'=> true,'versions'=> true,'ratings'=> true,'download_link'=> true )
	);
		 
		// Make request and extract plug-in object
	$response = wp_remote_post(
		    'http://api.wordpress.org/themes/info/1.0/?action=theme_information&request[fields][ratings]=true',
		    array(
		        'body' => array(
		            'action' => 'theme_information',
		            'request' => serialize((object)$args)
		        )
		    )
	);
	$theme = unserialize(wp_remote_retrieve_body($response));

	
		


	$data = get_site_transient( 'tr_theme_info' );
	// echo '<pre>';
	//var_dump($data);
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
	//var_dump($theme);
	if ( false === $data ) {
		$data = tr_fetch_data();
		$data = (!empty($data) ? $data : '' );
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

	if ( empty( $theme ) ) {
	 	return '';
	 }
	//var_dump($theme);
	$text = sprintf( '<div class="bsfresp-table">
	   			<div class="bsfresp-table-caption">THEME INFORMATION</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
							Name
						</div>
						<div class="bsftable-body-cell">&nbsp;
								'. esc_attr( $theme->name) .'&nbsp;
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Company
						</div>
						<div class="bsftable-body-cell">
								&nbsp;'. strip_tags( $theme->author ) .' 
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Version
						</div>
						<div class="bsftable-body-cell">&nbsp;
								' .esc_attr( $theme->version ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Active installs
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr($theme->active_installs).'
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						5 Star Ratings
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $theme->ratings[5] ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Total Ratings
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $theme->num_ratings ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Total Downloads
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr( $theme->downloaded ). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Updated on
						</div>
						<div class="bsftable-body-cell">
								&nbsp;' .esc_attr($theme->last_updated). '
						</div>
					</div>
					<div class="bsfresp-table-header">
						<div class="bsftable-header-cell">
						Download
						</div>
						<div class="bsftable-body-cell">
								&nbsp;<a href="' .esc_url($theme->download_link).'" target="_blank">'.esc_attr($theme->name).'</a>
						</div>
					</div>
		 		</div>'
		// $data->homepage,
		// $data->name,
		// $data->{date('Y-m-d')},
		// $data->rating
	);

	//var_dump($text);
	
	return $text;
}

function tr_fetch_data() {	
	$namet = get_option('wp_info');
	//$namep = get_option('wp_info');
	$argst = array(
		    'slug' => $namet['theme_slug'],
		    'fields' => array( 'active_installs' => true,'screenshot_url'=> true,'versions'=> true,'ratings'=> true,'download_link'=> true )
		);
		 
		// Make request and extract plug-in object
		$responset = wp_remote_post(
		    'http://api.wordpress.org/themes/info/1.0/?action=theme_information&request[fields][ratings]=true',
		    array(
		        'body' => array(
		            'action' => 'theme_information',
		            'request' => serialize((object)$argst)
		        )
		    )
		);
		$theme = unserialize(wp_remote_retrieve_body($responset));
		//var_dump($theme);
	
	return $theme;
}
?>