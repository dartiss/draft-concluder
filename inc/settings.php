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

	// Show the current status of the event run.
	echo '<br/><br/>';
	echo wp_kses( '<strong>' . __( 'Status: ', 'draft_concluder' ) . '</strong>', array( 'strong' => array() ) );
	if ( ! $output ) {
		echo esc_html( __( 'Draft Concluder has not yet run.', 'draft_concluder' ) );
	} else {
		$timestamp = date( 'l jS \o\f F Y \a\t g:i A', $output['timestamp'] );
		if ( 0 == $output['errors'] ) {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, successfully.', 'draft_concluder' ), $timestamp );
		} else {
			/* translators: %1$s: timestamp */
			$text = sprintf( __( 'Draft Concluder last ran at %1$s, with errors.', 'draft_concluder' ), $timestamp );
		}
	}

	// If an event has been scheduled, output the next run time.
	if ( false !== wp_next_scheduled( 'draft_concluder_mailer' ) ) {
		$next_run = date( 'l jS \o\f F Y \a\t g:i A', wp_next_scheduled( 'draft_concluder_mailer' ) );
		/* translators: %1$s: timestamp */
		$text .= '&nbsp;' . sprintf( __( 'It is next due to run on %1$s.', 'draft_concluder' ), $next_run );
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
	echo '<option ' . selected( 'Daily', $option, false ) . ' value="Daily">' . esc_html__( 'Daily', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Monday', $option, false ) . ' value="Monday">' . esc_html__( 'Monday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Tuesday', $option, false ) . ' value="Tuesday">' . esc_html__( 'Tuesday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Wednesday', $option, false ) . ' value="Wednesday">' . esc_html__( 'Wednesday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Thursday', $option, false ) . ' value="Thursday">' . esc_html__( 'Thursday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Friday', $option, false ) . ' value="Friday">' . esc_html__( 'Friday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Saturday', $option, false ) . ' value="Saturday">' . esc_html__( 'Saturday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'Sunday', $option, false ) . ' value="Sunday">' . esc_html__( 'Sunday', 'draft_concluder' ) . '</option>';
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
	echo '<option ' . selected( '12am', $option, false ) . ' value="12am">' . esc_html__( 'Midnight', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '1am', $option, false ) . ' value="1am">' . esc_html__( '1am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '2am', $option, false ) . ' value="2am">' . esc_html__( '2am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '3am', $option, false ) . ' value="3am">' . esc_html__( '3am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '4am', $option, false ) . ' value="4am">' . esc_html__( '4am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '5am', $option, false ) . ' value="5am">' . esc_html__( '5am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '6am', $option, false ) . ' value="6am">' . esc_html__( '6am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '7am', $option, false ) . ' value="7am">' . esc_html__( '7am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '8am', $option, false ) . ' value="8am">' . esc_html__( '8am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '9am', $option, false ) . ' value="9am">' . esc_html__( '9am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '10am', $option, false ) . ' value="10am">' . esc_html__( '10am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '11am', $option, false ) . ' value="11am">' . esc_html__( '11am', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '12pm', $option, false ) . ' value="12pm">' . esc_html__( 'Midday', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '1pm', $option, false ) . ' value="1pm">' . esc_html__( '1pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '2pm', $option, false ) . ' value="2pm">' . esc_html__( '2pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '3pm', $option, false ) . ' value="3pm">' . esc_html__( '3pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '4pm', $option, false ) . ' value="4pm">' . esc_html__( '4pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '5pm', $option, false ) . ' value="5pm">' . esc_html__( '5pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '6pm', $option, false ) . ' value="6pm">' . esc_html__( '6pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '7pm', $option, false ) . ' value="7pm">' . esc_html__( '7pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '8pm', $option, false ) . ' value="8pm">' . esc_html__( '8pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '9pm', $option, false ) . ' value="9pm">' . esc_html__( '9pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '10pm', $option, false ) . ' value="10pm">' . esc_html__( '10pm', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( '11pm', $option, false ) . ' value="11pm">' . esc_html__( '11pm', 'draft_concluder' ) . '</option>';
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
	echo '<option ' . selected( 'post', $option, false ) . ' value="post">' . esc_html__( 'Posts', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'page', $option, false ) . ' value="page">' . esc_html__( 'Pages', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'postpage', $option, false ) . ' value="post">' . esc_html__( 'Posts & Pages', 'draft_concluder' ) . '</option>';
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

	echo '<input name="draft_concluder_age" size="3" maxlength="3" type="text" value="' . esc_html( $option ) . '" />&nbsp;' . esc_html__( 'days', 'draft_concluder' );
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
	echo '<option ' . selected( 'created', $option, false ) . ' value="created">' . esc_html__( 'Since they were created', 'draft_concluder' ) . '</option>';
	echo '<option ' . selected( 'modified', $option, false ) . ' value="modified">' . esc_html__( 'Since they were last updated', 'draft_concluder' ) . '</option>';
	echo '</select>';
}
