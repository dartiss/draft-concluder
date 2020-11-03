<?php
/**
 * Settings
 * 
 * Functions to add settings to WP Admin
 *
 * @package draft-concluder
 */

/**
 * Add to settings
 *
 * Add fields to the general settings to capture the various settings required for the plugin.
 */
function draft_concluder_settings_init() {

	// Add the new section to General settings.
	add_settings_section( 'draft_concluder_section', __( 'Draft Concluder', 'draft_concluder' ), 'draft_concluder_section_callback', 'general' );

	// Add the settings field for what day to generate the emails.
	add_settings_field( 'draft_concluder_when', __( 'Day to generate', 'draft-concluder' ), 'draft_concluder_when_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_when' ) );
	register_setting( 'general', 'draft_concluder_when' );

	// Add the settings field for what time to generate the emails.
	add_settings_field( 'draft_concluder_time', __( 'Time to generate', 'draft-concluder' ), 'draft_concluder_time_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_time' ) );
	register_setting( 'general', 'draft_concluder_time' );

	// Add the settings field for which taxonomies to look through for drafts.
	add_settings_field( 'draft_concluder_what', __( 'Taxonomies to check', 'draft-concluder' ), 'draft_concluder_what_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_what' ) );
	register_setting( 'general', 'draft_concluder_what' );

	// Add the settings field for how old drafts must be to qualify.
	add_settings_field( 'draft_concluder_age', __( 'Draft age to qualify', 'draft-concluder' ), 'draft_concluder_age_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_age' ) );
	register_setting( 'general', 'draft_concluder_age' );
}

add_action( 'admin_init', 'draft_concluder_settings_init' );

/**
 * Section callback
 *
 * Create the new section that we've added to the Discussion settings screen
 */
function draft_concluder_section_callback() {

	$output = get_option( 'draft_concluder_output' );

	echo esc_attr( __( 'These settings allow you to control when the emails are generated and what they should report on.', 'draft-concluder' ) );

	echo '<br/><br/>';
	echo wp_kses( '<strong>' . __( 'Status: ', 'draft_concluder' ) . '</strong>', array( 'strong' => array() ) );
	if ( ! $output ) {
		echo esc_attr( __( 'Draft Concluder has not yet run.', 'draft_concluder' ) );
	} else {
		$timestamp = date( 'l jS \of F Y at h:i:s A', $output['timestamp'] );
		if ( 0 == $output['errors'] ) {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, successfully.', 'draft_concluder' ), $timestamp );
		} else {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, with errors.', 'draft_concluder' ), $timestamp );
		}
	}
}

/**
 * When? callback
 *
 * Add the settings field for what date to generate the emails.
 */
function draft_concluder_when_callback() {

	$option = get_option( 'draft_concluder_when' );

	echo '<select name="draft_concluder_when"><option ';
	if ( 'Daily' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Daily">Daily</option><option ';
	if ( 'Monday' == $option || ! $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Monday">Monday</option><option ';
	if ( 'Tuesday' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Tuesday">Tuesday</option><option ';
	if ( 'Wednesday' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Wednesday">Wednesday</option><option ';
	if ( 'Thursday' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Thursday">Thursday</option><option ';
	if ( 'Friday' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Friday">Friday</option><option ';
	if ( 'Saturday' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Saturday">Saturday</option><option ';
	if ( 'Sunday' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="Sunday">Sunday</option></select>';
}

/**
 * Time callback
 *
 * Add the settings field for what time to generate the emails.
 */
function draft_concluder_time_callback() {

	$option = get_option( 'draft_concluder_time' );

	// If the time has changed, update the event schedule.
	draft_concluder_check_scheduled_time( $option );

	echo '<select name="draft_concluder_time"><option ';
	if ( '1am' == $option || ! $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="1am">1am</option><option ';
	if ( '4am' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="4am">4am</option><option ';
	if ( '7am' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="7am">7am</option><option ';
	if ( '11am' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="11am">11am</option><option ';
	if ( '1pm' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="1pm">1pm</option><option ';
	if ( '4pm' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="4pm">4pm</option><option ';
	if ( '7pm' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="7pm">7pm</option><option ';
	if ( '10pm' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="10pm">10pm</option></select>';
}

/**
 * What? callback
 *
 * Add the settings field for which taxonomies to look through for drafts
 */
function draft_concluder_what_callback() {

	$option = get_option( 'draft_concluder_what' );

	echo '<select name="draft_concluder_what"><option ';
	if ( 'post' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="post">Posts</option><option ';
	if ( 'page' == $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="page">Pages</option><option ';
	if ( 'postpage' == $option || ! $option ) {
		echo 'selected="selected" ';
	}
	echo 'value="postpage">Posts & Pages</option></select>';
}

/**
 * Age? callback
 *
 * Add the settings field for how old drafts must be to qualify.
 */
function draft_concluder_age_callback() {

	$option = get_option( 'draft_concluder_age' );
	if ( ! $option ) {
		$option = 0;
	}

	echo '<input name="draft_concluder_age" size="3" maxlength="3" type="text" value="' . esc_attr( $option ) . '" />&nbsp;days';
}
