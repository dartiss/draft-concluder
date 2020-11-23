<?php
/**
 * Debug
 * 
 * Functions to help with debugging any issues
 *
 * @package draft-concluder
 */

/**
 * Now shortcode
 *
 * Will generate and output the email content.
 *
 * @param string $paras   Parameters.
 * @param string $content Content between shortcodes.
 */
function draft_concluder_now_shortcode( $paras, $content ) {

	return draft_concluder_process_posts( true );

}

add_shortcode( 'dc_now', 'draft_concluder_now_shortcode' );


/**
 * Last run shortcode
 *
 * Outputs the results of the last run.
 *
 * @param string $paras   Parameters.
 * @param string $content Content between shortcodes.
 */
function draft_concluder_last_run_shortcode( $paras, $content ) {

	$output = get_option( 'draft_concluder_output' );
	$debug  = '';

	if ( ! $output ) {
		$debug .= esc_html( __( 'Draft Concluder has not yet run.', 'draft_concluder' ) );
	} else {
		$timestamp = date( 'l jS \of F Y h:i:s A', $output['timestamp'] );
		if ( 0 == $output['errors'] ) {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, successfully.', 'draft_concluder' ), esc_html( $timestamp ) );
		} else {
			/* translators: %1$s: timestamp %2$s: number of errors */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, with %2$s errors.', 'draft_concluder' ), esc_html( $timestamp ), esc_html( $output['errors'] ) );
		}
		$debug .= esc_html( $text ) . '<br/>';
		$debug .= wp_kses(
			$output['emails'],
			array(
				'br' => array(),
				'p'  => array(),
			)
		);
	}

	return $debug;
}

add_shortcode( 'dc_last_run', 'draft_concluder_last_run_shortcode' );
