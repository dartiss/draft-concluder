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

	// Add the settings field for which post types to look through for drafts.
	add_settings_field( 'draft_concluder_what', __( 'Post types to check', 'draft-concluder' ), 'draft_concluder_what_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_what' ) );
	register_setting( 'general', 'draft_concluder_what' );

	// Add the settings field for how old drafts must be to qualify.
	add_settings_field( 'draft_concluder_age', __( 'Draft age to qualify', 'draft-concluder' ), 'draft_concluder_age_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_age' ) );
	register_setting( 'general', 'draft_concluder_age' );

	// Add the settings field for whether draft ages should be based on creation or update.
	add_settings_field( 'draft_concluder_since', '', 'draft_concluder_since_callback', 'general', 'draft_concluder_section', array( 'label_for' => 'draft_concluder_since' ) );
	register_setting( 'general', 'draft_concluder_since' );
}

add_action( 'admin_init', 'draft_concluder_settings_init' );

/**
 * Section callback
 *
 * Create the new section that we've added to the Discussion settings screen
 */
function draft_concluder_section_callback() {

	$output = get_option( 'draft_concluder_output' );

	echo esc_html( __( 'These settings allow you to control when the emails are generated and what they should report on.', 'draft-concluder' ) );

	echo '<br/><br/>';
	echo wp_kses( '<strong>' . __( 'Status: ', 'draft_concluder' ) . '</strong>', array( 'strong' => array() ) );
	if ( ! $output ) {
		echo esc_html( __( 'Draft Concluder has not yet run.', 'draft_concluder' ) );
	} else {
		$timestamp = date( 'l jS \of F Y @ h:i:s A', $output['timestamp'] );
		if ( 0 == $output['errors'] ) {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, successfully.', 'draft_concluder' ), $timestamp );
		} else {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, with errors.', 'draft_concluder' ), $timestamp );
		}
	}

	echo esc_html( $text ) . '<br/>';
}

/**
 * When? callback
 *
 * Add the settings field for what date to generate the emails.
 */
function draft_concluder_when_callback() {

	$option = get_option( 'draft_concluder_when' );
	if ( ! $option ) {
		$option = 'Monday';
	}

	echo '<select name="draft_concluder_when">';
	echo '<option ' . selected( 'Daily', $option, false ) . ' value="Daily">Daily</option>';
	echo '<option ' . selected( 'Monday', $option, false ) . ' value="Monday">Monday</option>';
	echo '<option ' . selected( 'Tuesday', $option, false ) . ' value="Tuesday">Tuesday</option>';
	echo '<option ' . selected( 'Wednesday', $option, false ) . ' value="Wednesday">Wednesday</option>';
	echo '<option ' . selected( 'Thursday', $option, false ) . ' value="Thursday">Thursday</option>';
	echo '<option ' . selected( 'Friday', $option, false ) . ' value="Friday">Friday</option>';
	echo '<option ' . selected( 'Saturday', $option, false ) . ' value="Saturday">Saturday</option>';
	echo '<option ' . selected( 'Sunday', $option, false ) . ' value="Sunday">Sunday</option>';
	echo '</select>';
}

/**
 * Time callback
 *
 * Add the settings field for what time to generate the emails.
 */
function draft_concluder_time_callback() {

	$option = get_option( 'draft_concluder_time' );
	if ( ! $option ) {
		$option = '1am';
	}

	echo '<select name="draft_concluder_time">';
	echo '<option ' . selected( '12am', $option, false ) . ' value="12am">Midnight</option>';
	echo '<option ' . selected( '1am', $option, false ) . ' value="1am">1am</option>';
	echo '<option ' . selected( '2am', $option, false ) . ' value="2am">2am</option>';
	echo '<option ' . selected( '3am', $option, false ) . ' value="3am">3am</option>';
	echo '<option ' . selected( '4am', $option, false ) . ' value="4am">4am</option>';
	echo '<option ' . selected( '5am', $option, false ) . ' value="5am">5am</option>';
	echo '<option ' . selected( '6am', $option, false ) . ' value="6am">6am</option>';
	echo '<option ' . selected( '7am', $option, false ) . ' value="7am">7am</option>';
	echo '<option ' . selected( '8am', $option, false ) . ' value="8am">8am</option>';
	echo '<option ' . selected( '9am', $option, false ) . ' value="9am">9am</option>';
	echo '<option ' . selected( '10am', $option, false ) . ' value="10am">10am</option>';
	echo '<option ' . selected( '11am', $option, false ) . ' value="11am">11am</option>';
	echo '<option ' . selected( '12pm', $option, false ) . ' value="12pm">Midday</option>';
	echo '<option ' . selected( '1pm', $option, false ) . ' value="1pm">1pm</option>';
	echo '<option ' . selected( '2pm', $option, false ) . ' value="2pm">2pm</option>';
	echo '<option ' . selected( '3pm', $option, false ) . ' value="3pm">3pm</option>';
	echo '<option ' . selected( '4pm', $option, false ) . ' value="4pm">4pm</option>';
	echo '<option ' . selected( '5pm', $option, false ) . ' value="5pm">5pm</option>';
	echo '<option ' . selected( '6pm', $option, false ) . ' value="6pm">6pm</option>';
	echo '<option ' . selected( '7pm', $option, false ) . ' value="7pm">7pm</option>';
	echo '<option ' . selected( '8pm', $option, false ) . ' value="8pm">8pm</option>';
	echo '<option ' . selected( '9pm', $option, false ) . ' value="9pm">9pm</option>';
	echo '<option ' . selected( '10pm', $option, false ) . ' value="10pm">10pm</option>';
	echo '<option ' . selected( '11pm', $option, false ) . ' value="11pm">11pm</option>';
	echo '</select>';
}

/**
 * What? callback
 *
 * Add the settings field for which post types to look through for drafts
 */
function draft_concluder_what_callback() {

	$option = get_option( 'draft_concluder_what' );
	if ( ! $option ) {
		$option = 'postpage';
	}

	echo '<select name="draft_concluder_what">';
	echo '<option ' . selected( 'post', $option, false ) . ' value="post">Posts</option>';
	echo '<option ' . selected( 'page', $option, false ) . ' value="page">Pages</option>';
	echo '<option ' . selected( 'postpage', $option, false ) . ' value="post">Posts & Pages</option>';
	echo '</select>';
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

	echo '<input name="draft_concluder_age" size="3" maxlength="3" type="text" value="' . esc_html( $option ) . '" />&nbsp;days';
}

/**
 * Since? callback
 *
 * Add the settings field for whether draft ages should be based on creation or update.
 */
function draft_concluder_since_callback() {

	$option = get_option( 'draft_concluder_since' );
	if ( ! $option ) {
		$option = 'created';
	}

	echo '<select name="draft_concluder_since">';
	echo '<option ' . selected( 'created', $option, false ) . ' value="created">Since they were created</option>';
	echo '<option ' . selected( 'modified', $option, false ) . ' value="modified">Since they were last updated</option>';
	echo '</select>';
}
