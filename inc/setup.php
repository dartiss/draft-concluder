<?php
/**
 * Set-up functions
 * 
 * All the initial set-up functions, including plugin meta and scheduling the regular event
 *
 * @package draft-concluder
 */

/**
 * Add meta to plugin details
 *
 * Add options to plugin meta line
 *
 * @param    string $links  Current links.
 * @param    string $file   File in use.
 * @return   string         Links, now with settings added.
 */
function draft_concluder_plugin_meta( $links, $file ) {

	if ( false !== strpos( $file, 'draft-concluder.php' ) ) {

		$links = array_merge(
			$links,
			array( '<a href="https://github.com/dartiss/draft-concluder">' . __( 'Github', 'draft-concluder' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/draft-concluder">' . __( 'Support', 'draft-concluder' ) . '</a>' ),
			array( '<a href="https://artiss.blog/donate">' . __( 'Donate', 'draft-concluder' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/draft-concluder/reviews/#new-post">' . __( 'Write a Review', 'draft-concluder' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' ),
		);
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'draft_concluder_plugin_meta', 10, 2 );

/**
 * Modify actions links.
 *
 * Add or remove links for the actions listed against this plugin
 *
 * @param    string $actions      Current actions.
 * @param    string $plugin_file  The plugin.
 * @return   string               Actions, now with deactivation removed!
 */
function draft_concluder_action_links( $actions, $plugin_file ) {

	// Make sure we only perform actions for this specific plugin!
	if ( strpos( $plugin_file, 'draft-concluder.php' ) !== false ) {

		// If the appropriate constant is defined, remove the deactionation link.
		if ( defined( 'DO_NOT_DISABLE_MY_DRAFT_REMINDER' ) && true === DO_NOT_DISABLE_MY_DRAFT_REMINDER ) {
			unset( $actions['deactivate'] );
		}

		// Add link to the settings page.
		array_unshift( $actions, '<a href="' . admin_url() . 'options-general.php">' . __( 'Settings', 'draft-concluder' ) . '</a>' );

	}

	return $actions;
}

add_filter( 'plugin_action_links', 'draft_concluder_action_links', 10, 2 );

/**
 * Define scheduler
 *
 * If a schedule isn't already set up, set one up!
 * It will run daily at a time which can be adjusted.
 */
function draft_concluder_set_up_schedule() {

	// Get the time that the event needs scheduling for.
	// If one isn't specified, default to 1am!

	$time = get_option( 'draft_concluder_time' );
	if ( ! $time ) {
		$time = '1am';
	}

	draft_concluder_check_scheduled_time( $time );

	// Schedule an event if one doesn't already exist.

	if ( ! wp_next_scheduled( 'draft_concluder_mailer' ) && ! wp_installing() ) {
		wp_schedule_event( strtotime( 'tomorrow ' . $time ), 'daily', 'draft_concluder_mailer' );
	}
}

add_action( 'init', 'draft_concluder_set_up_schedule' );

/**
 * Checked scheduled time
 *
 * If the scheduled time has changed, remove it.
 *
 * @param string $time Current schedule time.
 */
function draft_concluder_check_scheduled_time( $time ) {

	// If we have an old time saved, check to see if it's changed.
	// If it has, remove the event and update the old one.

	$saved_time = get_option( 'draft_concluder_prev_time' );

	if ( false !== $saved_time && $time != $saved_time ) {
		wp_clear_scheduled_hook( 'draft_concluder_mailer' );
		update_option( 'draft_concluder_prev_time', $time );
	}
}

/**
 * Scheduler engine
 *
 * This is the function that runs when the daily scheduler rolls around
 */
function draft_concluder_schedule_engine() {

	// Grab the settings for how often to run this.
	// If it's not been set, assume weekly!

	$when = get_option( 'draft_concluder_when' );
	if ( ! $when ) {
		$when = 'Monday';
	}

	// Check to see if it should be run. If weekly, make sure it's a Sunday.

	if ( 'daily' == $when || ( 'daily' != $when && date( 'l' ) == $when ) ) {
		if ( 'daily' != $when ) {
			$daily = 'weekly';
		}
		draft_concluder_process_posts( $when );
	}

}

add_action( 'draft_concluder_mailer', 'draft_concluder_schedule_engine' );
